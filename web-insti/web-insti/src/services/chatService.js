const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:5678/webhook'

export async function sendChatMessage(text, sessionId = null) {
  const response = await fetch(`${API_BASE_URL}/chat-agente`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json'
    },
    body: JSON.stringify({
      question: text,
      session_id: sessionId
    })
  })
  const data = await response.json().catch(() => null)
  if (!response.ok) {
    const message = data?.detail || data?.answer || `HTTP ${response.status}`
    throw new Error(message)
  }
  return data
}