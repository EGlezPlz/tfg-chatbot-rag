<template>
  <article class="message" :class="message.role">
    <div class="bubble">
      <p class="text">{{ displayedText }}<span v-if="isTyping" class="cursor">▋</span></p>
      <time class="timestamp" v-if="!isTyping">{{ formattedTime }}</time>
    </div>
  </article>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'

const props = defineProps({
  message: {
    type: Object,
    required: true
  }
})

const displayedText = ref('')
const isTyping = ref(false)

const formattedTime = computed(() => {
  const date = new Date(props.message.timestamp)
  return new Intl.DateTimeFormat('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
})

function typeText(text) {
  // Solo aplicar typewriter a mensajes del asistente
  if (props.message.role !== 'assistant') {
    displayedText.value = text
    return
  }

  displayedText.value = ''
  isTyping.value = true

  const chars = [...text] // Soporte correcto para emojis y caracteres especiales
  let i = 0

  // Velocidad adaptativa según longitud del texto
  const speed = text.length > 300 ? 8 : text.length > 100 ? 15 : 25

  function typeNext() {
    if (i < chars.length) {
      displayedText.value += chars[i]
      i++
      setTimeout(typeNext, speed)
    } else {
      isTyping.value = false
    }
  }

  typeNext()
}

onMounted(() => {
  typeText(props.message.text)
})

watch(() => props.message.text, (newText) => {
  typeText(newText)
})
</script>

<style scoped>
.message {
  display: flex;
  max-width: 100%;
  min-width: 0;
}
.message.user {
  justify-content: flex-end;
}
.message.assistant {
  justify-content: flex-start;
}
.bubble {
  max-width: min(760px, 88vw);
  min-width: 0;
  padding: 14px 16px;
  border-radius: 20px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  overflow-wrap: anywhere;
  word-break: break-word;
}
.message.user .bubble {
  background: var(--primary);
  color: #ffffff;
  border-top-right-radius: 8px;
}
.message.assistant .bubble {
  background: var(--assistant-bubble);
  color: var(--text);
  border-top-left-radius: 8px;
}
.text {
  margin: 0;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
  word-break: break-word;
  line-height: 1.6;
  font-size: var(--fs-md);
}
.cursor {
  display: inline-block;
  animation: blink 0.7s step-end infinite;
  color: var(--primary);
  font-weight: 300;
}
.timestamp {
  display: block;
  margin-top: 8px;
  font-size: var(--fs-xs);
  opacity: 0.7;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}
</style>