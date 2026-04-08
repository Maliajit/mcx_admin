@if ($orders->isEmpty())
    <div style="padding: 24px; color: var(--text-secondary);">
        {{ $emptyMessage ?? 'No orders are available yet.' }}
    </div>
@else
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Asset</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Placed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            <strong>{{ $order->customer_name ?: 'Walk-in User' }}</strong><br>
                            <span style="color: var(--text-secondary); font-size: 12px;">
                                {{ $order->customer_phone ?: 'No phone provided' }}
                            </span>
                        </td>
                        <td>{{ $order->asset }}</td>
                        <td>{{ strtoupper($order->side) }} / {{ strtoupper($order->order_type) }}</td>
                        <td>{{ number_format((float) $order->quantity, 2) }}</td>
                        <td>{{ number_format((float) $order->price, 2) }}</td>
                        <td>{{ number_format((float) $order->total, 2) }}</td>
                        <td>
                            <span style="font-weight: 700; color: {{ $order->status === 'completed' ? 'var(--success-color)' : '#b45309' }};">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td>{{ optional($order->placed_at)->format('d M Y, h:i A') }}</td>
                        <td>
                            <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; text-decoration: none;">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
