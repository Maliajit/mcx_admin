@extends('admin.layout.main')

@section('title', 'Pending Orders')
@section('page_title', 'Pending Settlements')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Pending Settlements</h3>
    </div>
    @include('admin.orders.partials.table', [
        'orders' => $orders,
        'emptyMessage' => 'No pending orders are waiting right now.',
    ])
</div>
@endsection
