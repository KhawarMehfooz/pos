<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import Header from '@/components/Pos/Header.vue';
import ProductCard from '@/components/Pos/ProductCard.vue';
import type { Paginated, Product } from '@/types';
import CategoryTabs from '@/components/Pos/CategoryTabs.vue';

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
                    onclick="toggleBarcode()"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="5" width="2" height="14" />
                        <rect x="7" y="5" width="1" height="14" />
                        <rect x="10" y="5" width="2" height="14" />
                        <rect x="14" y="5" width="1" height="14" />
                        <rect x="17" y="5" width="2" height="14" />
                        <rect x="21" y="5" width="1" height="14" />
                    </svg>
                    Barcode
                </button>
                <button class="toolbar-btn">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                    Filter
                </button>
            </div>

            <CategoryTabs />

            <section
                class=""
            >
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

                <!-- Cart -->
                <aside class="cart-panel">
                    <!-- cart goes here -->
                </aside>
            </section>
        </div>
    </div>
</template>
