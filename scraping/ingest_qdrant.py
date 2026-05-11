#!/usr/bin/env python3
import json
import uuid
import requests

OLLAMA_URL = "http://localhost:11434"
QDRANT_URL = "http://localhost:6333"
COLLECTION  = "corpus_centro"
EMBED_MODEL = "nomic-embed-text:latest"
CORPUS_FILE = "corpus_ies.json"
BATCH_SIZE  = 10

def get_embedding(text):
    resp = requests.post(
        f"{OLLAMA_URL}/api/embeddings",
        json={"model": EMBED_MODEL, "prompt": text},
        timeout=60
    )
    resp.raise_for_status()
    return resp.json()["embedding"]

def upsert_batch(points):
    resp = requests.put(
        f"{QDRANT_URL}/collections/{COLLECTION}/points",
        json={"points": points},
        headers={"Content-Type": "application/json"},
        timeout=30
    )
    resp.raise_for_status()

def main():
    with open(CORPUS_FILE, "r", encoding="utf-8") as f:
        corpus = json.load(f)

    print(f"📂 Corpus cargado: {len(corpus)} chunks\n")
    batch = []
    errores = 0

    for i, item in enumerate(corpus):
        try:
            vector = get_embedding(item["pageContent"])
        except Exception as e:
            print(f"  ⚠️  Chunk {i} — error embedding: {e}")
            errores += 1
            continue

        batch.append({
            "id":      str(uuid.uuid4()),
            "vector":  vector,
            "payload": {"pageContent": item["pageContent"], "documento": item["documento"]}
        })

        if len(batch) >= BATCH_SIZE:
            upsert_batch(batch)
            print(f"  ✅ Chunks {i - BATCH_SIZE + 1}–{i} subidos")
            batch = []

    if batch:
        upsert_batch(batch)
        print(f"  ✅ Últimos {len(batch)} chunks subidos")

    print(f"\n✅ Ingesta completada: {len(corpus) - errores}/{len(corpus)} chunks indexados")

if __name__ == "__main__":
    main()
