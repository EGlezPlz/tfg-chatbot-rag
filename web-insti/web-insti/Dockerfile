FROM node:20-alpine AS builder

# Instalar pnpm (más seguro que npm: no ejecuta scripts de postinstalación)
RUN npm install -g pnpm

WORKDIR /app
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install --frozen-lockfile

# Opción npm (descomentar si hay problemas con pnpm):
# COPY package*.json ./
# RUN npm install

COPY . .
RUN pnpm run build

FROM nginx:alpine
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=builder /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
