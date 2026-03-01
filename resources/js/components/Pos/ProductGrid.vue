<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { Paginated, Product } from '@/types';
import ProductCard from './ProductCard.vue';


const {products} = defineProps<{
    products: Paginated<Product>;
}>();

/**
 * This function is to navigate through paginated results
 * @param url pagination url (next or previous)
 */
function goToPage(url: string | null){
    if(!url) return;
    router.get(url,{},{
        preserveScroll: true,
        preserveState: true,
        only: ['products']
    })
}

/**
 * Function to format laravel's pagination label
 * because label contains symbols like >> for next (Next >>) 
 * and this function removes it. Same for previous.
 * @param label 
 */
function formatLabel(label: string){
  if(label.includes('Previous')) return 'Prev';
  if(label.includes('Next')) return 'Next';
  return label;
}

</script>

<template>
  <section class="product-grid-wrap">
    <ul class="grid grid-cols-[repeat(auto-fill,minmax(140px,1fr))] gap-[0.9rem]" role="list">
      <li v-for="product in products.data" :key="product.id">
        <ProductCard :product="product" @click="$emit('add-to-cart', product)" />
      </li>
    </ul>

    <hr class="my-4 h-px border-0" style="background: hsl(var(--border) / 0.6)" />

    <!-- Pagination -->
    <div class="flex flex-wrap items-center justify-center">
      <button
        v-for="link in products.links"
        :key="link.label"
        :disabled="!link.url"
        @click="goToPage(link.url)"
        class="pagination-btn"
        :class="{ 'is-active': link.active, 'cursor-not-allowed opacity-50': !link.url }"
      >
        {{ formatLabel(link.label) }}
      </button>
    </div>
  </section>
</template>