<script setup lang="ts">
import { Pause, Trash, UserRound } from 'lucide-vue-next';
import type { CartItem } from '@/types';


const {cart} = defineProps<{
    cart: CartItem[] 
}>()

</script>
<template>
    <div class="cart-header">
        <div class="cart-header-row">
            <div class="cart-title">
                Cart
                <span class="cart-count" id="cartCount">{{cart.length}}</span>
            </div>
            <div class="cart-actions">
                <button class="icon-btn btn-hold" title="Hold order">
                    <Pause />
                </button>
                <button class="icon-btn btn-clear" title="Clear cart">
                    <Trash />
                </button>
            </div>
        </div>

        <div class="customer-selector">
            <UserRound />
            <select
                class="w-full cursor-pointer border-none outline-none focus:border-none focus:ring-0 focus:outline-none"
            >
                <option>Walk-in Customer</option>
            </select>
        </div>
    </div>
</template>
