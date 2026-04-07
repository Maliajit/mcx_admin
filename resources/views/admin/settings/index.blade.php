@extends('admin.layout.main')

@section('title', 'Admin Profile')
@section('page_title', 'Settings')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 32px;">
    <!-- Profile Info -->
    <div class="section-card">
        <div class="section-header">
            <h3>Update Profile Information</h3>
        </div>
        <div style="padding: 32px;">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" class="form-control" value="Admin User">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" class="form-control" value="admin@example.com">
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" class="form-control" value="+91 9876543210">
            </div>
            
            <button class="btn btn-primary" style="width: 100%;">Save Profile Changes</button>
        </div>
    </div>

    <!-- Password Change -->
    <div class="section-card">
        <div class="section-header">
            <h3>Change Password</h3>
        </div>
        <div style="padding: 32px;">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" class="form-control">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" class="form-control">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" class="form-control">
            </div>
            
            <button class="btn btn-accent" style="width: 100%;">Update Password</button>
        </div>
    </div>
</div>
@endsection
