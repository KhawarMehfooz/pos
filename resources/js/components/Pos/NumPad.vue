<script setup lang="ts">
import { Banknote, CreditCard, Delete, Dot } from 'lucide-vue-next';
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps<{
    totalDue: number;
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'process-transaction', status: 'hold' | 'completed', paidAmount: number, paymentMethod: 'cash' | 'card'): void;
    (e: 'close'): void;
}>();

const value = ref('');
const paymentMethod = ref<'cash' | 'card'>('cash');

const exactAmount = computed(() => props.totalDue ?? 0);

const formattedValue = computed(() => {
    if (!value.value) return '';
    const num = parseFloat(value.value);
    return isNaN(num) ? '' : num.toFixed(2);
});

function selectMethod(method: 'cash' | 'card') {
    paymentMethod.value = method;
    if (method === 'card') {
        value.value = exactAmount.value.toString();
    }
}

// Reset state when numpad opens
watch(() => props.open, (open) => {
    if (open) {
        value.value = '';
        paymentMethod.value = 'cash';
    }
});

function numpadKey(key: string) {
    if (key === '.' && value.value.includes('.')) return;
    if (!value.value && key === '.') { value.value = '0.'; return; }
    if (value.value === '0' && key !== '.') { value.value = key; return; }
    value.value += key;
}

function numpadDel() {
    value.value = value.value.slice(0, -1);
}

function numpadExact() {
    value.value = exactAmount.value.toString();
}

function handleKeyboard(e: KeyboardEvent) {
    if (/^[0-9]$/.test(e.key)) numpadKey(e.key);
    if (e.key === '.') numpadKey('.');
    if (e.key === 'Backspace') numpadDel();
    if (e.key === 'Enter' && value.value && Number(value.value) >= props.totalDue) {
        emit('process-transaction', 'completed', Number(value.value), paymentMethod.value);
    }
}

onMounted(() => window.addEventListener('keydown', handleKeyboard));
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeyboard));
</script>

<template>
    <div
        class="numpad-overlay"
        id="numpadOverlay"
        :class="{ open: open }"
        @click.self="emit('close')"
    >
        <div class="numpad-modal">
            <!-- Payment method toggle -->
            <div class="payment-method-toggle">
                <button
                    class="payment-method-btn"
                    :class="{ active: paymentMethod === 'cash' }"
                    @click="selectMethod('cash')"
                >
                    <Banknote :size="15" />
                    Cash
                </button>
                <button
                    class="payment-method-btn"
                    :class="{ active: paymentMethod === 'card' }"
                    @click="selectMethod('card')"
                >
                    <CreditCard :size="15" />
                    Card
                </button>
            </div>

            <!-- Display -->
            <div class="numpad-display">
                <div class="numpad-label">
                    {{ paymentMethod === 'cash' ? 'Cash Tendered' : 'Card Payment' }}
                </div>
                <div class="numpad-value" :class="{ 'numpad-value--empty': !value }">
                    {{ formattedValue || '—' }}
                </div>
            </div>

            <!-- Keys -->
            <div class="numpad-grid">
                <button class="numpad-key" @click="numpadKey('7')">7</button>
                <button class="numpad-key" @click="numpadKey('8')">8</button>
                <button class="numpad-key" @click="numpadKey('9')">9</button>

                <button class="numpad-key" @click="numpadKey('4')">4</button>
                <button class="numpad-key" @click="numpadKey('5')">5</button>
                <button class="numpad-key" @click="numpadKey('6')">6</button>

                <button class="numpad-key" @click="numpadKey('1')">1</button>
                <button class="numpad-key" @click="numpadKey('2')">2</button>
                <button class="numpad-key" @click="numpadKey('3')">3</button>

                <button class="numpad-key" @click="numpadKey('0')">0</button>

                <button
                    class="numpad-key flex items-center justify-center"
                    @click="numpadKey('.')"
                >
                    <Dot />
                </button>

                <button class="numpad-key key-del" @click="numpadDel">
                    <Delete />
                </button>

                <hr class="col-span-3 my-4 h-0 border-t border-dashed border-gray-300" />

                <button
                    class="numpad-key"
                    @click="numpadExact"
                    style="grid-column: 1/3"
                >
                    Exact: {{ exactAmount.toFixed(2) }}
                </button>

                <button
                    class="numpad-key key-action"
                    @click="emit('process-transaction', 'completed', Number(value), paymentMethod)"
                    :disabled="!value || Number(value) < props.totalDue"
                >
                    Pay
                </button>
            </div>

            <button class="numpad-close" @click.self="emit('close')">
                Cancel
            </button>
        </div>
    </div>
</template>

<style>
.numpad-key:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.numpad-value--empty {
    color: hsl(var(--muted-fg));
}

.payment-method-toggle {
    display: flex;
    gap: 0.35rem;
    margin-bottom: 1rem;
}
.payment-method-btn {
    flex: 1;
    padding: 0.55rem;
    border-radius: var(--border-radius);
    border: 1px dashed hsl(var(--border));
    background: hsl(var(--muted));
    font-family: 'DM Sans', sans-serif;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    color: hsl(var(--muted-fg));
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all 0.15s;
}
.payment-method-btn:hover {
    border-color: hsl(var(--primary) / 0.5);
    background: hsl(var(--accent));
    color: hsl(var(--accent-fg));
}
.payment-method-btn.active {
    background: hsl(var(--primary));
    border-color: hsl(var(--primary));
    border-style: solid;
    color: #fff;
}
</style>
