<script setup lang="ts">
import { Loader, Pause, ShoppingBag, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import type { HeldTransaction } from '@/types';

const props = defineProps<{
    open: boolean;
    currencySymbol: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'resume', transaction: HeldTransaction): void;
    (e: 'deleted', id: number): void;
}>();

const transactions = ref<HeldTransaction[]>([]);
const loading = ref(false);
const deletingId = ref<number | null>(null);

async function fetchHeld() {
    loading.value = true;
    try {
        const res = await fetch('/transactions/held', {
            headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        transactions.value = data.transactions ?? [];
    } finally {
        loading.value = false;
    }
}

watch(
    () => props.open,
    (open) => {
        if (open) fetchHeld();
    },
);

async function deleteHold(transaction: HeldTransaction) {
    deletingId.value = transaction.id;
    try {
        const csrfToken = document.cookie
            .split('; ')
            .find((r) => r.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        await fetch(`/transactions/${transaction.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(csrfToken ?? ''),
            },
        });
        transactions.value = transactions.value.filter((t) => t.id !== transaction.id);
        emit('deleted', transaction.id);
    } finally {
        deletingId.value = null;
    }
}

async function resume(transaction: HeldTransaction) {
    deletingId.value = transaction.id;
    try {
        const csrfToken = document.cookie
            .split('; ')
            .find((r) => r.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        await fetch(`/transactions/${transaction.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(csrfToken ?? ''),
            },
        });
        emit('resume', transaction);
    } finally {
        deletingId.value = null;
    }
}

function timeAgo(dateStr: string): string {
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
}

function itemsPreview(transaction: HeldTransaction): string {
    return transaction.items
        .slice(0, 2)
        .map((i) => `${i.product_name} ×${i.quantity}`)
        .join(', ') + (transaction.items.length > 2 ? ` +${transaction.items.length - 2} more` : '');
}
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="hold-backdrop" @click.self="emit('close')">
            <div class="hold-modal">
                <!-- Header -->
                <div class="hold-modal-header">
                    <div class="hold-modal-title">
                        <Pause :size="15" />
                        Held Orders
                        <span v-if="transactions.length" class="hold-count">{{ transactions.length }}</span>
                    </div>
                    <button class="icon-btn" @click="emit('close')">
                        <X :size="14" />
                    </button>
                </div>

                <!-- Body -->
                <div class="hold-modal-body">
                    <!-- Loading -->
                    <div v-if="loading" class="hold-state-center">
                        <Loader :size="22" class="hold-spinner" />
                        <span>Loading held orders…</span>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="!transactions.length" class="hold-state-center">
                        <div class="cart-empty-icon">
                            <ShoppingBag :size="24" />
                        </div>
                        <p>No held orders</p>
                    </div>

                    <!-- List -->
                    <template v-else>
                        <div
                            v-for="t in transactions"
                            :key="t.id"
                            class="hold-order-row"
                            :class="{ 'hold-order-row--loading': deletingId === t.id }"
                        >
                            <div class="hold-order-meta">
                                <span class="hold-order-id">#{{ t.id }}</span>
                                <span class="hold-order-customer">
                                    {{ t.customer?.customer_name ?? 'Walk-in' }}
                                </span>
                                <span class="hold-order-time">{{ timeAgo(t.created_at) }}</span>
                            </div>

                            <div class="hold-order-items">{{ itemsPreview(t) }}</div>

                            <div class="hold-order-footer">
                                <span class="hold-order-total">
                                    {{ currencySymbol }} {{ t.grand_total.toFixed(2) }}
                                </span>
                                <div class="hold-order-actions">
                                    <button
                                        class="btn btn-hold-resume"
                                        :disabled="deletingId === t.id"
                                        @click="resume(t)"
                                    >
                                        Resume
                                    </button>
                                    <button
                                        class="icon-btn btn-delete-hold"
                                        :disabled="deletingId === t.id"
                                        title="Delete hold"
                                        @click="deleteHold(t)"
                                    >
                                        <Trash2 :size="13" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.hold-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(28, 25, 23, 0.5);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 3rem;
    z-index: 100;
    backdrop-filter: blur(2px);
}

.hold-modal {
    background: var(--surface-card, #fff);
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(120, 53, 15, 0.2);
    display: flex;
    flex-direction: column;
    width: 480px;
    max-height: 80vh;
    overflow: hidden;
}

.hold-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1rem;
    border-bottom: 1px solid var(--border-subtle, #e7e5e4);
    flex-shrink: 0;
}

.hold-modal-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
}

.hold-count {
    background: var(--primary-500, #f59e0b);
    color: #fff;
    font-family: 'DM Mono', monospace;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    min-width: 20px;
    text-align: center;
}

.hold-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem;
    scrollbar-width: thin;
    scrollbar-color: var(--border-subtle) transparent;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.hold-state-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 3rem 1rem;
    color: var(--text-muted);
    font-size: 0.85rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
.hold-spinner {
    animation: spin 1s linear infinite;
    color: var(--primary-500);
}

.hold-order-row {
    border: 1px solid var(--border-subtle, #e7e5e4);
    border-radius: 10px;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    transition: border-color 0.15s, background 0.15s;
}
.hold-order-row:hover {
    border-color: var(--primary-300, #fcd34d);
    background: var(--primary-50, #fffbeb);
}
.hold-order-row--loading {
    opacity: 0.5;
    pointer-events: none;
}

.hold-order-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.hold-order-id {
    font-family: 'DM Mono', monospace;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--primary-700, #b45309);
    background: var(--primary-100, #fef3c7);
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
}
.hold-order-customer {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-primary);
    flex: 1;
}
.hold-order-time {
    font-family: 'DM Mono', monospace;
    font-size: 0.68rem;
    color: var(--text-muted);
}

.hold-order-items {
    font-size: 0.78rem;
    color: var(--text-secondary);
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.hold-order-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.1rem;
}
.hold-order-total {
    font-family: 'DM Mono', monospace;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary);
}
.hold-order-actions {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.btn-hold-resume {
    padding: 0.3rem 0.85rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: var(--border-radius, 6px);
    border: 1px solid var(--primary-400, #fbbf24);
    background: var(--primary-500, #f59e0b);
    color: #fff;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}
.btn-hold-resume:hover:not(:disabled) {
    background: var(--primary-600, #d97706);
    border-color: var(--primary-600, #d97706);
}
.btn-hold-resume:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-delete-hold:hover {
    border-color: hsl(var(--destructive) / 0.4);
    background: #fff1f2;
    color: hsl(var(--destructive));
}
</style>
