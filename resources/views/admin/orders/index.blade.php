@extends('admin.layout.main')

@section('title', 'All Orders')
@section('page_title', 'All Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Orders</h3>
                </div>
                <div class="card-body">
                    @if($orders->isEmpty())
                        <p>No orders placed yet.</p>
                    @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Target Price</th>
                                <th>Status</th>
                                <th>Placed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->email }}</td>
                                <td>{{ $order->asset }}</td>
                                <td><span class="badge badge-info">{{ ucfirst($order->type) }}</span></td>
                                <td>{{ $order->price }}</td>
                                <td>{{ $order->target_price ?? 'N/A' }}</td>
                                <td><span class="badge badge-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->placed_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
