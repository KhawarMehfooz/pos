<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next';
import type { CartItem as CartItemType, Customer } from '@/types';
import CartHeader from './CartHeader.vue';
import CartItem from './CartItem.vue';
import CartSummary from './CartSummary.vue';

const { cart } = defineProps<{
    cart: CartItemType[];
    subtotal: number;
    appliedDiscount: number;
    totalDue: number;
    discountAmount: number | null;
    discountInput: string;
    hasCartItems: boolean;
    canApplyDiscount: boolean;
    customers: Customer[];
    customerSearch: string;
    selectedCustomer: Customer | null;
}>();

const emit = defineEmits<{
    (e: 'increment', item: CartItemType): void;
    (e: 'decrement', item: CartItemType): void;
    (e: 'remove', item: CartItemType): void;
    (e: 'apply-discount'): void;
    (e: 'remove-discount'): void;
    (e: 'update-quantity', item: CartItemType): void;
    (e: 'update-discount-input', value: string): void;
    (e: 'update-customer-search', value: string): void;
    (e: 'select-customer', customer: Customer): void;
    (e: 'search-customer'): void;
    (e: 'clear-cart'): void;
    (e: 'charge-payment'): void;
}>();
</script>

<template>
    <aside class="cart-panel">
        <!-- Cart Header -->
        <CartHeader
            :customers="customers"
            :search="customerSearch"
            :selected-customer="selectedCustomer"
            @update:search="emit('update-customer-search', $event)"
            @select-customer="emit('select-customer', $event)"
            @clear-cart="emit('clear-cart')"
            @search-customer = "emit('search-customer')"
        />

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

        <!-- Cart Summary -->
        <CartSummary
            :cart="cart"
            :subtotal="subtotal"
            :appliedDiscount="appliedDiscount"
            :totalDue="totalDue"
            :discountAmount="discountAmount"
            :discountInput="discountInput"
            :hasCartItems="hasCartItems"
            :canApplyDiscount="canApplyDiscount"
            @apply-discount="emit('apply-discount')"
            @remove-discount="emit('remove-discount')"
            @update-discount-input="emit('update-discount-input', $event)"
            @charge-payment="emit('charge-payment')"
        />
    </aside>
</template>
