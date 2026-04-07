@extends('admin.layout.main')

@section('title', 'Gold Rate')
@section('page_title', 'Market Rates')

@section('content')
<div style="max-width: 600px;">
    <div class="section-card">
        <div class="section-header">
            <h3>Update Gold Rate</h3>
        </div>
        <div style="padding: 32px;">
            <div class="form-group">
                <label>Current Gold Price (per 10g)</label>
                <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 24px;">₹72,450.00</div>
            </div>
            
            <div class="form-group">
                <label for="gold_rate">New Gold Price</label>
                <input type="number" id="gold_rate" class="form-control" placeholder="Enter new price" style="font-size: 1.5rem; height: 60px;">
            </div>
            
            <button class="btn btn-accent" style="width: 100%; height: 50px; font-size: 1.1rem;">Update Gold Rate</button>
        </div>
    </div>
    
    <div class="section-card">
        <div class="section-header">
            <h3>Auto-Update Settings</h3>
        </div>
        <div style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-weight: 700; color: var(--primary);">Real-time MCX Sync</p>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Automatically update rates from MCX feed</p>
                </div>
                <button class="btn btn-success btn-sm">Enabled</button>
            </div>
        </div>
    </div>
</div>
@endsection
