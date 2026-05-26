#!/usr/bin/env python3
"""
ingest_qdrant.py
Ingesta de uno o varios ficheros JSON de corpus en Qdrant usando embeddings de Ollama.

Uso:
    python3 ingest_qdrant.py corpus_ies.json corpus_jcyl.json
    # o, sin parámetros, usa corpus_ies.json por defecto
"""

import json
import sys
import time
from typing import List

import requests
from qdrant_client import QdrantClient
from qdrant_client.models import VectorParams, Distance, PointStruct

OLLAMA_URL = "http://localhost:11434"
EMBED_MODEL = "nomic-embed-text"

QDRANT_HOST = "localhost"
QDRANT_PORT = 6333
COLLECTION = "corpus_centro"
VECTOR_SIZE = 768
DISTANCE = Distance.COSINE


def get_embedding(texto: str) -> list[float]:
    """Obtiene el embedding de un texto usando Ollama."""
    resp = requests.post(
        f"{OLLAMA_URL}/api/embeddings",
        json={"model": EMBED_MODEL, "prompt": texto},
        timeout=60,
    )
    resp.raise_for_status()
    data = resp.json()
    return data["embedding"]


def cargar_corpus(rutas: List[str]) -> list[dict]:
    """Carga y concatena varios ficheros JSON de corpus."""
    corpus_total: list[dict] = []
    for ruta in rutas:
        print(f"📂 Cargando corpus desde {ruta}...")
        with open(ruta, "r", encoding="utf-8") as f:
            datos = json.load(f)
            print(f"   ✅ {len(datos)} chunks leídos")
            corpus_total.extend(datos)
    print(f"\n📦 Corpus combinado: {len(corpus_total)} chunks totales\n")
    return corpus_total


def clasificar_metadata(documento: str, texto: str) -> dict:
    base = f"{documento or ''} {texto or ''}".lower()
    doc = documento.lower()

    # Clasificación por nombre de documento (más fiable que por contenido)
    if "oferta formativa" in doc or "ciclos formativos" in doc or "cursos de especialización" in doc:
        tipo = "oferta"
    elif "matrícula" in doc or "matricula" in doc or "matriculación" in doc:
        tipo = "matricula"
    elif "admisión" in doc or "admision" in doc:
        tipo = "admision"
    elif "calendario" in doc:
        tipo = "calendario"
    # Clasificación por contenido como fallback
    elif "matriculación" in base or "matricula" in base or "matrícula" in base:
        tipo = "matricula"
    elif "admisión" in base or "admision" in base:
        tipo = "admision"
    elif "calendario" in base:
        tipo = "calendario"
    elif "oferta" in base or "ciclos formativos" in base or "grado medio" in base or "grado superior" in base:
        tipo = "oferta"
    else:
        tipo = "general"

    etapa = "general"
    if "bachillerato" in base:
        etapa = "bachillerato"
    elif (
        "formación profesional" in base
        or "formacion profesional" in base
        or "ciclos formativos" in base
        or "fp" in base
    ):
        etapa = "fp"
    elif "eso" in base:
        etapa = "eso"

    curso = "desconocido"
    if "2025/2026" in base or "25/26" in base:
        curso = "2025/2026"
    elif "2026/2027" in base or "26/27" in base:
        curso = "2026/2027"

    origen = "jcyl" if any(
        x in documento.lower() for x in ["educacyl", "jcyl", "junta"]
    ) else "ies"

    return {
        "tipo": tipo,
        "etapa": etapa,
        "curso": curso,
        "origen": origen,
    }


def recrear_coleccion(client: QdrantClient):
    """Elimina y recrea la colección en Qdrant con la configuración deseada."""
    collections = client.get_collections().collections
    nombres = {c.name for c in collections}

    if COLLECTION in nombres:
        print(f"ℹ️  La colección '{COLLECTION}' ya existe. Se eliminará y recreará...")
        client.delete_collection(COLLECTION)

    print(f"🛠  Creando colección '{COLLECTION}' en Qdrant...")
    client.recreate_collection(
        collection_name=COLLECTION,
        vectors_config=VectorParams(size=VECTOR_SIZE, distance=DISTANCE),
    )
    print("   ✅ Colección creada\n")


def main():
    rutas = sys.argv[1:]
    if not rutas:
        rutas = ["corpus_ies.json"]
        print(
            "ℹ️  No se han pasado rutas por parámetro. Usando corpus_ies.json por defecto.\n"
        )

    corpus = cargar_corpus(rutas)

    client = QdrantClient(host=QDRANT_HOST, port=QDRANT_PORT)

    recrear_coleccion(client)

    puntos: list[PointStruct] = []
    total = len(corpus)
    print(
        f"🚀 Iniciando generación de embeddings e ingesta en Qdrant ({total} chunks)...\n"
    )

    for idx, item in enumerate(corpus):
        texto = item.get("pageContent", "")
        documento = item.get("documento", "desconocido")
        metadata = clasificar_metadata(documento, texto)

        if not texto.strip():
            continue

        try:
            embedding = get_embedding(texto)
        except Exception as e:
            print(f"  ⚠️  Error al obtener embedding del chunk {idx}: {e}")
            continue

        puntos.append(
            PointStruct(
                id=idx,
                vector=embedding,
                payload={
                    "pageContent": texto,
                    "documento": documento,
                    "tipo": metadata["tipo"],
                    "etapa": metadata["etapa"],
                    "curso": metadata["curso"],
                    "origen": metadata["origen"],
                },
            )
        )

        if len(puntos) % 10 == 0 or idx == total - 1:
            client.upsert(collection_name=COLLECTION, points=puntos)
            print(f"   ✅ Chunks 0–{idx} subidos ({len(puntos)} puntos en este lote)")
            puntos = []
            time.sleep(0.2)

    print(
        f"\n✅ Ingesta completada: {total}/{total} chunks procesados (verificar recuento en Qdrant).\n"
    )


if __name__ == "__main__":
    main()
