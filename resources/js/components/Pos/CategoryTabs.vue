<script setup lang="ts">
import {  ref, computed } from 'vue';
import type { Category } from '@/types';

const props = defineProps<{
    categories: Category[];
    activeCategoryId: number;
}>();

const emit = defineEmits<{
    (e: 'update:category', categoryId: number): void;
}>();

const allCategory: Category = { id: 0, category_name: 'All', parent_id: null, children: [] };

const categoriesWithAll = computed(() => [allCategory, ...props.categories]);

const activeCategory = ref(
    categoriesWithAll.value.find(c => c.id === props.activeCategoryId) || allCategory
);

function selectCategory(category: Category) {
    activeCategory.value = category;
    emit('update:category', category.id);
}
</script>

<template>
  <nav class="categories-wrapper">
    <ul class="no-scrollbar flex gap-2 overflow-x-auto p-1">
      <li
        v-for="category in categoriesWithAll"
        :key="category.id"
        :class="[
          'cursor-pointer px-4 py-1 rounded-[6px] border border-dashed text-xs whitespace-nowrap transition',
          activeCategory.id === category.id
            ? 'border-amber-400 bg-amber-200'
            : 'border-amber-200 bg-amber-50 hover:border-amber-300 hover:bg-amber-100'
        ]"
        @click="selectCategory(category)"
      >
        {{ category.category_name }}
      </li>
    </ul>
  </nav>
</template>