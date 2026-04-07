@extends('admin.layout.main')

@section('title', 'Completed Orders')
@section('page_title', 'Orders')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Finalized Trading History</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Metal</th>
                    <th>Quantity</th>
                    <th>Final Price</th>
                    <th>Completed Date</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ORD-8942</td>
                    <td style="font-weight: 700;">Jane Cooper</td>
                    <td>GOLD</td>
                    <td>100g</td>
                    <td>₹7,24,500</td>
                    <td>Mar 14, 2026</td>
                    <td style="text-align: right;"><a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a></td>
                </tr>
                <tr>
                    <td>#ORD-8940</td>
                    <td style="font-weight: 700;">Ankit Patel</td>
                    <td>GOLD</td>
                    <td>10g</td>
                    <td>₹72,450</td>
                    <td>Mar 14, 2026</td>
                </tr>
                <tr>
                    <td>#ORD-8930</td>
                    <td style="font-weight: 700;">Deepak Singh</td>
                    <td>SILVER</td>
                    <td>2kg</td>
                    <td>₹1,74,460</td>
                    <td>Mar 13, 2026</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
