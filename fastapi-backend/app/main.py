from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
from datetime import datetime
import uuid
import requests
import logging
import time
from qdrant_client import QdrantClient

app = FastAPI(title="TFG Fase 2 - Backend FastAPI")

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:5173",
        "http://127.0.0.1:5173"
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

logging.basicConfig(level=logging.INFO, format="%(levelname)s: %(message)s")
logger = logging.getLogger("tfg-logs")

OLLAMA_URL = "http://172.17.0.1:11434"
EMBED_MODEL = "nomic-embed-text"
CHAT_MODEL = "llama3.2"
QDRANT_HOST = "qdrant"
QDRANT_PORT = 6333
COLLECTION = "corpus_centro"


class Consulta(BaseModel):
    session_id: Optional[str] = None
    pregunta: str


class Fuente(BaseModel):
    titulo: str
    url: str

class Respuesta(BaseModel):
    session_id: str
    estado: str
    respuesta: str
    fuentes: List[Fuente]
    timestamp: str
    latencia_ms: int


def get_embedding(text: str):
    r = requests.post(
        f"{OLLAMA_URL}/api/embeddings",
        json={"model": EMBED_MODEL, "prompt": text},
        timeout=60,
    )
    r.raise_for_status()
    return r.json()["embedding"]


def search_qdrant(embedding, limit=4):
    client = QdrantClient(host=QDRANT_HOST, port=QDRANT_PORT)
    results = client.search(
        collection_name=COLLECTION,
        query_vector=embedding,
        limit=limit,
        with_payload=True,
    )
    return results


def build_context_and_sources(results):
    partes = []
    fuentes = []
    vistos = set()

    for r in results:
        payload = r.payload or {}
        texto = payload.get("pageContent", "") or payload.get("texto", "")
        if texto:
            partes.append(texto)

        titulo = payload.get("title") or payload.get("titulo") or payload.get("source", "corpus_centro")
        url = payload.get("url") or payload.get("link") or ""
        key = (titulo, url)

        if key not in vistos:
            fuentes.append(Fuente(titulo=titulo, url=url))
            vistos.add(key)

    contexto = "\n\n".join(partes)
    return contexto, fuentes


def ask_ollama(pregunta: str, contexto: str):
    prompt = f"""Eres un asistente del IES Venancio Blanco. Responde SOLO usando el contexto proporcionado. Si no encuentras la respuesta, dilo claramente y de forma breve.

Contexto:
{contexto}

Pregunta: {pregunta}
Respuesta:"""

    r = requests.post(
        f"{OLLAMA_URL}/api/generate",
        json={"model": CHAT_MODEL, "prompt": prompt, "stream": False},
        timeout=120,
    )
    r.raise_for_status()
    return r.json()["response"]


@app.get("/health")
async def health():
    return {
        "status": "ok",
        "service": "backend",
        "version": "v0.4-chat-frontend"
    }


@app.get("/status")
async def status():
    estado = {
        "backend": "OK",
        "qdrant": "KO",
        "ollama": "KO",
        "version": "v0.4-chat-frontend"
    }

    try:
        client = QdrantClient(host=QDRANT_HOST, port=QDRANT_PORT)
        client.get_collections()
        estado["qdrant"] = "OK"
    except Exception:
        estado["qdrant"] = "KO"

    try:
        r = requests.get(f"{OLLAMA_URL}/api/tags", timeout=3)
        estado["ollama"] = "OK" if r.status_code == 200 else "KO"
    except Exception:
        estado["ollama"] = "KO"

    return estado


@app.post("/chat", response_model=Respuesta)
async def chat(consulta: Consulta):
    inicio = time.monotonic()
    session_id = consulta.session_id or str(uuid.uuid4())

    try:
        embedding = get_embedding(consulta.pregunta)
        resultados = search_qdrant(embedding)
        contexto, fuentes = build_context_and_sources(resultados)

        if contexto.strip():
            estado = "EXITO"
            respuesta = ask_ollama(consulta.pregunta, contexto)
        else:
            estado = "SIN_CONTEXTO"
            respuesta = (
                "No he encontrado información suficiente en el corpus para responder con seguridad."
            )

        lat_ms = int((time.monotonic() - inicio) * 1000)

        logger.info(
            f"consulta_procesada timestamp={datetime.now().isoformat()} "
            f"sessionId={session_id} latenciatotalms={lat_ms} resultado={estado}"
        )

        return Respuesta(
            session_id=session_id,
            estado=estado,
            respuesta=respuesta,
            fuentes=fuentes,
            timestamp=datetime.now().isoformat(),
            latencia_ms=lat_ms,
        )

    except Exception as e:
        lat_ms = int((time.monotonic() - inicio) * 1000)

        logger.error(
            f"consulta_error timestamp={datetime.now().isoformat()} "
            f"sessionId={session_id} latenciatotalms={lat_ms} resultado=ERROR"
        )

        return Respuesta(
            session_id=session_id,
            estado="ERROR",
            respuesta=f"Error en RAG: {str(e)}",
            fuentes=[],
            timestamp=datetime.now().isoformat(),
            latencia_ms=lat_ms,
        )
