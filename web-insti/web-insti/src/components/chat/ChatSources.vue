<template>
  <section class="sources">
    <div class="sources-header">
      <h3>Fuentes</h3>
      <span>{{ sources.length }}</span>
    </div>

    <ul class="sources-list">
      <li v-for="(source, index) in sources" :key="source.id ?? index" class="source-item">
        <a
          v-if="source.url"
          :href="source.url"
          target="_blank"
          rel="noopener noreferrer"
          class="source-link"
        >
          <strong>{{ source.title || source.name || `Fuente ${index + 1}` }}</strong>
          <small>{{ source.url }}</small>
        </a>

        <div v-else class="source-link source-link-static">
          <strong>{{ source.title || source.name || `Fuente ${index + 1}` }}</strong>
          <small>{{ source.snippet || 'Sin enlace disponible' }}</small>
        </div>
      </li>
    </ul>
  </section>
</template>

<script setup>
defineProps({
  sources: {
    type: Array,
    default: () => []
  }
})
</script>

<style scoped>
.sources {
  width: 100%;
  border-radius: 22px;
  background: var(--surface);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  padding: 16px;
}

.sources-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.sources-header h3 {
  margin: 0;
  font-size: var(--fs-sm);
  font-weight: 700;
}

.sources-header span {
  font-size: var(--fs-xs);
  color: var(--muted);
  background: var(--surface-alt);
  border-radius: 999px;
  padding: 4px 10px;
}

.sources-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 10px;
}

.source-link {
  display: grid;
  gap: 4px;
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid var(--border);
  background: var(--surface-alt);
  text-decoration: none;
  color: inherit;
  transition: transform var(--transition-fast), background var(--transition-fast), border-color var(--transition-fast);
}

.source-link:hover {
  transform: translateY(-1px);
  background: var(--surface-hover);
  border-color: rgba(31, 95, 168, 0.22);
}

.source-link small {
  color: var(--muted);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>