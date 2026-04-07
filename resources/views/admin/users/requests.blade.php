@extends('admin.layout.main')

@section('title', 'Activation Requests')
@section('page_title', 'User Management')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Pending Account Approvals</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Phone</th>
                    <th>Request Time</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 700;">Manish Singh</td>
                    <td>+91 98223 34455</td>
                    <td>Today, 10:30 AM</td>
                    <td style="text-align: right;">
                        <button class="btn btn-success btn-sm" style="padding: 4px 10px;">Approve</button>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Priya Verma</td>
                    <td>+91 91776 65544</td>
                    <td>Yesterday, 4:15 PM</td>
                    <td style="text-align: right;">
                        <button class="btn btn-success btn-sm" style="padding: 4px 10px;">Approve</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
