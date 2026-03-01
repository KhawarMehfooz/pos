<script setup lang="ts">
import { Search, Barcode } from 'lucide-vue-next';

const {search} = defineProps<{ search: string }>();
const emit = defineEmits<{ (e: 'update:search', val: string): void; (e: 'search'): void }>();

function onInput(e: Event) {
  const val = (e.target as HTMLInputElement).value;
  emit('update:search', val);
  emit('search');
}
</script>

<template>
  <div class="panel-toolbar">
    <div class="search-wrap">
      <Search class="search-icon" />
      <input
        :value="search"
        @input="onInput"
        class="search-input"
        type="text"
        placeholder="Search products or scan barcode…"
      />
    </div>
    <button class="toolbar-btn">
      <Barcode /> Barcode
    </button>
  </div>
</template>