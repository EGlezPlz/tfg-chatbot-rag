<template>
  <div class="chat-shell">
    <header class="chat-header">
      <div class="brand">
        <img class="brand-logo" :src="logoUrl" alt="IES Venancio Blanco" />
        <div class="brand-text">
          <h1>VenancIA</h1>
          <p>Asistente del IES Venancio Blanco</p>
        </div>
      </div>

      <div class="status-chip">
        <span class="status-dot"></span>
        <span>En línea</span>
      </div>
    </header>

    <main ref="chatBodyRef" class="chat-body">
      <section v-if="messages.length === 0" class="empty-state">
        <div class="welcome-card">
          <h2>¿En qué puedo ayudarte hoy?</h2>
          <p>Prueba alguna de estas consultas rápidas para empezar.</p>

          <div class="suggestions">
            <button
              v-for="suggestion in suggestions"
              :key="suggestion"
              class="suggestion-card"
              type="button"
              @click="handleSend(suggestion)"
            >
              {{ suggestion }}
            </button>
          </div>
        </div>
      </section>

      <ChatMessage
        v-for="message in messages"
        :key="message.id"
        :message="message"
      />

      <div v-if="isLoading" class="loading-row">
        <div class="typing-indicator">
          <span class="thinking-label">Pensando...</span>
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>

      <ChatSources v-if="false && lastSources.length" :sources="lastSources" />
    </main>

    <footer class="chat-footer">
      <ChatInput :disabled="isLoading" @send="handleSend" />
    </footer>
  </div>
</template>

<script setup>
import { nextTick, ref } from 'vue'
import ChatMessage from './ChatMessage.vue'
import ChatInput from './ChatInput.vue'
import ChatSources from './ChatSources.vue'
import { sendChatMessage } from '@/services/chatService'
import logoUrl from '@/assets/images/logo.png'

const messages = ref([])
const isLoading = ref(false)
const lastSources = ref([])
const sessionId = ref(null)
const chatBodyRef = ref(null)

const suggestions = [
  '¿Qué ciclos formativos ofrece el centro?',
  '¿Cuál es el horario de secretaría?',
  '¿Cómo puedo contactar con el instituto?'
]

function normalizeMessage(text, role) {
  return {
    id: crypto.randomUUID(),
    role,
    text,
    timestamp: new Date().toISOString()
  }
}

function scrollToBottom() {
  nextTick(() => {
    const el = chatBodyRef.value
    if (el) el.scrollTop = el.scrollHeight
  })
}

async function handleSend(text) {
  if (!text?.trim() || isLoading.value) return

  messages.value.push(normalizeMessage(text, 'user'))
  isLoading.value = true
  scrollToBottom()

  try {
    const response = await sendChatMessage(text, sessionId.value)

    sessionId.value = response.session_id ?? sessionId.value

    messages.value.push(
      normalizeMessage(response.respuesta ?? 'No se obtuvo respuesta.', 'assistant')
    )

    lastSources.value = response.fuentes ?? []
  } catch (error) {
    messages.value.push(
      normalizeMessage(
        error?.message || 'Ha ocurrido un error al procesar tu mensaje.',
        'assistant'
      )
    )
  } finally {
    isLoading.value = false
    scrollToBottom()
  }
}
</script>

<style scoped>
.chat-shell {
  height: 100%;
  min-height: 0;
  min-width: 0;
  display: flex;
  flex-direction: column;
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-sans);
  overflow: hidden;
}

.chat-header {
  flex: 0 0 auto;
  min-width: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: var(--header-pad);
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border);
}

.brand {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.brand-logo {
  width: 42px;
  height: 42px;
  object-fit: contain;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: var(--shadow-sm);
  flex: 0 0 auto;
}

.brand-text {
  min-width: 0;
}

.brand-text h1 {
  margin: 0;
  font-size: var(--fs-lg);
  font-weight: 800;
  letter-spacing: -0.02em;
}

.brand-text p {
  margin: 2px 0 0;
  font-size: var(--fs-xs);
  color: var(--muted);
}

.status-chip {
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 999px;
  background: var(--surface);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  font-size: var(--fs-xs);
  color: var(--muted);
}

.status-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: var(--success);
  box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.45);
  animation: pulse 1.8s infinite;
}

.chat-body {
  flex: 1 1 auto;
  min-height: 0;
  min-width: 0;
  overflow-y: auto;
  overflow-x: hidden;
  padding: var(--body-pad);
  display: flex;
  flex-direction: column;
  align-items: stretch;
  gap: var(--space-3);
}

.empty-state {
  flex: 1 1 auto;
  width: 100%;
  min-height: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.welcome-card {
  width: min(760px, 100%);
  margin: 0 auto;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  padding: clamp(22px, 3vw, 30px);
}

.welcome-card h2 {
  margin: 0 0 8px;
  font-size: var(--fs-lg);
  font-weight: 700;
}

.welcome-card p {
  margin: 0;
  color: var(--muted);
  font-size: var(--fs-sm);
}

.suggestions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: var(--space-3);
  margin-top: var(--space-5);
}

.suggestion-card {
  text-align: left;
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 14px 16px;
  background: var(--surface-alt);
  color: var(--text);
  cursor: pointer;
  transition: transform var(--transition-fast), background var(--transition-fast), border-color var(--transition-fast);
}

.suggestion-card:hover {
  transform: translateY(-2px);
  background: var(--surface-hover);
  border-color: rgba(31, 95, 168, 0.25);
}

.loading-row {
  display: flex;
  justify-content: flex-start;
}

.typing-indicator {
  display: inline-flex;
  gap: 6px;
  padding: 14px 16px;
  border-radius: 20px;
  border-top-left-radius: 8px;
  background: var(--assistant-bubble);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
}

.thinking-label {
  font-size: var(--fs-sm);
  color: var(--muted);
  align-self: center;
  margin-right: 4px;
}

.typing-indicator span:not(.thinking-label) {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: bounce 1.2s infinite ease-in-out;
}

.typing-indicator span:nth-child(3) { animation-delay: 0.15s; }
.typing-indicator span:nth-child(4) { animation-delay: 0.3s; }

.chat-footer {
  flex: 0 0 auto;
  width: 100%;
  padding: var(--footer-pad) var(--body-pad);
  background: linear-gradient(to top, rgba(245, 247, 251, 1), rgba(245, 247, 251, 0.82));
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: center;
  box-sizing: border-box;
}

.chat-footer :deep(.composer) {
  width: 100%;
  max-width: 100%;
  margin: 0;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.35); }
  70% { box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}

@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); opacity: 0.55; }
  40% { transform: translateY(-4px); opacity: 1; }
}

@media (max-width: 768px) {
  .chat-header {
    padding: 12px 14px;
  }

  .brand-logo {
    width: 36px;
    height: 36px;
  }

  .chat-body {
    padding: 14px;
  }

  .chat-footer {
    padding: 10px 10px 12px;
  }

  .empty-state {
    align-items: flex-start;
  }

  .welcome-card {
    width: 100%;
    margin: 0;
  }

  .suggestions {
    grid-template-columns: 1fr;
  }
}
</style>