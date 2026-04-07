@extends('admin.layout.main')

@section('title', 'User Details')
@section('page_title', 'User Management')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
    <!-- Profile Info Card -->
    <div class="section-card">
        <div class="section-header">
            <h3>User Profile</h3>
            <span class="badge badge-success">Active</span>
        </div>
        <div style="padding: 32px; text-align: center;">
            <div style="width: 100px; height: 100px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 24px;">JD</div>
            <h2 style="color: var(--primary); margin-bottom: 8px;">John Doe</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Member since Mar 2026</p>
            
            <div style="text-align: left; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">Email Address</label>
                    <span style="font-weight: 700; color: var(--primary);">john@example.com</span>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">Mobile Number</label>
                    <span style="font-weight: 700; color: var(--primary);">+91 98765 43210</span>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.8rem; color: var(--text-muted); display: block;">KYC Status</label>
                    <span class="badge badge-success">Verified</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button class="btn btn-warning btn-sm" style="flex: 1;">Deactivate</button>
                <button class="btn btn-danger btn-sm" style="flex: 1;">Delete</button>
            </div>
        </div>
    </div>

    <!-- Trading History Card -->
    <div class="section-card">
        <div class="section-header">
            <h3>Trading History</h3>
            <button class="btn btn-primary btn-sm">Full Report</button>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Metal</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#ORD-8942</td>
                        <td>GOLD</td>
                        <td>100g</td>
                        <td>₹7,24,500</td>
                        <td><span class="badge badge-success">Completed</span></td>
                    </tr>
                    <tr>
                        <td>#ORD-8910</td>
                        <td>SILVER</td>
                        <td>5kg</td>
                        <td>₹4,36,150</td>
                        <td><span class="badge badge-success">Completed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
