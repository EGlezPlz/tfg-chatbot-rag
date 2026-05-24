from qdrant_client import QdrantClient
import pandas as pd
import json
from pathlib import Path

QDRANT_URL = "http://localhost:6333"
COLLECTION = "corpus_centro"
OUT_DIR = Path("output")
OUT_DIR.mkdir(exist_ok=True)

client = QdrantClient(url=QDRANT_URL)

all_rows = []
offset = None

while True:
    points, next_offset = client.scroll(
        collection_name=COLLECTION,
        limit=100,
        offset=offset,
        with_payload=True,
        with_vectors=False
    )

    if not points:
        break

    for p in points:
        payload = p.payload or {}
        all_rows.append({
            "id": str(p.id),
            "documento": payload.get("documento", ""),
            "pageContent": payload.get("pageContent", ""),
            "tipo": payload.get("tipo", ""),
            "etapa": payload.get("etapa", ""),
            "curso": payload.get("curso", "")
        })

    if next_offset is None:
        break
    offset = next_offset

df = pd.DataFrame(all_rows)

csv_path = OUT_DIR / "corpus_centro.csv"
json_path = OUT_DIR / "corpus_centro.json"

df.to_csv(csv_path, index=False, encoding="utf-8-sig")
with open(json_path, "w", encoding="utf-8") as f:
    json.dump(all_rows, f, ensure_ascii=False, indent=2)

print(f"Exported {len(all_rows)} rows")
print(csv_path)
print(json_path)
