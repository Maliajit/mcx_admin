@extends('admin.layout.main')

@section('title', 'Silver Rate')
@section('page_title', 'Market Rates')

@section('content')
<div style="max-width: 600px;">
    <div class="section-card">
        <div class="section-header">
            <h3>Update Silver Rate</h3>
        </div>
        <div style="padding: 32px;">
            <div class="form-group">
                <label>Current Silver Price (per 1kg)</label>
                <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 24px;">₹87,230.00</div>
            </div>
            
            <div class="form-group">
                <label for="silver_rate">New Silver Price</label>
                <input type="number" id="silver_rate" class="form-control" placeholder="Enter new price" style="font-size: 1.5rem; height: 60px;">
            </div>
            
            <button class="btn btn-accent" style="width: 100%; height: 50px; font-size: 1.1rem;">Update Silver Rate</button>
        </div>
    </div>
</div>
@endsection
