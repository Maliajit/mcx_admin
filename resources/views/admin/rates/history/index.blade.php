@extends('admin.layout.main')

@section('title', 'Price History')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <p style="color: var(--text-secondary); margin-bottom: 5px;">Market Records</p>
        <h1>Price History</h1>
    </div>
    
    <div class="page-tabs" style="display: flex; gap: 8px; background: var(--bg-card); padding: 4px; border-radius: 12px; border: 1px solid var(--border-color);">
        <button class="tab-btn active" style="padding: 8px 16px; border-radius: 8px; border: none; background: var(--accent-color); color: white; font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 0.85rem;">
            All
        </button>
        <button class="tab-btn" style="padding: 8px 16px; border-radius: 8px; border: none; background: transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 0.85rem;">
            Gold
        </button>
        <button class="tab-btn" style="padding: 8px 16px; border-radius: 8px; border: none; background: transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 0.85rem;">
            Silver
        </button>
    </div>
</div>

<div class="user-table-container">
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Historical Rates</h2>
        <i class="fa fa-ellipsis-h" style="color: var(--text-secondary); cursor: pointer;"></i>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>Commodity</th>
                <th>Opening Price</th>
                <th>Closing Price</th>
                <th>High / Low</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #FACC15;"></div>
                        <span style="font-weight: 600; color: var(--text-primary);">Gold Live</span>
                    </div>
                </td>
                <td><span style="font-weight: 600;">₹ 62,200</span></td>
                <td><span style="font-weight: 600;">₹ 62,450</span></td>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="color: var(--success-color); font-size: 0.8rem; font-weight: 600;">H: ₹ 62,600</span>
                        <span style="color: var(--danger-color); font-size: 0.8rem; font-weight: 600;">L: ₹ 62,100</span>
                    </div>
                </td>
                <td><span class="text-muted" style="font-size: 0.85rem;">13 Mar 2026</span></td>
            </tr>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #8A8A8A;"></div>
                        <span style="font-weight: 600; color: var(--text-primary);">Silver Live</span>
                    </div>
                </td>
                <td><span style="font-weight: 600;">₹ 74,100</span></td>
                <td><span style="font-weight: 600;">₹ 74,320</span></td>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="color: var(--success-color); font-size: 0.8rem; font-weight: 600;">H: ₹ 74,800</span>
                        <span style="color: var(--danger-color); font-size: 0.8rem; font-weight: 600;">L: ₹ 74,000</span>
                    </div>
                </td>
                <td><span class="text-muted" style="font-size: 0.85rem;">13 Mar 2026</span></td>
            </tr>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #FACC15;"></div>
                        <span style="font-weight: 600; color: var(--text-primary);">Gold Live</span>
                    </div>
                </td>
                <td><span style="font-weight: 600;">₹ 61,900</span></td>
                <td><span style="font-weight: 600;">₹ 62,150</span></td>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="color: var(--success-color); font-size: 0.8rem; font-weight: 600;">H: ₹ 62,300</span>
                        <span style="color: var(--danger-color); font-size: 0.8rem; font-weight: 600;">L: ₹ 61,800</span>
                    </div>
                </td>
                <td><span class="text-muted" style="font-size: 0.85rem;">12 Mar 2026</span></td>
            </tr>
        </tbody>
    </table>
    
    <div class="pagination-container">
        <p class="pagination-info">Showing 1 to 3 of 120 entries</p>
        <div class="pagination-links">
            <button class="btn-page" disabled><i class="fa fa-chevron-left"></i></button>
            <button class="btn-page active">1</button>
            <button class="btn-page">2</button>
            <button class="btn-page">3</button>
            <span class="page-dots">...</span>
            <button class="btn-page">40</button>
            <button class="btn-page"><i class="fa fa-chevron-right"></i></button>
        </div>
    </div>
</div>
@endsection

