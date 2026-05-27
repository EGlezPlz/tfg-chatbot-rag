# tfg-chatbot-rag

Chatbot conversacional con RAG (Retrieval-Augmented Generation) local, desarrollado 
como Trabajo de Fin de Grado del Ciclo Formativo de Grado Superior en Desarrollo de 
Aplicaciones Multiplataforma (DAM) en el IES Venancio Blanco, Salamanca. Curso 2025/2026.

**Autora:** Elena González Plaza  
**Tutor:** Alejandro Pérez-Moneo Nieto  
**Repositorio:** https://github.com/EGlezPlz/tfg-chatbot-rag

---

## Descripción

Sistema de atención automatizada mediante un chatbot conversacional que procesa toda 
la información de forma local, sin dependencia de servicios externos de IA. Orientado 
inicialmente al IES Venancio Blanco, con arquitectura modular adaptable a cualquier 
entorno educativo o empresarial.

El sistema responde consultas sobre el centro (horarios, ciclos formativos, matrícula, 
becas, normas de convivencia) utilizando un corpus documental de 380 chunks extraído 
mediante scraping del sitio web del centro y del portal Educacyl de la Junta de 
Castilla y León.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Frontend | Vue 3 + Vite + Vue Router (servido con nginx en Docker) |
| Backend | Python 3.12 / FastAPI + Uvicorn |
| Orquestación RAG | n8n 2.20.6 (workflow agéntico con Simple Memory) |
| Modelo LLM | llama3.2:latest (local, vía Ollama) |
| Modelo embeddings | nomic-embed-text:latest (local, vía Ollama) |
| Base vectorial | Qdrant v1.17.0 |
| Base de datos | PostgreSQL 16 (interno a n8n) |
| Contenedores | Docker + Docker Compose |
| Administración | Arcane |

---

## Requisitos del sistema

- Docker Engine y Docker Compose v2.x instalados
- Mínimo 8 núcleos CPU, 16 GB RAM, 256 GB SSD
- Sin GPU dedicada necesaria (modo CPU-only; latencia ~8-20 s en régimen estable)
- Conexión a Internet solo para la descarga inicial de imágenes y modelos

---

## Estructura del proyecto

<details>
<summary><strong>Estructura del proyecto</strong></summary>

- `tfg-chatbot-rag/`
  - `arranque.sh` — Crea la red externa `red_ia` si no existe
  - `docker-compose.global.yml` — Compose único, punto de entrada principal
  - `fastapi-backend/` — Backend Python/FastAPI
    - `app/`
      - `main.py`
    - `Dockerfile`
    - `requirements.txt`
  - `infra/`
    - `n8n/` — Orquestador RAG + PostgreSQL
      - `.env.example` — Plantilla de variables de entorno
      - `workflows/` — Workflow exportado de n8n
    - `Ollama/` — Modelos LLM locales
      - `docker-compose.yaml`
    - `qdrant/` — Base vectorial
      - `qdrant_storage/` — Datos persistentes (ignorado en git)
  - `scraping/` — Scripts de generación del corpus
    - `scraper_ies.py`
    - `scraper_jcyl.py`
    - `ingest_qdrant.py`
    - `corpus_ies.json`
    - `corpus_jcyl.json`
  - `web-insti/web-insti/` — Frontend Vue 3
    - `src/`
    - `Dockerfile` — Build con pnpm + nginx
    - `nginx.conf`
    - `pnpm-lock.yaml`
    - `vite.config.js`

</details>

---

## Despliegue rápido

### 1. Clonar el repositorio

```bash
git clone https://github.com/EGlezPlz/tfg-chatbot-rag.git
cd tfg-chatbot-rag
```

### 2. Configurar variables de entorno de n8n

```bash
cp infra/n8n/.env.example infra/n8n/.env
```

Edita `infra/n8n/.env` y establece al menos:
- `POSTGRES_USER` — usuario de PostgreSQL
- `POSTGRES_PASSWORD` — contraseña de PostgreSQL
- `POSTGRES_DB` — nombre de la base de datos
- `N8N_ENCRYPTION_KEY` — clave de cifrado de credenciales (genera con `openssl rand -hex 32`)
- `N8N_USER_MANAGEMENT_JWT_SECRET` — secreto JWT (genera con `openssl rand -hex 32`)

Copia también el `.env` a la raíz para que el compose resuelva las variables:

```bash
cp infra/n8n/.env .env
```

