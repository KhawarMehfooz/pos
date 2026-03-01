<script setup lang="ts">
import { Minus, Plus, X } from 'lucide-vue-next';
import type { CartItem } from '@/types';

const { cartItem } = defineProps<{
  cartItem: CartItem;
}>();

const emit = defineEmits<{
  (e: 'increment', item: CartItem): void;
  (e: 'decrement', item: CartItem): void;
  (e: 'remove', item: CartItem): void;
  (e: 'update-quantity', item: CartItem): void;
}>();

function onQuantityInput(event: Event) {
  const value = Number((event.target as HTMLInputElement).value)

  emit('update-quantity', {
    ...cartItem,
    quantity: value
  })
}

function itemTotal() {
  return (
    (cartItem.product.sale_price ?? cartItem.product.retail_price) *
    cartItem.quantity
  );
}
</script>

<template>
  <div class="cart-item">
    <img :src="cartItem.product.product_image_url" class="cart-item-thumb" />

    <div class="cart-item-info">
      <div class="cart-item-name">
        {{ cartItem.product.product_name }}
      </div>

      <div class="cart-item-meta">
        <span class="unit-price">
          PKR {{ cartItem.product.sale_price ?? cartItem.product.retail_price }}
        </span>
        · {{ cartItem.product.sku }}
      </div>
    </div>

    <div class="cart-item-controls">
      <div class="qty-control">
        <button class="qty-btn minus" @click="emit('decrement', cartItem)">
          <Minus />
        </button>

        <input
          :value="cartItem.quantity"
          @input="onQuantityInput"
          type="number"
          class="qty-value"
          min="1"
          :max="cartItem.product.track_stock ? cartItem.product.stock_quantity : undefined"
        />

        <button class="qty-btn" @click="emit('increment', cartItem)">
          <Plus />
        </button>
      </div>

      <div class="cart-item-total">
        PKR {{ itemTotal().toFixed(2) }}
      </div>
    </div>

    <button class="item-delete" @click="emit('remove', cartItem)">
      <X />
    </button>
  </div>
</template>