@extends('admin.layout.main')

@section('title', 'Price History')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Current Feed Snapshot</h3>
    </div>
    <div id="rates-history-panel" style="padding: 24px;">Loading market feed...</div>
</div>

<script>
async function loadRateHistorySnapshot() {
    const panel = document.getElementById('rates-history-panel');

    try {
        const response = await fetch('/api/v1/live-rates');
        const payload = await response.json();
        const data = payload.data || {};

        if (!data.items || !data.items.length) {
            panel.innerHTML = '<p>No live feed rows are available.</p>';
            return;
        }

        panel.innerHTML = `
            <p style="margin-bottom: 16px;">
                Historical storage is not connected yet, so this page now shows the current upstream snapshot instead of mock history.
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
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.bid}</td>
                                <td>${item.ask}</td>
                                <td>${item.high}</td>
                                <td>${item.low}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    } catch (error) {
        panel.innerHTML = '<p style="color: var(--danger);">Unable to load the current feed snapshot.</p>';
    }
}

loadRateHistorySnapshot();
</script>
@endsection
