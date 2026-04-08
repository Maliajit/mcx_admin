@extends('admin.layout.main')

@section('title', 'Silver Rate')
@section('page_title', 'Market Rates')

@section('content')
<div class="section-card">
    <div class="section-header">
        <h3>Live Silver Rates</h3>
    </div>
    <div id="silver-rates-panel" style="padding: 24px;">Loading live silver rates...</div>
</div>

<script>
async function loadSilverRates() {
    const panel = document.getElementById('silver-rates-panel');

    try {
        const response = await fetch('/api/v1/live-rates');
        const payload = await response.json();
        const data = payload.data || {};
        const items = (data.items || []).filter(item => item.name.toUpperCase().includes('SILVER'));

        if (!items.length) {
            panel.innerHTML = '<p>No live silver instruments are available from the feed.</p>';
            return;
        }

        panel.innerHTML = `
            <p style="margin-bottom: 16px; color: ${data.is_stale ? '#b45309' : 'var(--text-secondary)'};">
                Source: ${data.source} | Served at: ${data.served_at}
            </p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Bid</th>
                            <th>Ask</th>
                            <th>High</th>
                            <th>Low</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
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
        panel.innerHTML = '<p style="color: var(--danger);">Unable to load live silver rates.</p>';
    }
}

loadSilverRates();
</script>
@endsection
