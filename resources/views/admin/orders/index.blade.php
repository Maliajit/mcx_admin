@extends('admin.layout.main')

@section('title', 'Order Management')
@section('page_title', 'Orders')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Orders Feed</h3>
    </div>
    @include('admin.orders.partials.table', [
        'orders' => $orders,
        'emptyMessage' => 'No customer orders have been placed yet.',
    ])
</div>
@endsection
