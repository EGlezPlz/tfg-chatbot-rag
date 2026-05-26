#!/usr/bin/env python3
"""
scraper_ies.py
Scraping del IES Venancio Blanco para corpus RAG.
Genera: corpus_ies.json  → lista de {pageContent, documento}
"""

import io
import json
import time
import requests
from bs4 import BeautifulSoup

HEADERS = {
    "User-Agent": "Mozilla/5.0 (compatible; TFG-chatbot/1.0; research)"
}

URLS = [
    {"url": "https://iesvenancioblanco.es/secretaria-horario-y-atencion-al-publico/", "documento": "Secretaría - Horario y atención al público"},
    {"url": "https://iesvenancioblanco.es/category/secretaria/", "documento": "Secretaría - Avisos y noticias"},
    {"url": "https://iesvenancioblanco.es/admision-y-matricula-eso-y-bachillerato/", "documento": "Admisión y matrícula - ESO y Bachillerato"},
    {"url": "https://iesvenancioblanco.es/admision-y-matricula-formacion-profesional/", "documento": "Admisión y matrícula - Formación Profesional"},
    {"url": "https://iesvenancioblanco.es/admision-y-matricula-fp-virtual-curso-2025-2026/", "documento": "Admisión y matrícula - FP Virtual 2025-2026"},
    {"url": "https://iesvenancioblanco.es/matricula-curso-2025-26-atencion-a-las-fechas/", "documento": "Matrícula curso 2025-26 - Fechas importantes"},
    {"url": "https://iesvenancioblanco.es/ciclos-formativos-de-grado-superior/", "documento": "Oferta formativa - Ciclos Formativos Grado Superior"},
    {"url": "https://iesvenancioblanco.es/ciclos-formativos-de-grado-medio/", "documento": "Oferta formativa - Ciclos Formativos Grado Medio"},
    {"url": "https://iesvenancioblanco.es/cursos-de-especializacion-de-fp/", "documento": "Oferta formativa - Cursos de Especialización FP"},
    {"url": "https://iesvenancioblanco.es/horario-y-contacto/", "documento": "Secretaría - Horario y contacto"},
    {"url": "https://iesvenancioblanco.es/horarios/", "documento": "Horarios generales del centro"},
    {"url": "https://iesvenancioblanco.es/solicitud-de-titulos/", "documento": "Secretaría - Solicitud de títulos"},
    {"url": "https://iesvenancioblanco.es/becas-y-ayudas-a-alumnos-de-niveles-postobligatorios-curso-2026-2027/", "documento": "Becas y ayudas postobligatorias 2026-2027"},
    {"url": "https://iesvenancioblanco.es/releo-plus-y-otras-becas/", "documento": "RELEO PLUS y otras becas"},
    {"url": "https://iesvenancioblanco.es/ayudas-para-la-adquisicion-de-dispositivos-digitales-2025-2026-convocatoria/", "documento": "Ayudas dispositivos digitales 2025-2026"},
    {"url": "https://iesvenancioblanco.es/tag/becas/", "documento": "Listado noticias becas"},
    {"url": "https://www.iesvenancioblanco.es/index.php/17-convivencia/109-reglamento-de-regimen-interior", "documento": "Convivencia - Reglamento de Régimen Interior"},
    {"url": "https://iesvenancioblanco.es/consejo-escolar/", "documento": "Convivencia - Consejo escolar"},
    {"url": "https://www.iesvenancioblanco.es/index.php/conocenos/organizacion/consejo-escolar", "documento": "Convivencia - Consejo escolar (detalle)"},
    {"url": "https://iesvenancioblanco.es/orientacion/", "documento": "Orientación"},
    {"url": "https://iesvenancioblanco.es/tutores/", "documento": "Tutorías"},
    {"url": "https://iesvenancioblanco.es/ensenanza-secundaria-obligatoria-eso/", "documento": "ESO - información académica"},
    {"url": "https://iesvenancioblanco.es/index.php/informacion-academica/evaluaciones/normativa", "documento": "Evaluación, promoción y titulación"},
    {"url": "https://iesvenancioblanco.es/wp-content/uploads/2025/11/Propuesta-Curricular_ESO_25-26_Venancio-Blanco.pdf", "documento": "Propuesta Curricular ESO 2025-26"},
    {"url": "https://iesvenancioblanco.es/wp-content/uploads/2025/11/Propuesta-Curricular_Bach_25-26_Venancio-Blanco.pdf", "documento": "Propuesta Curricular Bachillerato 2025-26"},
    {"url": "https://iesvenancioblanco.es/wp-content/uploads/2025/09/RRI_2025.pdf", "documento": "Reglamento de Régimen Interior"},
]


def extraer_texto_pdf(contenido_bytes: bytes) -> str:
    """Extrae texto de un PDF dado como bytes."""
    import pdfplumber
    texto_total = []
    try:
        with pdfplumber.open(io.BytesIO(contenido_bytes)) as pdf:
            for pagina in pdf.pages:
                texto = pagina.extract_text()
                if texto:
                    for linea in texto.split("\n"):
                        linea = linea.strip()
                        if len(linea) > 3:
                            texto_total.append(linea)
    except Exception as e:
        print(f"  ⚠️  Error al extraer PDF: {e}")
    return "\n".join(texto_total)


def extraer_texto(url: str) -> str:
    """Descarga la página o PDF y devuelve el texto limpio."""
    try:
        resp = requests.get(url, headers=HEADERS, timeout=30)
        resp.raise_for_status()
    except requests.RequestException as e:
        print(f"  ⚠️  Error al descargar {url}: {e}")
        return ""

    content_type = resp.headers.get("Content-Type", "")
    if "pdf" in content_type or url.endswith(".pdf"):
        return extraer_texto_pdf(resp.content)

    soup = BeautifulSoup(resp.text, "html.parser")
    for tag in soup(["script", "style", "nav", "header", "footer",
                     "aside", "form", "noscript", "iframe", "button"]):
        tag.decompose()

    contenido = (
        soup.find("article") or
        soup.find("div", class_="entry-content") or
        soup.find("div", class_="post-content") or
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
    print(f"Iniciando scraping de {len(URLS)} páginas...\n")

    for entrada in URLS:
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

    output_path = "corpus_ies.json"
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(corpus, f, ensure_ascii=False, indent=2)

    print(f"{'='*50}")
    print(f"✅ Corpus generado: {len(corpus)} chunks totales")
    print(f"📁 Guardado en: {output_path}")
    print(f"{'='*50}")


if __name__ == "__main__":
    main()