> ⚠️ Ninguno de estos ficheros va al repositorio. El `.gitignore` ya los excluye.

### 3. Crear la red Docker externa

```bash
chmod +x arranque.sh
./arranque.sh
```

### 4. Levantar todos los servicios

```bash
docker compose -f docker-compose.global.yml up -d
```

Esto levanta en un único comando: **FastAPI · Frontend Vue · Qdrant · PostgreSQL · n8n · Ollama · Arcane**

### 5. Descargar los modelos en Ollama *(solo la primera vez)*

```bash
docker exec ollama ollama pull llama3.2
docker exec ollama ollama pull nomic-embed-text
```

La descarga puede tardar varios minutos según la conexión.

### 6. Ingestar el corpus

```bash
cd scraping
source venv/bin/activate
python ingest_qdrant.py corpus_ies.json corpus_jcyl.json
deactivate
cd ..
```

> Si no tienes el venv creado:
> ```bash
> cd scraping
> python3 -m venv venv
> source venv/bin/activate
> pip install qdrant-client requests beautifulsoup4 pdfplumber
> python ingest_qdrant.py corpus_ies.json corpus_jcyl.json
> ```

### 7. Importar el workflow en n8n

1. Acceder a `http://localhost:5678`
2. Iniciar sesión con las credenciales del `.env`
3. Ir a **Settings → Import** y cargar:  
   `infra/n8n/workflows/TFG RAG - v2 Agentic n8n.json`
4. Activar el workflow con el toggle superior derecho
5. Ir a **Settings → Credentials** y crear una credencial de tipo **Ollama**  
   apuntando a `http://172.17.0.1:11434`

### 8. Verificar el despliegue

```bash
# Todos los contenedores deben aparecer como Up
docker ps --format "table {{.Names}}\t{{.Status}}"

# Backend
curl http://localhost:8001/health

# Corpus
curl -s http://localhost:6333/collections/corpus_centro | python3 -m json.tool | grep vectors_count
```

El chatbot estará disponible en **`http://localhost:3000`**

---

## Servicios y puertos

| Servicio | URL | Descripción |
|---|---|---|
| **Frontend VenancIA** | http://localhost:3000 | Interfaz del chatbot |
| **Backend FastAPI** | http://localhost:8001 | API REST (`/health`, `/status`, `/chat`) |
| **n8n** | http://localhost:5678 | Orquestador RAG y workflow agéntico |
| **Qdrant dashboard** | http://localhost:6333/dashboard | Base vectorial |
| **Ollama API** | http://localhost:11434 | Modelos LLM locales |
| **Arcane** | http://localhost:3552 | Administración de contenedores Docker |

---

## Versiones fijadas

| Componente | Versión |
|---|---|
| n8n | 2.20.6 |
| Qdrant | 1.17.0 |
| PostgreSQL | 16-alpine |
| Node.js (build frontend) | 20-alpine |

> **Nota de compatibilidad:** el workflow de n8n usa nodos de LangChain integrados 
> (`AI Agent`, `Ollama Chat Model`, `Simple Memory`) disponibles a partir de n8n 1.30. 
> La imagen del compose está fijada a la versión 2.20.6 con la que fue desarrollado 
> para garantizar compatibilidad total.

---

## Desarrollo local (sin Docker)

> ⚠️ **Gestor de paquetes del frontend:** este proyecto usa **pnpm** en lugar de npm 
> por razones de seguridad — pnpm no ejecuta scripts de postinstalación 
> automáticamente, eliminando un vector de ataque conocido en paquetes comprometidos.

```bash
# Frontend
cd web-insti/web-insti
pnpm install
pnpm run dev          # http://localhost:5173

# Backend
cd fastapi-backend
pip install -r requirements.txt
uvicorn app.main:app --reload --port 8001

# Scrapers
cd scraping
source venv/bin/activate
python scraper_ies.py
python scraper_jcyl.py
```

> **¿No tienes pnpm?**
> ```bash
> # Con permisos de administrador:
> sudo npm install -g pnpm
> # O con el instalador oficial (sin sudo):
> curl -fsSL https://get.pnpm.io/install.sh | sh -
> ```
> Si prefieres usar npm directamente, es compatible: descomenta las líneas de npm 
> en `web-insti/web-insti/Dockerfile` y comenta las de pnpm.

---

## Licencia

Esta obra está bajo una licencia 
[Creative Commons Reconocimiento-CompartirIgual 3.0 España](http://creativecommons.org/licenses/by-sa/3.0/es/).
