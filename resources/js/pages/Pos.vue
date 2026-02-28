<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import Header from '@/components/Pos/Header.vue';
import ProductCard from '@/components/Pos/ProductCard.vue';
import type { Paginated, Product } from '@/types';
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';
import {
    Banknote,
    Barcode,
    Check,
    Minus,
    Pause,
    Plus,
    ShoppingBag,
    ShoppingCart,
    Tag,
    Tally4,
    Trash,
    UserRound,
    X,
} from 'lucide-vue-next';

const page = usePage<{
    products: Paginated<Product>;
}>();

const products = page.props.products;
</script>

<template>
    <Header />

    <div class="pos-layout">
        <div class="product-panel">
            <!-- toolbar -->
            <div class="panel-toolbar">
                <div class="search-wrap">
                    <svg
                        class="search-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input
                        class="search-input"
                        type="text"
                        placeholder="Search products or scan barcode…"
                        id="searchInput"
                        oninput="filterProducts(this.value)"
                    />
                </div>
                <button
                    class="toolbar-btn"
                    id="barcodeBtn"
                >
                    <Barcode />
                    Barcode
                </button>
            </div>

            <CategoryTabs />

            <section class="">
                <!-- Products -->
                <div class="product-grid-wrap">
                    <ul
                        class="grid grid-cols-[repeat(auto-fill,minmax(140px,1fr))] gap-[0.9rem]"
                        role="list"
                    >
                        <li v-for="product in products.data" :key="product.id">
                            <ProductCard :product="product" />
                        </li>
                    </ul>
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
                        >Discount
                        <span id="discountLabel">(10%)</span></span
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
                    <button class="apply-btn">
                        Apply
                    </button>
                </div>
                <div
                    id="discountTag"
                    style=" margin-bottom: 0.5rem"
                >
                    <span class="discount-tag">
                        <Tag :size="14" /> <span id="discountTagLabel">10% Discount Applied</span>
                        <button title="Remove Discount" > <X :size="16" /></button>
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
