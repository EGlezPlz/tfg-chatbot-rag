from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional
from datetime import datetime
import uuid
import requests
from qdrant_client import QdrantClient

app = FastAPI(title="TFG Fase 2 - Backend FastAPI")

# Configuraciones
OLLAMA_URL = "http://ollama:11434"
EMBED_MODEL = "nomic-embed-text"
CHAT_MODEL = "llama3.2"
QDRANT_HOST = "qdrant"
QDRANT_PORT = 6333
COLLECTION = "corpus_centro"

class Consulta(BaseModel):
    session_id: Optional[str] = None
    pregunta: str

class Respuesta(BaseModel):
    session_id: str
    respuesta: str
    fuente: str
    timestamp: str

def get_embedding(text: str):
    r = requests.post(f"{OLLAMA_URL}/api/embeddings", json={"model": EMBED_MODEL, "prompt": text})
    return r.json()["embedding"]

def search_qdrant(embedding, limit=4):
    client = QdrantClient(host=QDRANT_HOST, port=QDRANT_PORT)
    results = client.search(collection_name=COLLECTION, query_vector=embedding, limit=limit, with_payload=True)
    return results

def ask_ollama(pregunta: str, contexto: str):
    prompt = f"""Eres un asistente del IES Venancio Blanco. Responde SOLO usando el contexto proporcionado. Si no encuentras la respuesta, dilo claramente.

Contexto:
{contexto}

Pregunta: {pregunta}
Respuesta:"""
    r = requests.post(f"{OLLAMA_URL}/api/generate", json={"model": CHAT_MODEL, "prompt": prompt, "stream": False})
    return r.json()["response"]

@app.post("/chat", response_model=Respuesta)
async def chat(consulta: Consulta):
    session_id = consulta.session_id or str(uuid.uuid4())
    try:
        embedding = get_embedding(consulta.pregunta)
        resultados = search_qdrant(embedding)
        contexto = "\n\n".join([r.payload.get("pageContent", "") for r in resultados])
        fuentes = list(set([r.payload.get("source", "corpus_centro") for r in resultados]))
        respuesta = ask_ollama(consulta.pregunta, contexto)
        return Respuesta(
            session_id=session_id, 
            respuesta=respuesta, 
            fuente=fuentes[0] if fuentes else "corpus_centro", 
            timestamp=datetime.now().isoformat()
        )
    except Exception as e:
        return Respuesta(
            session_id=session_id, 
            respuesta=f"Error en RAG: {str(e)}", 
            fuente="error", 
            timestamp=datetime.now().isoformat()
        )
