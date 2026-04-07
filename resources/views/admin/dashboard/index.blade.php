@extends('admin.layout.main')

@section('title', 'Dashboard')
@section('page_title', 'System Overview')

@section('content')
<div class="dashboard-grid">
    <!-- User Statistics -->
    <div class="stat-card">
        <span class="label">Total Users</span>
        <span class="value">1,250</span>
    </div>
    <div class="stat-card">
        <span class="label">Active Users</span>
        <span class="value">1,200</span>
    </div>
    <div class="stat-card">
        <span class="label">Pending Requests</span>
        <span class="value">12</span>
    </div>
    <div class="stat-card">
        <span class="label">Pending Settlements</span>
        <span class="value">45</span>
    </div>
    <div class="stat-card" style="background-color: var(--accent); color: var(--primary);">
        <span class="label" style="color: rgba(10, 25, 47, 0.7);">Today's Booking</span>
        <span class="value">128</span>
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <h3>Recent Trading Activity</h3>
        <a href="{{ url('/admin/orders') }}" class="btn btn-primary btn-sm">View All Orders</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Metal</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ORD-8942</td>
                    <td>Jane Cooper</td>
                    <td><span style="color: var(--accent); font-weight: 700;">GOLD</span></td>
                    <td>100g</td>
                    <td>₹7,24,500</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td>10:45 AM</td>
                    <td><a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 2px 8px; text-decoration: none;">View</a></td>
                </tr>
                <tr>
                    <td>#ORD-8941</td>
                    <td>Rahul Roy</td>
                    <td><span style="color: #8A8A8A; font-weight: 700;">SILVER</span></td>
                    <td>1kg</td>
                    <td>₹87,230</td>
                    <td><span class="badge badge-pending">Pending</span></td>
                    <td>10:30 AM</td>
                </tr>
                <tr>
                    <td>#ORD-8940</td>
                    <td>Ankit Patel</td>
                    <td><span style="color: var(--accent); font-weight: 700;">GOLD</span></td>
                    <td>10g</td>
                    <td>₹72,450</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td>10:15 AM</td>
                </tr>
                <tr>
                    <td>#ORD-8939</td>
                    <td>Cody Fisher</td>
                    <td><span style="color: var(--accent); font-weight: 700;">GOLD</span></td>
                    <td>50g</td>
                    <td>₹3,62,250</td>
                    <td><span class="badge badge-danger">Cancelled</span></td>
                    <td>09:50 AM</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
