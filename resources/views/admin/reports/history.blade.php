@extends('admin.layout.main')

@section('title', 'Order History')
@section('page_title', 'Data & Reports')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Filter History</h3>
    </div>
    <div style="padding: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Date Range</label>
                <input type="text" class="form-control" placeholder="Select dates">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>User Filter</label>
                <input type="text" class="form-control" placeholder="All Users">
            </div>
            <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
                <button class="btn btn-primary" style="height: 48px; width: 100%;">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <h3>Order Results</h3>
        <button class="btn btn-success btn-sm"><i class="fa fa-download"></i> Export CSV</button>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Metal</th>
                    <th>Qty</th>
                    <th>Final Price</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>14-03-2026</td>
                    <td style="font-weight: 700;">John Doe</td>
                    <td>GOLD</td>
                    <td>100g</td>
                    <td>₹7,24,500</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td style="text-align: right;"><a href="{{ url('/admin/orders/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a></td>
                </tr>
                <tr>
                    <td>13-03-2026</td>
                    <td style="font-weight: 700;">Alice Smith</td>
                    <td>SILVER</td>
                    <td>1kg</td>
                    <td>₹87,230</td>
                    <td><span class="badge badge-success">Completed</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
