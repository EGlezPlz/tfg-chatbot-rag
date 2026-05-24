<template>
  <article class="message" :class="message.role">
    <div class="bubble">
      <p class="text">{{ message.text }}</p>
      <time class="timestamp">{{ formattedTime }}</time>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: {
    type: Object,
    required: true
  }
})

const formattedTime = computed(() => {
  const date = new Date(props.message.timestamp)
  return new Intl.DateTimeFormat('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
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

.timestamp {
  display: block;
  margin-top: 8px;
  font-size: var(--fs-xs);
  opacity: 0.7;
}
</style>