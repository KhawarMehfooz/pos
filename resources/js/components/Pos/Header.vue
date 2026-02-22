<script setup lang="ts">
import { LayoutDashboard, LogOut, Settings } from 'lucide-vue-next';
</script>

<template>
    <header class="header">
        <div class="header-inner">
            <div class="header-left">
                <a href="/admin" class="nav-btn">
                    <LayoutDashboard />
                    Dashboard
                </a>
            </div>
            <div class="store-name">
                <span class="logo-dot"></span>POINT OF SALE SYSTEM
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
    </header>
</template>
