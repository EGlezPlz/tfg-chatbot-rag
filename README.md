# tfg-chatbot-rag

Chatbot conversacional con RAG (Retrieval-Augmented Generation) local, desarrollado 
como Trabajo de Fin de Grado del Ciclo Formativo de Grado Superior en Desarrollo de 
Aplicaciones Multiplataforma (DAM) en el IES Venancio Blanco, Salamanca. Curso 2025/2026.

**Autora:** Elena González Plaza  
**Tutor:** Alejandro Pérez-Moneo Nieto

---

## Descripción

Sistema de atención automatizada mediante un chatbot conversacional que procesa toda 
la información de forma local, sin dependencia de servicios externos de IA. Orientado 
inicialmente al IES Venancio Blanco, con arquitectura modular adaptable a cualquier 
entorno educativo o empresarial.

El sistema responde consultas sobre el centro (horarios, ciclos formativos, matrícula, 
becas) utilizando un corpus documental extraído mediante scraping del sitio web del 
centro y del portal Educacyl de la Junta de Castilla y León.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Frontend | Vue 3 + Vite + Vue Router |
| Backend | Python 3.12 / FastAPI + Uvicorn |
| Orquestación RAG | n8n (workflow agéntico con Simple Memory) |
| Modelo LLM | llama3.2 (local, vía Ollama) |
| Modelo embeddings | nomic-embed-text (local, vía Ollama) |
| Base vectorial | Qdrant |
| Base de datos | PostgreSQL 16 (interno a n8n) |
| Contenedores | Docker + Docker Compose |
| Administración | Arcane |

---

## Requisitos del sistema

- Docker y Docker Compose instalados
- Python 3.12+
- Node.js 24+
- Mínimo 8 GB RAM (16 GB recomendado)
- Sin GPU dedicada necesaria (modo CPU-only)

---

## Estructura del proyecto

tfg-chatbot-rag/
├── arranque.sh                  # Script de inicialización de red y volúmenes
├── docker-compose.global.yml    # Despliegue del backend FastAPI
├── fastapi-backend/             # Backend Python/FastAPI
│   ├── app/main.py
│   ├── Dockerfile
│   └── requirements.txt
├── infra/
│   ├── n8n/                     # Orquestador RAG + PostgreSQL
│   │   ├── docker-compose.yaml
│   │   └── workflows/           # Workflow exportado de n8n
│   ├── Ollama/                  # Modelos LLM locales
│   │   └── docker-compose.yaml
│   └── qdrant/                  # Base vectorial
│       └── docker-compose.yaml
├── scraping/                    # Scripts de generación del corpus
│   ├── scraper_ies.py
│   ├── scraper_jcyl.py
│   ├── ingest_qdrant.py
│   ├── corpus_ies.json
│   └── corpus_jcyl.json
├── Arcane/                      # Administración de contenedores
│   └── docker-compose.yaml
└── web-insti/
└── web-insti/               # Frontend Vue 3
├── src/
├── Dockerfile
└── vite.config.js

---

## Despliegue

### 1. Clonar el repositorio

```bash
git clone https://github.com/EGlezPlz/tfg-chatbot-rag.git
cd tfg-chatbot-rag
```

### 2. Configurar variables de entorno

**Archivo 1 — raíz del proyecto:**
```bash
cp .env.example .env
# Rellenar POSTGRES_PASSWORD, N8N_ENCRYPTION_KEY y N8N_USER_MANAGEMENT_JWT_SECRET
# Para generar claves: openssl rand -hex 32
```

**Archivo 2 — frontend:**
```bash
cp web-insti/web-insti/.env.example web-insti/web-insti/.env
# Establecer VITE_APP_NAME=web-insti y VITE_API_BASE_URL=http://localhost:8001
```

### 3. Inicializar red y volúmenes Docker

```bash
chmod +x arranque.sh
./arranque.sh
```

### 4. Levantar los servicios

```bash
# Base vectorial
cd infra/qdrant && docker compose up -d && cd ../..

# Ollama + Open WebUI
cd infra/Ollama && docker compose up -d && cd ../..

# Descargar modelos (esperar a que Ollama esté arriba)
docker exec -it ollama-ollama-1 ollama pull llama3.2
docker exec -it ollama-ollama-1 ollama pull nomic-embed-text

# n8n + PostgreSQL
cd infra/n8n && docker compose up -d && cd ../..

# Backend FastAPI
docker compose -f docker-compose.global.yml up -d

# Frontend (modo desarrollo)
cd web-insti/web-insti
npm install
npm run dev

# Arcane — administración de contenedores (opcional)
cd ../../Arcane && docker compose up -d && cd ..
```

### 5. Importar el workflow de n8n

1. Acceder a `http://localhost:5678`
2. Crear cuenta o iniciar sesión
3. Ir a **Workflows → Import from file**
4. Seleccionar `infra/n8n/workflows/TFG RAG - v2 Agentic n8n.json`
5. Activar el workflow

### 6. Generar e ingestar el corpus

El corpus documental (`corpus_ies.json` y `corpus_jcyl.json`) está incluido en el 
repositorio como ejemplo de datos. En un nuevo despliegue puede regenerarse 
ejecutando los scrapers:

```bash
cd scraping
python3 -m venv venv
source venv/bin/activate        # Linux/Mac
# En Windows: venv\Scripts\activate
pip install qdrant-client requests beautifulsoup4
python3 scraper_ies.py
python3 scraper_jcyl.py
python3 ingest_qdrant.py corpus_ies.json corpus_jcyl.json
deactivate
```

---

## Servicios y puertos

| Servicio | URL local | Descripción |
|---|---|---|
| Frontend (dev) | http://localhost:5173 | Interfaz del chatbot |
| Backend FastAPI | http://localhost:8001 | API REST + /docs |
| n8n | http://localhost:5678 | Orquestador RAG |
| Qdrant dashboard | http://localhost:6333/dashboard | Base vectorial |
| Ollama API | http://localhost:11434 | Modelos LLM locales |
| Open WebUI | http://localhost:3000 | Interfaz para Ollama (opcional) |
| Arcane | http://localhost:3552 | Administración Docker |

---

## Licencia

Esta obra está bajo una licencia 
[Creative Commons Reconocimiento-CompartirIgual 3.0 España](http://creativecommons.org/licenses/by-sa/3.0/es/).
