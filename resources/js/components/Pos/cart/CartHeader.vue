<script setup lang="ts">
import { Pause, Trash } from 'lucide-vue-next';
import { computed} from 'vue';
import type { Customer } from '@/types';

const props = defineProps<{
    customers: Customer[];
    search: string;
    selectedCustomer: Customer | null;
}>();

const emit = defineEmits<{
    (e: 'update:search', value: string): void;
    (e: 'select-customer', customer: Customer): void;
    (e: 'search-customer'): void;
    (e: 'clear-cart'): void;
    (e: 'open-held-orders'): void;
}>();

const walkInCustomer: Customer = {
    id: 0,
    customer_name: 'Walk-in',
    customer_email: null,
    customer_phone: null,
};

const customersWithWalkIn = computed(() => [
    walkInCustomer,
    ...props.customers,
]);

const selected = computed({
    get() {
        return props.selectedCustomer ?? walkInCustomer;
    },
    set(customer: Customer) {
        emit('select-customer', customer);
    },
});

const onSearch = (value: string) => {
    // emit event to parent
    emit('update:search', value);
    emit('search-customer'); // trigger API
};

</script>
<template>
    <div class="cart-header">
        <div class="cart-header-row">
            <div class="cart-title">Cart</div>
            <div class="cart-actions">
                <button class="sub-btn btn-hold" @click="emit('open-held-orders')">
                    <Pause :size="12" />
                    Held Orders
                </button>
                <button class="sub-btn btn-clear" @click="emit('clear-cart')">
                    <Trash :size="12" />
                    Clear
                </button>
            </div>
        </div>

        <div class="">
            <v-select
                class="c-selector"
                :options="customersWithWalkIn"
                label="customer_name"
                v-model="selected"
                @search="onSearch"
                :filterable="false"
            />
        </div>
    </div>
</template>

<style>
.c-selector .vs__dropdown-toggle {
    border-radius: var(--border-radius) !important;
    border: 1px dashed hsl(var(--border)) !important;
    background: hsl(var(--muted)) !important;
    cursor: pointer !important;
    transition: all 0.15s !important;
}
.c-selector ul,
.c-selector ul li,
.c-selector .vs__selected {
    font-family: 'DM Mono';
}
</style>
