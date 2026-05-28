#!/bin/bash
# setup-infra.sh — crear recursos compartidos antes de levantar los compose

echo "Creando redes externas..."
docker network create red_ia 2>/dev/null || echo "red_ia ya existe"

echo "Creando volúmenes externos..."
docker volume create n8n_data 2>/dev/null || echo "n8n_data ya existe"

echo "Listo. Ahora puedes levantar todos los servicios con: docker compose -f docker-compose.global.yml up -d"
