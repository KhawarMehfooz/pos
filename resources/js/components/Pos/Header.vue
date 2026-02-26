<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { LayoutDashboard, LogOut, Settings } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { Category } from '@/types';

const page = usePage();

const categories = ref<Category[]>(page.props.categories as Category[]);

const activeParent = ref<Category | null>(null);

const parentCategories = computed(() =>
    categories.value.filter((category) => category.parent_id === null),
);
</script>

<template>
    <div class="header-wrapper">
        <header class="header">
            <div class="header-inner">
                <div class="header-left">
                    <a href="/admin" class="nav-btn">
                        <LayoutDashboard />
                        Dashboard
                    </a>
                </div>

                <div class="store-name">
                    <span class="logo-dot"></span>
                    POINT OF SALE SYSTEM
                </div>

                <div class="header-right">
                    <a href="#" class="nav-btn">
                        <Settings />
                        Settings
                    </a>

                    <button class="nav-btn danger">
                        <LogOut />
                        Logout
                    </button>
                </div>
            </div>

            <nav class="categories-wrapper">
                <ul
                    class="no-scrollbar flex flex-nowrap items-center gap-4 overflow-x-auto p-1"
                >
                    <li
                        v-for="category in parentCategories"
                        :key="category.id"
                        class="min-w-fit cursor-pointer rounded-[6px] bg-amber-50 px-4 py-1 text-xs whitespace-nowrap border border-dashed border-amber-200 hover:bg-amber-100 hover:border-amber-300"
                        @mouseenter="activeParent = category"
                        @click="activeParent = category"
                    >
                        {{ category.category_name }}
                    </li>
                </ul>
            </nav>

            <Transition name="category-reveal">
                <div
                    v-if="activeParent && activeParent.children?.length"
                    class="origin-top"
                    @mouseleave="activeParent = null"
                >
                    <div class="border-t border-t-[#e7e5e4]">
                        <ul class="flex flex-wrap gap-2 py-2">
                            <li
                                v-for="child in activeParent.children"
                                :key="child.id"
                                class="min-w-fit cursor-pointer rounded-[6px] bg-amber-50 px-2 py-1 text-xs whitespace-nowrap border border-dashed border-amber-200 hover:bg-amber-100 hover:border-amber-300"
                            >
                                {{ child.category_name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </Transition>
        </header>
    </div>
</template>

<style scoped>
.category-reveal-enter-active {
    transition: all 180ms cubic-bezier(0.4, 0, 0.2, 1);
}
.category-reveal-enter-from {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
}

.category-reveal-leave-active {
    transition: all 140ms cubic-bezier(0.4, 0, 1, 1);
}
.category-reveal-leave-to {
    opacity: 0;
    transform: translateY(-4px) scale(0.98);
}
</style>
