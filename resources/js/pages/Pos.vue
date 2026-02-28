<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import Header from '@/components/Pos/Header.vue';
import type { Paginated, Product } from '@/types';

const page = usePage<{
    products: Paginated<Product>;
}>();

const products = page.props.products;
</script>

<template>
    <Header />

    <section
        class="mx-auto my-[0.9rem] grid max-w-400 grid-cols-[minmax(0,70%)_minmax(0,30%)] gap-4"
    >
        <!-- Products -->
        <div>
            <ul
                class="grid grid-cols-[repeat(auto-fill,minmax(140px,1fr))] gap-[0.9rem]"
                role="list"
            >
                <li v-for="product in products.data" :key="product.id">
                    <article class="product-card" title="Add to cart">
                        <figure class="product-thumb">
                            <img
                                :src="product.product_image_url"
                                :alt="product.product_name"
                            />
                        </figure>

                        <h3 class="product-name" :title="product.product_name">
                            {{ product.product_name }}
                        </h3>

                        <p class="product-sku">
                            {{ product.sku }}
                        </p>

                        <footer class="product-row">
                            <span class="product-price">
                                <template v-if="product.sale_price">
                                    <span class="sale-price">
                                        PKR {{ product.sale_price }}
                                    </span>
                                    <span
                                        class="price-old retail-price block line-through"
                                    >
                                        PKR {{ product.retail_price }}
                                    </span>
                                </template>
                                <template v-else>
                                    PKR {{ product.retail_price }}
                                </template>
                            </span>

                            <span
                                class="stock-dot"
                                :class="
                                    !product.track_stock ||
                                    product.stock_quantity > 0
                                        ? 'stock-in'
                                        : 'stock-out'
                                "
                                :title="
                                    !product.track_stock
                                        ? 'Stock Not Tracked'
                                        : product.stock_quantity > 0
                                          ? 'In Stock'
                                          : 'Out of Stock'
                                "
                                aria-hidden="true"
                            ></span>
                        </footer>
                    </article>
                </li>
            </ul>
        </div>

        <!-- Cart (30%) -->
        <aside class="cart-panel">
            <!-- cart goes here -->
        </aside>
    </section>
</template>
