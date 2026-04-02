<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;600;700&family=Sora:wght@700&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
<style>
  html, body {
    height: auto !important;
  }
  body {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center !important;
    align-items: flex-start !important;
    background: #f5f5f4 !important;
    padding: 1.5rem;
    min-height: 100vh;
  }
</style>
</head>
<body>
<div class="receipt">

  {{-- Header / Store Info --}}
  <div class="receipt-header">
    <div class="receipt-store">{{ $setting->store_name }}</div>
    @if($setting->store_address || $setting->store_phone)
    <div class="receipt-store-sub">
      @if($setting->store_address){{ $setting->store_address }}<br>@endif
      @if($setting->store_phone)Tel: {{ $setting->store_phone }}@endif
    </div>
    @endif
    <div class="receipt-date">
      {{ $transaction->created_at->format('d M Y, h:i A') }}
    </div>
    <div>
      <span class="receipt-status status-{{ $transaction->status }}">
        {{ ucfirst($transaction->status) }}
      </span>
    </div>
  </div>

  <hr class="receipt-divider">

  {{-- Transaction Meta --}}
  <div class="receipt-meta">Receipt# <span>#{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}</span></div>
  @if($transaction->customer)
    <div class="receipt-meta">Customer: <span>{{ $transaction->customer->customer_name }}</span></div>
  @endif

  <hr class="receipt-divider">

  {{-- Items --}}
  <table class="receipt-items">
    <thead>
      <tr>
        <th>Item</th>
        <th style="text-align:right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transaction->items as $item)
        <tr>
          <td>
            <span class="item-name">{{ $item->product_name }}</span>
            <span class="item-qty-price">{{ $item->quantity }} × {{ number_format($item->product_price, 2) }}</span>
          </td>
          <td>{{ number_format($item->total, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <hr class="receipt-divider">

  {{-- Totals --}}
  @php
    $cur     = $setting->currency_symbol;
    $preTax  = $transaction->subtotal - $transaction->discount;
    $gstAmount = $setting->gst_enabled ? round($preTax * $setting->gst_percentage / 100, 2) : 0;
    $vatAmount = $setting->vat_enabled ? round($preTax * $setting->vat_percentage / 100, 2) : 0;
  @endphp

  <div class="receipt-row">
    <span>Subtotal</span>
    <span>{{ $cur }} {{ number_format($transaction->subtotal, 2) }}</span>
  </div>

  @if($transaction->discount > 0)
    <div class="receipt-row discount">
      <span>Discount</span>
      <span>- {{ $cur }} {{ number_format($transaction->discount, 2) }}</span>
    </div>
  @endif

  @if($setting->gst_enabled)
    <div class="receipt-row tax">
      <span>GST ({{ $setting->gst_percentage }}%)</span>
      <span>{{ $cur }} {{ number_format($gstAmount, 2) }}</span>
    </div>
  @endif

  @if($setting->vat_enabled)
    <div class="receipt-row tax">
      <span>VAT ({{ $setting->vat_percentage }}%)</span>
      <span>{{ $cur }} {{ number_format($vatAmount, 2) }}</span>
    </div>
  @endif

  <div class="receipt-row total">
    <span>Total</span>
    <span>{{ $cur }} {{ number_format($transaction->grand_total, 2) }}</span>
  </div>

  @if($transaction->status === 'completed')
    <hr class="receipt-divider">

    <div class="receipt-row paid">
      <span>Paid</span>
      <span>{{ $cur }} {{ number_format($transaction->paid_amount, 2) }}</span>
    </div>

    @if($transaction->change_amount > 0)
    <div class="receipt-change">
      Change
      <strong>{{ $cur }} {{ number_format($transaction->change_amount, 2) }}</strong>
    </div>
    @endif
  @endif

  <hr class="receipt-divider">

  {{-- Footer --}}
  <div class="receipt-footer">
    Thank you for your purchase!<br>
    Please keep this receipt for your records.
  </div>

</div>
</body>
</html>
