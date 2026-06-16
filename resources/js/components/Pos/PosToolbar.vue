<script setup lang="ts">
import { Barcode, Search } from 'lucide-vue-next';

const props = defineProps<{
    search: string;
    barcodeMode: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:search', val: string): void;
    (e: 'search'): void;
    (e: 'toggle-barcode-mode'): void;
}>();

function onInput(e: Event) {
    const val = (e.target as HTMLInputElement).value;
    emit('update:search', val);
    if (!props.barcodeMode) {
        emit('search');
    }
}

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter' && props.barcodeMode) {
        emit('search');
    }
}
</script>

<template>
  <div class="panel-toolbar">
    <div class="search-wrap">
      <component :is="barcodeMode ? Barcode : Search" class="search-icon" />
      <input
        :value="search"
        @input="onInput"
        @keydown="onKeydown"
        class="search-input"
        :class="{ 'search-input--barcode': barcodeMode }"
        type="text"
        :placeholder="barcodeMode ? 'Scan barcode or enter SKU…' : 'Search products by name…'"
        autocomplete="off"
      />
    </div>
    <button
      class="toolbar-btn"
      :class="{ active: barcodeMode }"
      @click="emit('toggle-barcode-mode')"
      title="Toggle barcode scan mode"
    >
      <Barcode /> Barcode
    </button>
  </div>
</template>
