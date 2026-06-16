<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, ref, watch } from 'vue';
import CartPanel from '@/components/Pos/cart/CartPanel.vue';
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';
import Header from '@/components/Pos/Header.vue';
import NumPad from '@/components/Pos/NumPad.vue';
import PosToolbar from '@/components/Pos/PosToolbar.vue';
import ProductGrid from '@/components/Pos/ProductGrid.vue';
import type {
    CartItem,
    Category,
    Customer,
    Paginated,
    Product,
    TaxSettings,
    TransactionItem,
} from '@/types';

const props = defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    activeCategoryId?: number;
    customers: Customer[];
    taxSettings: TaxSettings;
    storeName: string;
}>();

const searchTerm = ref('');
const activeCategoryId = ref(props.activeCategoryId ?? 0);
const discountInput = ref('');
const discountAmount = ref<number | null>(null);
const customerSearch = ref('');
const selectedCustomer = ref<Customer | null>(null);

const emit = defineEmits<{
    (e: 'charge-payment'): void;
}>();

function searchCustomers(customerSearch: string) {
    router.get(
        '/pos',
        {
            category: activeCategoryId.value,
            search: searchTerm.value,
            customer_search: customerSearch,
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['customers', 'filters'],
        },
    );
}

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
const debouncedCustomerSearch = debounce(searchCustomers, 500);

const cart = ref<CartItem[]>([]);

const isNumpadOpen = ref(false);
const lastTransactionId = ref<number | null>(null);

function openNumpad() {
    isNumpadOpen.value = true;
}

function closeNumpad() {
    isNumpadOpen.value = false;
}

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

const gstAmount = computed(() => {
    if (!props.taxSettings.gst_enabled) return 0;
    return Math.round(totalDue.value * props.taxSettings.gst_percentage) / 100;
});

const vatAmount = computed(() => {
    if (!props.taxSettings.vat_enabled) return 0;
    return Math.round(totalDue.value * props.taxSettings.vat_percentage) / 100;
});

const grandTotal = computed(() => totalDue.value + gstAmount.value + vatAmount.value);

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

function clearCart() {
    cart.value = [];

    discountAmount.value = null;
    discountInput.value = '';
}

async function processTransaction(status: 'hold' | 'completed', paidAmount = 0) {
    if (!hasCartItems.value) return alert('Cart is empty');

    const payload = {
        customer_id: selectedCustomer.value?.id || null,
        discount: discountAmount.value || 0,
        status,
        paid_amount: paidAmount,
        items: cart.value.map((item): TransactionItem => {
            const product = item.product;
            const price = product.sale_price ?? product.retail_price;

            return {
                product_id: product.id,
                product_name: product.product_name,
                product_price: price,
                quantity: item.quantity,
                total: price * item.quantity,
                sku: product.sku ?? null,
                barcode: product.barcode ?? null,
                product_image: product.product_image_url ?? null,
            };
        }),
    };

    try {
        const csrfToken = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        const response = await fetch('/transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(csrfToken ?? ''),
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message ?? 'Request failed');
        lastTransactionId.value = data.transaction_id;
        closeNumpad();
        clearCart();
        selectedCustomer.value = null;
        customerSearch.value = '';
        discountAmount.value = null;
        discountInput.value = '';
    } catch (error) {
        console.error(error);
        alert('Failed to create transaction.');
    }
}

function closeReceipt() {
    lastTransactionId.value = null;
}

function printReceipt() {
    const win = window.open(`/transactions/${lastTransactionId.value}/receipt`, '_blank', 'width=400,height=600');
    if (win) {
        win.focus();
    }
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
    <Header :store-name="props.storeName" />

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
            :gstAmount="gstAmount"
            :vatAmount="vatAmount"
            :grandTotal="grandTotal"
            :taxSettings="taxSettings"
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
            :customers="customers"
            :customer-search="customerSearch"
            :selected-customer="selectedCustomer"
            @update-customer-search="customerSearch = $event"
            @select-customer="selectedCustomer = $event"
            @search-customer="debouncedCustomerSearch(customerSearch)"
            @charge-payment="openNumpad"
        />
    </div>

    <NumPad
        :total-due="grandTotal"
        :open="isNumpadOpen"
        @close="closeNumpad"
        @process-transaction="processTransaction"
    />

    <!-- Receipt Modal -->
    <Teleport to="body">
        <div v-if="lastTransactionId" class="receipt-modal-backdrop" @click.self="closeReceipt">
            <div class="receipt-modal">
                <div class="receipt-modal-actions">
                    <button class="btn btn-outline" @click="printReceipt">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Print
                    </button>
                    <button class="btn btn-ghost" @click="closeReceipt">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Close
                    </button>
                </div>
                <iframe :src="`/transactions/${lastTransactionId}/receipt`" class="receipt-frame" sandbox="allow-same-origin allow-scripts allow-popups" />
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.receipt-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(28, 25, 23, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    backdrop-filter: blur(2px);
}

.receipt-modal {
    background: var(--surface-card, #fff);
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(120, 53, 15, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    width: 500px;
    height: 90vh;
    max-height: 90vh;
}

.receipt-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-subtle, #e7e5e4);
}

.receipt-frame {
    width: 100%;
    flex: 1;
    border: none;
    min-height: 500px;
}
</style>
