<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import {
    Barcode,
    Check,
    Minus,
    Pause,
    Plus,
    Search,
    ShoppingCart,
    Tag,
    Trash,
    UserRound,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';
import Header from '@/components/Pos/Header.vue';
import ProductCard from '@/components/Pos/ProductCard.vue';
import type { Category, Paginated, Product } from '@/types';

const props = defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    activeCategoryId?: number;
}>();

const searchTerm = ref('');
const activeCategoryId = ref(props.activeCategoryId ?? 0);

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

function goToPage(url: string | null) {
    if (!url) return;

    router.get(
        url,
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['products'],
        },
    );
}

function formatLabel(label: string) {
    if (label.includes('Previous')) return 'Prev';
    if (label.includes('Next')) return 'Next';
    return label;
}
</script>

<template>
    <Header />

    <div class="pos-layout">
        <div class="product-panel">
            <!-- toolbar -->
            <div class="panel-toolbar">
                <div class="search-wrap">
                    <Search class="search-icon" />
                    <input
                        v-model="searchTerm"
                        @input="debouncedSearch"
                        class="search-input"
                        type="text"
                        placeholder="Search products or scan barcode…"
                        id="searchInput"
                    />
                </div>
                <button class="toolbar-btn" id="barcodeBtn">
                    <Barcode />
                    Barcode
                </button>
            </div>

            <CategoryTabs
                :categories="categories"
                :activeCategoryId="activeCategoryId"
                @update:category="filterByCategory"
            />

            <section class="product-grid-wrap">
                <ul
                    class="grid grid-cols-[repeat(auto-fill,minmax(140px,1fr))] gap-[0.9rem]"
                    role="list"
                >
                    <li v-for="product in products.data" :key="product.id">
                        <ProductCard :product="product" />
                    </li>
                </ul>

                <hr
                    class="my-4 h-px border-0"
                    style="background: hsl(var(--border) / 0.6)"
                />
                <!-- Pagination -->
                <div class="flex flex-wrap items-center justify-center">
                    <button
                        v-for="link in products.links"
                        :key="link.label"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        class="pagination-btn"
                        :class="{
                            'is-active': link.active,
                            'cursor-not-allowed opacity-50': !link.url,
                        }"
                    >
                        {{ formatLabel(link.label) }}
                    </button>
                </div>
            </section>
        </div>

        <!-- Cart -->
        <aside class="cart-panel">
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
                <div class="cart-empty" id="cartEmpty" style="display: none">
                    <div class="cart-empty-icon">
                        <ShoppingCart />
                    </div>

                    <p>
                        <strong>Cart is empty</strong><br />Click a product or
                        scan a barcode to add items
                    </p>
                </div>

                <!-- cart item -->
                <div class="cart-item">
                    <div class="cart-item-thumb">☕</div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">Arabica Blend 250g</div>
                        <div class="cart-item-meta">
                            <span class="unit-price">PKR 12.99</span> / unit ·
                            BEV-0042
                        </div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="qty-control">
                            <button class="qty-btn minus">
                                <Minus />
                            </button>
                            <input
                                type="number"
                                class="qty-value"
                                min="0"
                                step="1"
                            />
                            <button class="qty-btn">
                                <Plus />
                            </button>
                        </div>
                        <div class="cart-item-total">$12.99</div>
                    </div>
                    <button class="item-delete">
                        <X />
                    </button>
                </div>
            </div>

            <!-- order summary -->
            <div class="cart-summary" id="cartSummary" style="display: block">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="subtotalVal">$70.80</span>
                </div>
                <div class="summary-row" id="discountRow" style="display: none">
                    <span class="summary-label"
                        >Discount <span id="discountLabel"></span
                    ></span>
                    <span class="summary-value discount" id="discountVal"
                        >−$0.00</span
                    >
                </div>

                <div class="summary-row" id="discountRow" style="display: flex">
                    <span class="summary-label"
                        >Discount <span id="discountLabel">(10%)</span></span
                    >
                    <span class="summary-value discount" id="discountVal"
                        >−$7.08</span
                    >
                </div>

                <div class="summary-row">
                    <span class="summary-label">Tax (8%)</span>
                    <span class="summary-value" id="taxVal">$5.66</span>
                </div>

                <hr class="summary-divider" />

                <div class="summary-total-row">
                    <span class="summary-total-label">Total Due</span>
                    <span class="summary-total-value" id="totalVal"
                        >$76.46</span
                    >
                </div>

                <!-- Discount -->
                <div class="discount-row">
                    <div class="discount-input-wrap">
                        <input
                            class="discount-input"
                            type="text"
                            id="discountInput"
                            placeholder="Discount Amount"
                        />
                    </div>
                    <button class="apply-btn">Apply</button>
                </div>
                <div id="discountTag" style="margin-bottom: 0.5rem">
                    <span class="discount-tag">
                        <Tag :size="14" />
                        <span id="discountTagLabel">10% Discount Applied</span>
                        <button title="Remove Discount">
                            <X :size="16" />
                        </button>
                    </span>
                </div>

                <!-- Charge button -->
                <button class="charge-btn">
                    <Check :size="18" />
                    <span>
                        Charge <span id="chargeTotalBtn">$76.46</span>
                        <div class="charge-btn-sub">Tap to enter payment</div>
                    </span>
                </button>

                <div class="sub-actions">
                    <button class="sub-btn">
                        <Pause :size="12" />
                        Hold
                    </button>
                </div>
            </div>
        </aside>
    </div>
</template>
