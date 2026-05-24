<template>
  <form class="composer" @submit.prevent="submit">
    <textarea
      v-model="text"
      class="input"
      rows="1"
      placeholder="Escribe tu consulta..."
      :disabled="disabled"
      @keydown.enter.exact.prevent="submit"
    />

    <button class="send-button" type="submit" :disabled="disabled || !text.trim()" aria-label="Enviar">
      <svg viewBox="0 0 24 24" class="send-icon" aria-hidden="true">
        <path d="M3.4 20.2 21 12 3.4 3.8 3 10l12 2-12 2z" fill="currentColor" />
      </svg>
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['send'])
const text = ref('')

function submit() {
  if (!text.value.trim() || props.disabled) return
  emit('send', text.value.trim())
  text.value = ''
}
</script>

<style scoped>
.composer {
  width: 100%;
  min-width: 0;
  min-height: 62px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 10px 10px 14px;
  border-radius: 999px;
  background: var(--surface);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-md);
  box-sizing: border-box;
  margin: 0;
}

.input {
  flex: 1 1 auto;
  min-width: 0;
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  color: var(--text);
  font: inherit;
  font-size: var(--fs-md);
  line-height: 1.4;
  resize: none;
  padding: 0;
  margin: 0;
}

.send-button {
  flex: 0 0 auto;
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: var(--primary);
  color: #ffffff;
  display: grid;
  place-items: center;
  cursor: pointer;
}

</style>