@extends('admin.layout.main')

@section('title', 'Active Orders')
@section('page_title', 'Active Orders (Confirmed)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Live Trading Dashboard (Confirmed)</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active confirmed orders found.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Asset Detail</th>
                                <th>Type</th>
                                <th>Executed Price</th>
                                <th>Status</th>
                                <th>Placed On</th>
                                <th width="150">Action</th>
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
                                <td><span class="badge badge-info shadow-sm px-2 text-uppercase">{{ $order->type }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>₹{{ $order->price }}</strong>
                                        <small class="text-success">+ ₹{{ $order->tax_amount }} Tax</small>
                                    </div>
                                </td>
                                <td><span class="badge badge-success px-3 py-2">CONFIRMED</span></td>
                                <td>{{ $order->placed_at->format('d M, Y') }}<br><small class="text-muted">{{ $order->placed_at->format('H:i') }}</small></td>
                                <td>
                                    <form method="POST" action="{{ route('orders.deliver', $order) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm">
                                            MARK DELIVERED
                                        </button>
                                    </form>
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
