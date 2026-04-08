@extends('admin.layout.main')

@section('title', 'Order Details')
@section('page_title', 'Order Details')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Order #{{ $order->id }}</h3>
    </div>
    <div style="padding: 24px;">
        <div class="table-responsive">
            <table>
                <tbody>
                    <tr><th>Customer</th><td>{{ $order->customer_name ?: 'Walk-in User' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $order->customer_phone ?: 'No phone provided' }}</td></tr>
                    <tr><th>Asset</th><td>{{ $order->asset }}</td></tr>
                    <tr><th>Side</th><td>{{ strtoupper($order->side) }}</td></tr>
                    <tr><th>Order Type</th><td>{{ strtoupper($order->order_type) }}</td></tr>
                    <tr><th>Quantity</th><td>{{ number_format((float) $order->quantity, 2) }}</td></tr>
                    <tr><th>Price</th><td>{{ number_format((float) $order->price, 2) }}</td></tr>
                    <tr><th>Total</th><td>{{ number_format((float) $order->total, 2) }}</td></tr>
                    <tr><th>Status</th><td>{{ strtoupper($order->status) }}</td></tr>
                    <tr><th>Placed At</th><td>{{ optional($order->placed_at)->format('d M Y, h:i A') }}</td></tr>
                    <tr><th>Notes</th><td>{{ $order->notes ?: 'No notes supplied' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
