#!/usr/bin/env python3
"""
scraper_jcyl.py
Scraping de páginas de Educacyl (Junta de Castilla y León) relevantes
para el corpus RAG del chatbot.
Genera: corpus_jcyl.json  → lista de {pageContent, documento}
"""

import json
import time
import requests
from bs4 import BeautifulSoup

HEADERS = {
    "User-Agent": "Mozilla/5.0 (compatible; TFG-chatbot/1.0; research)"
}

URLS_JCYL = [
    {"url": "https://www.educa.jcyl.es/familias/es/matriculacion-web-junio", "documento": "Educacyl - Matriculación on-line familias"},
    {"url": "https://www.educa.jcyl.es/es/becasyayudas", "documento": "Educacyl - Becas, ayudas y subvenciones"},
    {"url": "https://www.educa.jcyl.es/es/becas_alumnado", "documento": "Educacyl - Alumnado no universitario"},
    {"url": "https://www.educa.jcyl.es/familias/es/matriculacion-web-junio-cc", "documento": "Educacyl - Matriculación on-line FAQ centros"},
    {"url": "https://www.educa.jcyl.es/es/calendario-escolar", "documento": "Educacyl - Calendario escolar"},
    {"url": "https://www.educa.jcyl.es/fr/ei-ep-bach-tva/matricula-centros-docentes-cursar-ensenanzas-sostenidas-fon", "documento": "Educacyl - Matrícula en centros docentes"},
]


def extraer_texto(url: str) -> str:
    """Descarga la página y devuelve el texto limpio del contenido principal."""
    try:
        resp = requests.get(url, headers=HEADERS, timeout=20)
        resp.raise_for_status()
    except requests.RequestException as e:
        print(f"  ⚠️  Error al descargar {url}: {e}")
        return ""

    soup = BeautifulSoup(resp.text, "html.parser")
    for tag in soup(["script", "style", "nav", "header", "footer",
                     "aside", "form", "noscript", "iframe", "button"]):
        tag.decompose()

    contenido = (
        soup.find("article") or
        soup.find("div", class_="contenido") or
        soup.find("div", id="contenidos") or
        soup.find("main") or
        soup.find("div", id="content") or
        soup.body
    )

    if not contenido:
        return ""

    lineas = []
    for elem in contenido.stripped_strings:
        linea = elem.strip()
        if len(linea) > 3:
            lineas.append(linea)

    return "\n".join(lineas)


def chunk_texto(texto: str, documento: str,
                max_chars: int = 800, overlap: int = 150) -> list[dict]:
    """Chunking con overlap para no perder contexto en los bordes."""
    parrafos = [p.strip() for p in texto.split("\n") if p.strip()]
    chunks = []
    buffer = []
    buffer_len = 0

    for parrafo in parrafos:
        if buffer_len + len(parrafo) > max_chars and buffer:
            chunk_text = "\n".join(buffer)
            chunks.append({"pageContent": chunk_text, "documento": documento})
            overlap_buffer = []
            overlap_len = 0
            for p in reversed(buffer):
                if overlap_len + len(p) <= overlap:
                    overlap_buffer.insert(0, p)
                    overlap_len += len(p)
                else:
                    break
            buffer = overlap_buffer
            buffer_len = overlap_len

        buffer.append(parrafo)
        buffer_len += len(parrafo)

    if buffer:
        chunks.append({"pageContent": "\n".join(buffer), "documento": documento})

    return chunks


def main():
    corpus = []
    print(f"Iniciando scraping de {len(URLS_JCYL)} páginas de Educacyl...\n")

    for entrada in URLS_JCYL:
        url = entrada["url"]
        documento = entrada["documento"]
        print(f"📄 {documento}")
        print(f"   {url}")

        texto = extraer_texto(url)

        if not texto:
            print(f"   ❌ Sin contenido extraído\n")
            continue

        chunks = chunk_texto(texto, documento)
        corpus.extend(chunks)
        print(f"   ✅ {len(chunks)} chunks generados ({len(texto)} chars)\n")

        time.sleep(1)

    output_path = "corpus_jcyl.json"
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(corpus, f, ensure_ascii=False, indent=2)

    print("=" * 50)
    print(f"✅ Corpus JCYL generado: {len(corpus)} chunks totales")
    print(f"📁 Guardado en: {output_path}")
    print("=" * 50)


if __name__ == "__main__":
    main()
