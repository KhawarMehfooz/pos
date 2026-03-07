<script setup lang="ts">
import { Delete, Dot } from 'lucide-vue-next';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps<{
    totalDue: number;
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'process-transaction', status: 'hold' | 'completed', paidAmount: number): void;
    (e: 'close'): void;
}>();

const value = ref('0');

const exactAmount = computed(() => props.totalDue ?? 0);

const formattedValue = computed(() => {
    const num = parseFloat(value.value || '0');
    return `${num.toFixed(2)}`;
});

function numpadKey(key: string) {
    if (key === '.' && value.value.includes('.')) return;

    if (value.value === '0' && key !== '.') {
        value.value = key;
    } else {
        value.value += key;
    }
}

function numpadDel() {
    value.value = value.value.slice(0, -1);
    if (!value.value) value.value = '0';
}

function numpadExact() {
    value.value = exactAmount.value.toString();
}

function processPayment() {
    const amount = parseFloat(value.value);
    console.log('Processing payment:', amount);
}

function handleKeyboard(e: KeyboardEvent) {
    if (/^[0-9]$/.test(e.key)) {
        numpadKey(e.key);
    }

    if (e.key === '.') {
        numpadKey('.');
    }

    if (e.key === 'Backspace') {
        numpadDel();
    }

    if (e.key === 'Enter') {
        processPayment();
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeyboard);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyboard);
});

</script>

<template>
    <div
        class="numpad-overlay"
        id="numpadOverlay"
        :class="{ open: open }"
        @click.self="emit('close')"
    >
        <div class="numpad-modal">
            <div class="numpad-display">
                <div class="numpad-label">Cash Tendered</div>
                <div class="numpad-value">{{ formattedValue }}</div>
            </div>

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

                <hr
                    class="col-span-3 my-4 h-0 border-t border-dashed border-gray-300"
                />

                <button
                    class="numpad-key"
                    @click="numpadExact"
                    style="grid-column: 1/3"
                >
                    Exact: {{ exactAmount.toFixed(2) }}
                </button>

                <button
                    class="numpad-key key-action"
                    @click="emit('process-transaction', 'completed', Number(value))"
                    :disabled="Number(value) < props.totalDue"
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
</style>
