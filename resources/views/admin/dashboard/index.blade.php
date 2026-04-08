@extends('admin.layout.main')

@section('title', 'Dashboard')
@section('page_title', 'System Overview')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Live Market Snapshot</h3>
    </div>
    <div id="dashboard-live-rates" style="padding: 24px; color: var(--text-primary);">
        Loading live rates...
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <h3>Orders Feed</h3>
    </div>
    <div style="padding: 24px;">
        <div style="display: flex; gap: 16px; margin-bottom: 16px;">
            <div style="padding: 16px; background: #fff7ed; border: 1px solid #fdba74; min-width: 180px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Pending Orders</div>
                <div style="font-size: 28px; font-weight: 800;">{{ $pendingOrdersCount }}</div>
            </div>
            <div style="padding: 16px; background: #ecfdf3; border: 1px solid #86efac; min-width: 180px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Completed Orders</div>
                <div style="font-size: 28px; font-weight: 800;">{{ $completedOrdersCount }}</div>
            </div>
        </div>
        @include('admin.orders.partials.table', [
            'orders' => $recentOrders,
            'emptyMessage' => 'No recent orders are available yet.',
        ])
    </div>
</div>

<script>
async function loadDashboardRates() {
    const container = document.getElementById('dashboard-live-rates');

    try {
        const response = await fetch('/api/v1/live-rates');
        const payload = await response.json();
        const data = payload.data || {};

        if (!data.items || !data.items.length) {
            container.innerHTML = '<p>No live market rows are available.</p>';
            return;
        }

        const rows = data.items.slice(0, 5).map(item => `
            <tr>
                <td>${item.name}</td>
                <td>${item.bid}</td>
                <td>${item.ask}</td>
                <td>${item.high}</td>
                <td>${item.low}</td>
            </tr>
        `).join('');

        container.innerHTML = `
            <p style="margin-bottom: 12px; color: ${data.is_stale ? '#b45309' : 'var(--text-secondary)'};">
                Source: ${data.source} | Served at: ${data.served_at}
            </p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Commodity</th>
                            <th>Bid</th>
                            <th>Ask</th>
                            <th>High</th>
                            <th>Low</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    } catch (error) {
        container.innerHTML = '<p style="color: var(--danger);">Unable to load live market data.</p>';
    }
}

loadDashboardRates();
</script>
@endsection
