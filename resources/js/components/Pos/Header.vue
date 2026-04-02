<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { LayoutDashboard, LogOut, Settings } from 'lucide-vue-next';
import { logout } from '@/routes';

defineProps<{
    storeName: string;
}>();

const handleLogout = () => {
    router.flushAll();
    router.visit(logout(), { method: 'post' });
};
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
                    {{ storeName }}
                </div>

                <div class="header-right">
                    <a href="/admin/settings-page" class="nav-btn">
                        <Settings />
                        Settings
                    </a>

                    <button class="nav-btn danger" @click="handleLogout">
                        <LogOut />
                        Logout
                    </button>
                </div>
            </div>
        </header>
    </div>
</template>