const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000'

export async function sendChatMessage(text, sessionId = null) {
  const response = await fetch(`${API_BASE_URL}/chat`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json'
    },
    body: JSON.stringify({
      pregunta: text,
      session_id: sessionId
    })
  })

  const data = await response.json().catch(() => null)

  if (!response.ok) {
    const message = data?.detail || data?.respuesta || `HTTP ${response.status}`
    throw new Error(message)
  }

  return data
}