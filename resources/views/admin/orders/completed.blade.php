@extends('admin.layout.main')

@section('title', 'Completed Orders')
@section('page_title', 'Order History (Delivered)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title mb-0 font-weight-bold">Fulfillment History</h3>
                </div>
                <div class="card-body">
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No completed/delivered orders yet.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Client Info</th>
                                <th>Asset Details</th>
                                <th>Final Value</th>
                                <th>Status</th>
                                <th>Fulfillment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><span class="text-muted">#{{ $order->id }}</span></td>
                                <td>
                                    <strong>{{ $order->user->mobile }}</strong><br>
                                    <small class="text-muted">{{ $order->user->verifiedUser?->full_name }}</small>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $order->asset }}</span><br>
                                    <small class="text-muted">{{ $order->quantity }} {{ $order->product_type == 'coin' ? 'Units' : 'g' }} • {{ strtoupper($order->type) }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column text-right pr-4">
                                        <strong class="text-dark">₹{{ $order->total }}</strong>
                                        <small class="text-muted" style="font-size: 0.7rem;">Basis: ₹{{ $order->price }}/unit</small>
                                    </div>
                                </td>
                                <td><span class="badge badge-dark px-3 shadow-sm border"><i class="fas fa-check-circle mr-1"></i> DELIVERED</span></td>
                                <td>
                                    <span class="text-muted">{{ $order->updated_at->format('d M Y, H:i') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
