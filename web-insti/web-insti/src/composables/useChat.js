import { computed, ref } from 'vue'
import { sendChatMessage } from '@/services/chatService'

export function useChat() {
  const messages = ref([
    { role: 'bot', text: '¡Hola! ¿En qué puedo ayudarte?' }
  ])

  const isLoading = ref(false)
  const lastSources = ref([])
  const sessionId = ref(null)
  const status = ref('idle')
  const error = ref(null)

  const statusLabel = computed(() => {
    if (status.value === 'thinking') return 'Pensando...'
    if (status.value === 'searching') return 'Buscando información...'
    if (status.value === 'error') return 'Ha ocurrido un error'
    return 'En línea'
  })

  const loadingLabel = computed(() => {
    if (status.value === 'thinking') return 'Pensando...'
    if (status.value === 'searching') return 'Buscando información...'
    return 'Procesando...'
  })

  async function sendMessage(text) {
    const cleanText = text?.trim()
    if (!cleanText || isLoading.value) return

    messages.value.push({ role: 'user', text: cleanText })
    isLoading.value = true
    status.value = 'searching'
    error.value = null

    try {
      const data = await sendChatMessage(cleanText, sessionId.value)
      sessionId.value = data.session_id ?? sessionId.value
      messages.value.push({ role: 'bot', text: data.answer })
      lastSources.value = data.sources ?? []
      status.value = 'idle'
      return data
    } catch (err) {
      error.value = err
      messages.value.push({
        role: 'bot',
        text: 'Lo siento, ha habido un problema al procesar tu consulta.'
      })
      status.value = 'error'
      lastSources.value = []
      throw err
    } finally {
      isLoading.value = false
    }
  }

  function resetChat() {
    messages.value = [
      { role: 'bot', text: '¡Hola! ¿En qué puedo ayudarte?' }
    ]
    isLoading.value = false
    lastSources.value = []
    sessionId.value = null
    status.value = 'idle'
    error.value = null
  }

  return {
    messages,
    isLoading,
    lastSources,
    sessionId,
    status,
    error,
    statusLabel,
    loadingLabel,
    sendMessage,
    resetChat
  }
}