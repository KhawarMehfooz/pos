<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import {
    Check,
    Minus,
    Pause,
    Plus,
    ShoppingCart,
    Tag,
    Trash,
    UserRound,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import CartPanel from '@/components/Pos/cart/CartPanel.vue';
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';
import Header from '@/components/Pos/Header.vue';
import PosToolbar from '@/components/Pos/PosToolbar.vue';
import ProductGrid from '@/components/Pos/ProductGrid.vue';
import type { CartItem, Category, Paginated, Product } from '@/types';

const props = defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    activeCategoryId?: number;
}>();

const searchTerm = ref('');
const activeCategoryId = ref(props.activeCategoryId ?? 0);
const discountInput = ref('');
const discountAmount = ref<number | null>(null);

function filterByCategory(categoryId: number) {
    activeCategoryId.value = categoryId;
    router.get(
        '/pos',
        { category: categoryId, search: searchTerm.value },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['products', 'activeCategoryId'],
        },
    );
}

function searchProducts() {
    router.get(
        '/pos',
        { category: activeCategoryId.value, search: searchTerm.value },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['products', 'activeCategoryId'],
        },
    );
}

const debouncedSearch = debounce(searchProducts, 500);

const cart = ref<CartItem[]>([]);

/**
 * Add product to cart
 */
function addToCart(product: Product) {
    const existing = cart.value.find((item) => item.product.id === product.id);

    if (existing) {
        // Increment quantity but not above stock
        if (
            !product.track_stock ||
            existing.quantity < product.stock_quantity
        ) {
            existing.quantity++;
        }
    } else {
        cart.value.push({
            product,
            quantity: 1,
        });
    }
}

/**
 * Increment quantity in cart
 */
function increment(item: CartItem) {
    if (
        !item.product.track_stock ||
        item.quantity < item.product.stock_quantity
    ) {
        item.quantity++;
    }
}

/**
 * Decrement quantity in cart
 */
function decrement(item: CartItem) {
    if (item.quantity > 1) {
        item.quantity--;
    } else {
        remove(item);
    }
}

/**
 * Remove item from cart
 */
function remove(item: CartItem) {
    const index = cart.value.indexOf(item);
    if (index > -1) cart.value.splice(index, 1);
}

function handleUpdateQuantity(updatedItem: CartItem) {
    const found = cart.value.find(
        (i) => i.product.id === updatedItem.product.id,
    );
    if (found) found.quantity = updatedItem.quantity;
}

/**
 * Compute total for a cart item
 */
function itemTotal(item: CartItem) {
    return (
        (item.product.sale_price ?? item.product.retail_price) * item.quantity
    );
}

/**
 * Calculate subtotal
 */

const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => {
        return sum + itemTotal(item);
    }, 0);
});

/**
 * Final discount applied to the cart.
 * Clamped so it never exceeds the subtotal.
 */
const appliedDiscount = computed(() => {
    if (!discountAmount.value) return 0;

    // discount should not be greater than subtotal
    return Math.min(discountAmount.value, subtotal.value);
});

/**
 * Total amount due after discount.
 */
const totalDue = computed(() => {
    return Math.max(subtotal.value - appliedDiscount.value, 0);
});

/**
 * Whether the cart currently has items.
 */
const hasCartItems = computed(() => cart.value.length > 0);

/**
 * Determines if a discount can be applied.
 * Requires cart items and a valid discount value.
 */
const canApplyDiscount = computed(() => {
    return hasCartItems.value && Number(discountInput.value) > 0;
});

/**
 * Apply a fixed discount amount to the cart.
 * Ignores invalid values and empty carts.
 */
function applyDiscount() {
    if (!hasCartItems.value) return;

    const value = Math.floor(Number(discountInput.value));
    if (value <= 0) return;

    discountAmount.value = Math.min(value, subtotal.value);
    discountInput.value = '';
}

/**
 * Remove the currently applied discount.
 */
function removeDiscount() {
    discountAmount.value = null;
}

function clearCart(){
    cart.value = [];

    discountAmount.value = null;
    discountInput.value = '';
}

/**
 * Clear discount when cart becomes empty.
 *
 * This watcher ensures discounts are not kept when there
 * are no items in the cart.
 */
watch(
    cart,
    () => {
        if (cart.value.length === 0) {
            discountAmount.value = null;
            discountInput.value = '';
        }
    },
    { deep: true },
);
</script>

<template>
    <Header />

    <div class="pos-layout">
        <div class="product-panel">
            <!-- toolbar -->
            <PosToolbar v-model:search="searchTerm" @search="debouncedSearch" />

            <CategoryTabs
                :categories="categories"
                :activeCategoryId="activeCategoryId"
                @update:category="filterByCategory"
            />

            <ProductGrid :products="products" @add-to-cart="addToCart" />
        </div>

        <CartPanel
            :cart="cart"
            :subtotal="subtotal"
            :appliedDiscount="appliedDiscount"
            :totalDue="totalDue"
            :discountAmount="discountAmount"
            :discountInput="discountInput"
            :hasCartItems="hasCartItems"
            :canApplyDiscount="canApplyDiscount"
            :applyDiscount="applyDiscount"
            :removeDiscount="removeDiscount"
            @increment="increment"
            @decrement="decrement"
            @remove="remove"
            @update-quantity="handleUpdateQuantity"
            @update-discount-input="discountInput = $event"
            @apply-discount="applyDiscount"
            @remove-discount="removeDiscount"
            @clear-cart="clearCart"
        />
    </div>
</template>
