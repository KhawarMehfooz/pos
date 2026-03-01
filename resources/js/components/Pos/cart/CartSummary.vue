<template>
        <!-- order summary -->
        <div class="cart-summary" id="cartSummary" style="display: block">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value" id="subtotalVal"
                    >PKR {{ subtotal.toFixed(2) }}</span
                >
            </div>
            <div
                v-if="appliedDiscount > 0"
                class="summary-row"
                id="discountRow"
            >
                <span class="summary-label">Discount </span>
                <span class="summary-value discount" id="discountVal"
                    >PKR {{ appliedDiscount.toFixed(2) }}</span
                >
            </div>

            <hr class="summary-divider" />

            <div class="summary-total-row">
                <span class="summary-total-label">Total Due</span>
                <span class="summary-total-value" id="totalVal"
                    >PKR {{ totalDue.toFixed(2) }}</span
                >
            </div>

            <!-- Discount -->
            <form
                v-if="!discountAmount"
                class="discount-row"
                @submit.prevent="applyDiscount"
            >
                <div class="discount-input-wrap">
                    <input
                        v-model="discountInput"
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
                        >PKR {{ appliedDiscount.toFixed(2) }} Discount
                        Applied</span
                    >
                    <button @click="removeDiscount" title="Remove Discount">
                        <X :size="16" />
                    </button>
                </span>
            </div>

            <!-- Charge button -->
            <button
                class="charge-btn"
                :disabled="cart.length === 0 || totalDue <= 0"
            >
                <Check :size="18" />
                <span>
                    Charge
                    <span>PKR {{ totalDue.toFixed(2) }}</span>
                    <div class="charge-btn-sub">Tap to enter payment</div>
                </span>
            </button>

            <div class="sub-actions">
                <button class="sub-btn" :disabled="cart.length === 0">
                    <Pause :size="12" />
                    Hold
                </button>
            </div>
        </div>
</template>