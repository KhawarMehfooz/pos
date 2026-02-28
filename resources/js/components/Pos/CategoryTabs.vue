<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { Category } from '@/types';

const page = usePage();

const categories = ref<Category[]>(page.props.categories as Category[]);

const activeParent = ref<Category | null>(null);

const parentCategories = computed(() =>
    categories.value.filter((category) => category.parent_id === null),
);
</script>

<template>
    <nav class="categories-wrapper">
        <ul
            class="no-scrollbar flex flex-nowrap items-center gap-4 overflow-x-auto p-1"
        >
            <li
                v-for="category in parentCategories"
                :key="category.id"
                class="min-w-fit cursor-pointer rounded-[6px] border border-dashed border-amber-200 bg-amber-50 px-4 py-1 text-xs whitespace-nowrap hover:border-amber-300 hover:bg-amber-100"
                @mouseenter="activeParent = category"
                @click="activeParent = category"
            >
                {{ category.category_name }}
            </li>
        </ul>
    </nav>
</template>