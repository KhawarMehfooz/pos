<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next';
import type { CartItem as CartItemType } from '@/types';
import CartHeader from './CartHeader.vue';
import CartItem from './CartItem.vue';

const { cart } = defineProps<{
    cart: CartItemType[];
    
}>();

const emit = defineEmits<{
    (e: 'increment', item: CartItemType): void;
    (e: 'decrement', item: CartItemType): void;
    (e: 'remove', item: CartItemType): void;
    (e: 'apply-discount'): void;
    (e: 'remove-discount'): void;
    (e: 'update-quantity', item: CartItemType): void;
}>();
</script>

<template>
    <aside class="cart-panel">

        <!-- Cart Header -->
        <CartHeader :cart="cart" />

        <!-- Cart Items -->
        <div class="cart-items">

            <!-- Empty Cart -->
            <div v-if="!cart.length" class="cart-empty">
                <div class="cart-empty-icon">
                    <ShoppingCart />
                </div>
                <p>
                    <strong>Cart is empty</strong><br />
                    Click a product or scan a barcode to add items
                </p>
            </div>

            <!-- Cart Item -->
            <template v-else>
                <CartItem
                    v-for="item in cart"
                    :key="item.product.id"
                    :cart-item="item"
                    @increment="emit('increment', $event)"
                    @decrement="emit('decrement', $event)"
                    @remove="emit('remove', $event)"
                    @update-quantity="emit('update-quantity', $event)"
                />
            </template>
        </div>
    </aside>
</template>
