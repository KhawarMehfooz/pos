<script setup lang="ts">
import { Check, Pause, Tag, X } from 'lucide-vue-next';
import type { CartItem, TaxSettings } from '@/types';

const { subtotal, appliedDiscount, totalDue, gstAmount, vatAmount, grandTotal, taxSettings, cart, discountInput, hasCartItems, discountAmount, canApplyDiscount } = defineProps<{
    subtotal: number;
    appliedDiscount: number;
    totalDue: number;
    gstAmount: number;
    vatAmount: number;
    grandTotal: number;
    taxSettings: TaxSettings;
    cart: CartItem[];
    discountInput: string;
    hasCartItems: boolean;
    discountAmount: number | null;
    canApplyDiscount: boolean;
}>()

const emit = defineEmits<{
    (e: 'apply-discount'): void
    (e: 'remove-discount'): void
    (e: 'update-discount-input', value: string): void
    (e: 'charge-payment'): void
    (e: 'hold-order'): void
}>();
</script>

<template>
        <!-- order summary -->
        <div class="cart-summary" id="cartSummary" style="display: block">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value" id="subtotalVal"
                    >{{ taxSettings.currency_symbol }} {{ subtotal.toFixed(2) }}</span
                >
            </div>
            <div
                v-if="appliedDiscount > 0"
                class="summary-row"
                id="discountRow"
            >
                <span class="summary-label">Discount</span>
                <span class="summary-value discount" id="discountVal"
                    >{{ taxSettings.currency_symbol }} {{ appliedDiscount.toFixed(2) }}</span
                >
            </div>

            <div v-if="taxSettings.gst_enabled" class="summary-row">
                <span class="summary-label">GST ({{ taxSettings.gst_percentage }}%)</span>
                <span class="summary-value">{{ taxSettings.currency_symbol }} {{ gstAmount.toFixed(2) }}</span>
            </div>

            <div v-if="taxSettings.vat_enabled" class="summary-row">
                <span class="summary-label">VAT ({{ taxSettings.vat_percentage }}%)</span>
                <span class="summary-value">{{ taxSettings.currency_symbol }} {{ vatAmount.toFixed(2) }}</span>
            </div>

            <hr class="summary-divider" />

            <div class="summary-total-row">
                <span class="summary-total-label">Total Due</span>
                <span class="summary-total-value" id="totalVal"
                    >{{ taxSettings.currency_symbol }} {{ grandTotal.toFixed(2) }}</span
                >
            </div>

            <!-- Discount -->
            <form
                v-if="!discountAmount"
                class="discount-row"
                @submit.prevent="emit('apply-discount')"
            >
                <div class="discount-input-wrap">
                    <input
                        :value="discountInput"
                        @input="emit('update-discount-input', ($event.target as HTMLInputElement).value)"
                        class="discount-input"
                        type="number"
                        min="0"
                        :max="subtotal"
                        step="1"
                        placeholder="Discount Amount"
                        autocomplete="off"
                        :disabled="!hasCartItems"
                    />
                </div>

                <button
                    type="submit"
                    class="apply-btn"
                    :disabled="!canApplyDiscount"
                >
                    Apply
                </button>
            </form>

            <div v-else id="discountTag" style="margin-bottom: 0.5rem">
                <span class="discount-tag">
                    <Tag :size="14" />
                    <span id="discountTagLabel"
                        >{{ taxSettings.currency_symbol }} {{ appliedDiscount.toFixed(2) }} Discount
                        Applied</span
                    >
                    <button @click="emit('remove-discount')" title="Remove Discount">
                        <X :size="16" />
                    </button>
                </span>
            </div>

            <!-- Charge button -->
            <button
                class="charge-btn"
                :disabled="cart.length === 0 || totalDue <= 0"
                @click="emit('charge-payment')"
            >
                <Check :size="18" />
                <span>
                    Charge
                    <span>{{ taxSettings.currency_symbol }} {{ grandTotal.toFixed(2) }}</span>
                    <div class="charge-btn-sub">Tap to enter payment</div>
                </span>
            </button>

            <div class="sub-actions">
                <button class="sub-btn" :disabled="cart.length === 0" @click="emit('hold-order')">
                    <Pause :size="12" />
                    Hold
                </button>
            </div>
        </div>
</template>