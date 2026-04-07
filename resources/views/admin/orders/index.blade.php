@extends('admin.layout.main')

@section('title', 'Order Management')
@section('page_title', 'Orders')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>@if(request('status') == 'pending') Pending @elseif(request('status') == 'completed') Completed @endif All Orders</h3>
        <div class="header-actions">
            <select class="form-control" style="width: 150px; display: inline-block; padding: 6px 12px; font-size: 0.85rem;" onchange="location = this.value;">
                <option value="{{ url('/admin/orders') }}" {{ !request('status') ? 'selected' : '' }}>All Metals</option>
                <option value="{{ url('/admin/orders?metal=gold') }}" {{ request('metal') == 'gold' ? 'selected' : '' }}>Gold Only</option>
                <option value="{{ url('/admin/orders?metal=silver') }}" {{ request('metal') == 'silver' ? 'selected' : '' }}>Silver Only</option>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Metal</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Order Time</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ORD-8942</td>
                    <td style="font-weight: 700;">Jane Cooper</td>
                    <td><span style="color: var(--accent); font-weight: 700;">GOLD</span></td>
                    <td>100g</td>
                    <td>₹7,24,500</td>
                    <td>10:45 AM</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                    </td>
                </tr>
                <tr>
                    <td>#ORD-8941</td>
                    <td style="font-weight: 700;">Rahul Roy</td>
                    <td><span style="color: #8A8A8A; font-weight: 700;">SILVER</span></td>
                    <td>1kg</td>
                    <td>₹87,230</td>
                    <td>10:30 AM</td>
                    <td><span class="badge badge-pending">Pending Payment</span></td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-warning btn-sm" style="padding: 4px 10px;">Track</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
