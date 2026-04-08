@extends('admin.layout.main')

@section('title', 'Completed Orders')
@section('page_title', 'Completed Orders')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Completed Orders</h3>
    </div>
    @include('admin.orders.partials.table', [
        'orders' => $orders,
        'emptyMessage' => 'No completed orders are available yet.',
    ])
</div>
@endsection
