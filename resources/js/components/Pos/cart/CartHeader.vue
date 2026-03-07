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
                <button class="icon-btn btn-hold" title="Hold order">
                    <Pause />
                </button>
                <button
                    class="icon-btn btn-clear"
                    title="Clear cart"
                    @click="emit('clear-cart')"
                >
                    <Trash />
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
