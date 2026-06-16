<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Receipt #{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</title>
<style>
  @page {
    size: 80mm auto;
    margin: 4mm 3mm;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    background: #e8e8e8;
    font-family: 'Courier New', Courier, monospace;
    font-size: 11px;
    line-height: 1.45;
    color: #111;
  }

  body {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px 12px;
    min-height: 100vh;
  }

  .receipt {
    background: #fff;
    width: 72mm;
    max-width: 72mm;
    padding: 12px 10px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  }

  /* ── Store Header ── */
  .store-name {
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 4px;
  }

  .store-sub {
    font-size: 9.5px;
    text-align: center;
    color: #555;
    line-height: 1.6;
    margin-bottom: 4px;
  }

  .receipt-date {
    font-size: 9.5px;
    text-align: center;
    color: #666;
    margin-bottom: 3px;
  }

  .receipt-status {
    display: block;
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
  }
  .status-completed { color: #166534; }
  .status-hold      { color: #92400e; }

  /* ── Divider ── */
  hr {
    border: none;
    border-top: 1px dashed #bbb;
    margin: 7px 0;
  }

  /* ── Meta rows ── */
  .meta {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    margin-bottom: 2px;
  }
  .meta .label { color: #555; }
  .meta .value { font-weight: 600; text-align: right; max-width: 62%; word-break: break-word; }

  /* ── Items table ── */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
  }
  thead th {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 0 0 5px;
    border-bottom: 1px dashed #bbb;
  }
  thead th:last-child { text-align: right; }
  tbody td {
    padding: 4px 0 2px;
    vertical-align: top;
  }
  tbody td:last-child { text-align: right; white-space: nowrap; }
  .item-name {
    display: block;
    font-size: 10.5px;
    font-weight: 600;
    line-height: 1.3;
    word-break: break-word;
    padding-right: 8px;
  }
  .item-detail {
    display: block;
    font-size: 9.5px;
    color: #666;
  }

  /* ── Totals ── */
  .row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    font-size: 10.5px;
    padding: 2px 0;
  }
  .row.discount { color: #b45309; }
  .row.tax      { color: #374151; }
  .row.grand {
    font-size: 13.5px;
    font-weight: 700;
    padding: 5px 0;
    border-top: 1px dashed #bbb;
    border-bottom: 1px dashed #bbb;
    margin: 4px 0;
  }
  .row.paid   { color: #166534; font-weight: 600; font-size: 11px; }
  .row.change { font-weight: 700; font-size: 12px; }

  /* ── Footer ── */
  .thankyou {
    text-align: center;
    font-size: 10px;
    color: #333;
    margin-top: 4px;
    line-height: 1.7;
  }
  .thankyou strong {
    display: block;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 1px;
  }

  .software {
    margin-top: 6px;
    text-align: center;
    font-size: 8.5px;
    color: #999;
    line-height: 1.6;
  }
  .software a {
    color: #999;
    text-decoration: none;
  }

  /* ── Print overrides ── */
  @media print {
    html, body {
      background: #fff;
      padding: 0;
    }
    body { display: block; }
    .receipt {
      box-shadow: none;
      width: 100%;
      max-width: 100%;
      padding: 0;
    }
  }
</style>
</head>
<body>
<div class="receipt">

  {{-- Store Header --}}
  <div class="store-name">{{ $setting->store_name }}</div>
  @if($setting->store_address || $setting->store_phone)
  <div class="store-sub">
    @if($setting->store_address){{ $setting->store_address }}<br>@endif
    @if($setting->store_phone)Tel: {{ $setting->store_phone }}@endif
  </div>
  @endif
  <div class="receipt-date">{{ $transaction->created_at->format('d M Y  h:i A') }}</div>
  <span class="receipt-status status-{{ $transaction->status }}">{{ $transaction->status }}</span>

  <hr>

  {{-- Transaction Meta --}}
  <div class="meta">
    <span class="label">Receipt #</span>
    <span class="value">{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
  </div>
  @if($transaction->customer)
  <div class="meta">
    <span class="label">Customer</span>
    <span class="value">{{ $transaction->customer->customer_name }}</span>
  </div>
  @endif
  <div class="meta">
    <span class="label">Payment</span>
    <span class="value">{{ ucfirst($transaction->payment_method ?? 'cash') }}</span>
  </div>

  <hr>

  {{-- Items --}}
  <table>
    <thead>
      <tr>
        <th style="text-align:left">Item</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transaction->items as $item)
      <tr>
        <td>
          <span class="item-name">{{ $item->product_name }}</span>
          <span class="item-detail">{{ $item->quantity }} &times; {{ number_format($item->product_price, 2) }}</span>
        </td>
        <td>{{ number_format($item->total, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <hr>

  {{-- Totals --}}
  @php
    $cur    = $setting->currency_symbol;
    $preTax = $transaction->subtotal - $transaction->discount;
    $gstAmt = $setting->gst_enabled ? round($preTax * $setting->gst_percentage / 100, 2) : 0;
    $vatAmt = $setting->vat_enabled ? round($preTax * $setting->vat_percentage / 100, 2) : 0;
  @endphp

  <div class="row">
    <span>Subtotal</span>
    <span>{{ $cur }} {{ number_format($transaction->subtotal, 2) }}</span>
  </div>

  @if($transaction->discount > 0)
  <div class="row discount">
    <span>Discount</span>
    <span>- {{ $cur }} {{ number_format($transaction->discount, 2) }}</span>
  </div>
  @endif

  @if($setting->gst_enabled && $gstAmt > 0)
  <div class="row tax">
    <span>GST ({{ $setting->gst_percentage }}%)</span>
    <span>{{ $cur }} {{ number_format($gstAmt, 2) }}</span>
  </div>
  @endif

  @if($setting->vat_enabled && $vatAmt > 0)
  <div class="row tax">
    <span>VAT ({{ $setting->vat_percentage }}%)</span>
    <span>{{ $cur }} {{ number_format($vatAmt, 2) }}</span>
  </div>
  @endif

  <div class="row grand">
    <span>TOTAL</span>
    <span>{{ $cur }} {{ number_format($transaction->grand_total, 2) }}</span>
  </div>

  @if($transaction->status === 'completed')
  <div class="row paid">
    <span>Paid</span>
    <span>{{ $cur }} {{ number_format($transaction->paid_amount, 2) }}</span>
  </div>
  @if($transaction->change_amount > 0)
  <div class="row change">
    <span>Change</span>
    <span>{{ $cur }} {{ number_format($transaction->change_amount, 2) }}</span>
  </div>
  @endif
  @endif

  <hr>

  {{-- Thank You --}}
  <div class="thankyou">
    <strong>Thank You!</strong>
    Please keep this receipt for your records.
  </div>

  <hr>

  {{-- Software Credit --}}
  <div class="software">
    Software by Khawar Mehfooz<br>
    <a href="https://khawarmehfooz.com">khawarmehfooz.com</a>
  </div>

</div>
</body>
</html>
