@extends('admin.layout.main')

@section('title', 'All Users')
@section('page_title', 'User Management')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>All Registered Users</h3>
        <div class="header-actions">
            <input type="text" placeholder="Search users..." class="form-control" style="width: 250px; display: inline-block; padding: 6px 12px; font-size: 0.85rem;">
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#USR-1024</td>
                    <td style="font-weight: 700;">John Doe</td>
                    <td>+91 98765 43210</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>12 Mar 2026</td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/users/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-warning btn-sm" style="padding: 4px 10px;">Deactivate</button>
                        <button class="btn btn-danger btn-sm" style="padding: 4px 10px;">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>#USR-1025</td>
                    <td style="font-weight: 700;">Alice Smith</td>
                    <td>+91 99988 77766</td>
                    <td><span class="badge badge-danger">Inactive</span></td>
                    <td>10 Mar 2026</td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/users/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-success btn-sm" style="padding: 4px 10px;">Activate</button>
                        <button class="btn btn-danger btn-sm" style="padding: 4px 10px;">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Rahul Kumar</td>
                    <td>rahul@domain.in <br> <span style="font-size: 0.8rem; color: var(--text-muted);">+91 91122 33445</span></td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>05 Mar 2026</td>
                    <td style="text-align: right;">
                        <a href="{{ url('/admin/users/show') }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        <button class="btn btn-warning btn-sm" style="padding: 4px 10px;">Deactivate</button>
                        <button class="btn btn-danger btn-sm" style="padding: 4px 10px;">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/users.js') }}"></script>
@endpush
