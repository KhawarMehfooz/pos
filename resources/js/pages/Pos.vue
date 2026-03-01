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
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';
import Header from '@/components/Pos/Header.vue';
import PosToolbar from '@/components/Pos/PosToolbar.vue';
import ProductGrid from '@/components/Pos/ProductGrid.vue';
import type { CartItem, Category, Paginated, Product } from '@/types';
import CartPanel from '@/components/Pos/cart/CartPanel.vue';

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
        />

        <!-- Cart -->
        <aside class="cart-panel" style="display: none">
            <div class="cart-header">
                <div class="cart-header-row">
                    <div class="cart-title">
                        Cart
                        <span class="cart-count" id="cartCount">0</span>
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

            <!-- cart items -->
            <div class="cart-items" id="cartItems">
                <!-- cart empty -->
                <div class="cart-empty" id="cartEmpty" v-if="!cart.length">
                    <div class="cart-empty-icon">
                        <ShoppingCart />
                    </div>

                    <p>
                        <strong>Cart is empty</strong><br />Click a product or
                        scan a barcode to add items
                    </p>
                </div>

                <!-- cart item -->
                <div
                    v-for="item in cart"
                    :key="item.product.id"
                    class="cart-item"
                >
                    <img
                        :src="item.product.product_image_url"
                        class="cart-item-thumb"
                    />
                    <div class="cart-item-info">
                        <div class="cart-item-name">
                            {{ item.product.product_name }}
                        </div>
                        <div class="cart-item-meta">
                            <span class="unit-price"
                                >PKR
                                {{
                                    item.product.sale_price ??
                                    item.product.retail_price
                                }}</span
                            >
                            ·
                            {{ item.product.sku }}
                        </div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="qty-control">
                            <button
                                class="qty-btn minus"
                                @click="decrement(item)"
                            >
                                <Minus />
                            </button>
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                class="qty-value"
                                min="1"
                                :max="
                                    item.product.track_stock
                                        ? item.product.stock_quantity
                                        : undefined
                                "
                                step="1"
                            />
                            <button class="qty-btn" @click="increment(item)">
                                <Plus />
                            </button>
                        </div>
                        <div class="cart-item-total">
                            PKR {{ itemTotal(item).toFixed(2) }}
                        </div>
                    </div>
                    <button class="item-delete" @click="remove(item)">
                        <X />
                    </button>
                </div>
            </div>

            <!-- order summary -->
            <div class="cart-summary" id="cartSummary" style="display: block">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="subtotalVal"
                        >PKR {{ subtotal.toFixed(2) }}</span
                    >
                </div>
                <div
                    v-if="appliedDiscount > 0"
                    class="summary-row"
                    id="discountRow"
                >
                    <span class="summary-label">Discount </span>
                    <span class="summary-value discount" id="discountVal"
                        >PKR {{ appliedDiscount.toFixed(2) }}</span
                    >
                </div>

                <hr class="summary-divider" />

                <div class="summary-total-row">
                    <span class="summary-total-label">Total Due</span>
                    <span class="summary-total-value" id="totalVal"
                        >PKR {{ totalDue.toFixed(2) }}</span
                    >
                </div>

                <!-- Discount -->
                <form
                    v-if="!discountAmount"
                    class="discount-row"
                    @submit.prevent="applyDiscount"
                >
                    <div class="discount-input-wrap">
                        <input
                            v-model="discountInput"
                            class="discount-input"
                            type="number"
                            min="0"
                            :max="subtotal"
                            step="1"
                            placeholder="Discount Amount"
                            autocomplete="off"
                            :disabled="!hasCartItems"
                        />
                    </div>

                    <button
                        type="submit"
                        class="apply-btn"
                        :disabled="!canApplyDiscount"
                    >
                        Apply
                    </button>
                </form>

                <div v-else id="discountTag" style="margin-bottom: 0.5rem">
                    <span class="discount-tag">
                        <Tag :size="14" />
                        <span id="discountTagLabel"
                            >PKR {{ appliedDiscount.toFixed(2) }} Discount
                            Applied</span
                        >
                        <button @click="removeDiscount" title="Remove Discount">
                            <X :size="16" />
                        </button>
                    </span>
                </div>

                <!-- Charge button -->
                <button
                    class="charge-btn"
                    :disabled="cart.length === 0 || totalDue <= 0"
                >
                    <Check :size="18" />
                    <span>
                        Charge
                        <span>PKR {{ totalDue.toFixed(2) }}</span>
                        <div class="charge-btn-sub">Tap to enter payment</div>
                    </span>
                </button>

                <div class="sub-actions">
                    <button class="sub-btn" :disabled="cart.length === 0">
                        <Pause :size="12" />
                        Hold
                    </button>
                </div>
            </div>
        </aside>
    </div>
</template>
