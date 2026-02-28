<script setup lang="ts">
import { computed } from 'vue';
import type { Product } from '@/types';
const props = defineProps<{ product: Product }>();

// Helper for stock title
const stockTitle = computed(() => {
  const { track_stock, stock_quantity } = props.product;
  if (!track_stock) return 'Stock Not Tracked';
  return stock_quantity > 0 ? 'In Stock' : 'Out of Stock';
});

// Stock dot class
const stockClass = computed(() => {
  const { track_stock, stock_quantity } = props.product;
  return !track_stock || stock_quantity > 0 ? 'stock-in' : 'stock-out';
});
</script>

<template>
  <article class="product-card" title="Add to cart">
    <figure class="product-thumb">
      <img :src="product.product_image_url" :alt="product.product_name" />
    </figure>

    <h3 class="product-name" :title="product.product_name">
      {{ product.product_name }}
    </h3>

    <p class="product-sku">{{ product.sku }}</p>

    <footer class="product-row">
      <span class="product-price">
        <template v-if="product.sale_price">
          <span class="sale-price">PKR {{ product.sale_price }}</span>
          <span class="product-price-orig retail-price block line-through">
            PKR {{ product.retail_price }}
          </span>
        </template>
        <template v-else>PKR {{ product.retail_price }}</template>
      </span>

      <span
        class="stock-dot"
        :class="stockClass"
        :title="stockTitle"
        aria-hidden="true"
      ></span>
    </footer>
  </article>
</template>