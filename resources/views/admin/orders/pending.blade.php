@extends('admin.layout.main')

@section('title', 'Settlement Queue')
@section('page_title', 'Settlement')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Pending Physical Settlements</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Confirm physical payment and metal delivery.</p>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Metal</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Order Time</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ORD-8941</td>
                    <td style="font-weight: 700;">Rahul Roy</td>
                    <td>SILVER</td>
                    <td>1kg</td>
                    <td>₹87,230</td>
                    <td>10:30 AM</td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-success btn-sm">Payment Received</button>
                        <button class="btn btn-accent btn-sm">Metal Delivered</button>
                    </td>
                </tr>
                <tr>
                    <td>#ORD-8935</td>
                    <td style="font-weight: 700;">Anita Desai</td>
                    <td>GOLD</td>
                    <td>10g</td>
                    <td>₹72,450</td>
                    <td>09:15 AM</td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-accent btn-sm">Metal Delivered</button>
                        <button class="btn btn-primary btn-sm">Complete Settlement</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
