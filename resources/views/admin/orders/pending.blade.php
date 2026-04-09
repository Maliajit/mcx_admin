@extends('admin.layout.main')

@section('title', 'Pending Limit Orders')
@section('page_title', 'Pending Limit Orders (Target Prices)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0 text-dark">Awaiting Price Collision / Target Match</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No pending limit orders at this time.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Asset</th>
                                <th>Current Rate</th>
                                <th class="bg-warning-light text-primary">TARGET PRICE</th>
                                <th>Status</th>
                                <th>Placed On</th>
                                <th width="220">Actions</th>
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
                                    <span class="font-weight-bold">{{ $order->asset }}</span><br>
                                    <small class="badge badge-secondary">{{ $order->quantity }} {{ $order->product_type == 'coin' ? 'Units' : 'g' }}</small>
                                </td>
                                <td>₹{{ $order->price }}</td>
                                <td class="bg-warning-light">
                                    <strong class="text-primary" style="font-size: 1.1rem;">₹{{ $order->target_price }}</strong>
                                </td>
                                <td><span class="badge badge-warning px-3 py-2 shadow-sm text-uppercase">WAITING</span></td>
                                <td>{{ $order->placed_at->format('d M, Y') }}<br><small class="text-muted">{{ $order->placed_at->format('H:i') }}</small></td>
                                <td>
                                    <div class="btn-group w-100 shadow-sm">
                                        <form method="POST" action="{{ route('orders.approve', $order) }}" class="flex-grow-1 mr-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold">
                                                CONFIRM HIT
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('orders.reject', $order) }}" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-block">
                                                REJECT
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="alert alert-info mt-4 shadow-sm border-0 border-left-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
                    <div>
                        <h5 class="mb-1">What are Target Price Orders?</h5>
                        <p class="mb-0 small">These are "Limit Orders" placed by users. They will remain here until the market price reaches their target. Click <strong>"Confirm Hit"</strong> to execute the trade and move it to the active confirmed list.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.08); }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
</style>
@endsection
