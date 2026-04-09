@extends('admin.layout.main')

@section('title', 'KYC Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">KYC Verification Dashboard</h3>
                    <span class="badge badge-light px-3 py-2">{{ $kycRequests->count() }} Submissions</span>
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
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Security Identity</th>
                                    <th>Full Name</th>
                                    <th>PAN / Aadhaar</th>
                                    <th>Status</th>
                                    <th>Submitted On</th>
                                    <th width="280">Actions / Trading Controls</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kycRequests as $request)
                                <tr>
                                    <td><small class="text-muted">#{{ $request->id }}</small></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold text-primary">{{ $request->user->mobile }}</span>
                                            <small class="text-muted">{{ $request->email ?? 'no-email@registered.com' }}</small>
                                        </div>
                                    </td>
                                    <td><span class="font-weight-bold text-dark">{{ $request->full_name }}</span></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><i class="fas fa-id-card text-muted mr-1"></i> {{ $request->pan_number }}</span>
                                            <small class="text-muted"><i class="fas fa-fingerprint text-muted mr-1"></i> {{ $request->aadhaar_number }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->kyc_status === 'pending' ? 'warning' : ($request->kyc_status === 'approved' ? 'success' : 'danger') }} py-2 px-3">
                                            {{ strtoupper($request->kyc_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}<br><small class="text-muted">{{ $request->created_at->format('H:i') }}</small></td>
                                    <td>
                                        @if($request->kyc_status === 'pending')
                                        <div class="btn-group w-100">
                                            <form method="POST" action="{{ route('admin.kyc.approve', $request) }}" class="flex-grow-1 mr-1">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-block">APPROVE</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.kyc.reject', $request) }}" class="flex-grow-1">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm btn-block">REJECT</button>
                                            </form>
                                        </div>
                                        @elseif($request->kyc_status === 'approved' && $request->user)
                                            <div class="bg-light p-2 rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="font-weight-bold text-uppercase text-muted" style="font-size: 0.7rem;">Trading Status</small>
                                                    @if($request->is_trading_enabled)
                                                        <span class="badge badge-success">ACTIVE</span>
                                                    @else
                                                        <form method="POST" action="{{ route('users.enableTrading', $request->user) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-xs py-0 px-2" style="font-size: 0.7rem;">ENABLE</button>
                                                        </form>
                                                    @endif
                                                </div>
                                                
                                                <form method="POST" action="{{ route('users.updateLimits', $request->user) }}">
                                                    @csrf
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend"><span class="input-group-text bg-white" style="font-size: 0.65rem; width: 65px;">GOLD (g)</span></div>
                                                        <input type="number" name="gold_limit" class="form-control text-right" value="{{ $request->gold_limit }}" step="0.001">
                                                    </div>
                                                    <div class="input-group input-group-sm mb-1">
                                                        <div class="input-group-prepend"><span class="input-group-text bg-white" style="font-size: 0.65rem; width: 65px;">SILVER (g)</span></div>
                                                        <input type="number" name="silver_limit" class="form-control text-right" value="{{ $request->silver_limit }}" step="0.001">
                                                    </div>
                                                    <button type="submit" class="btn btn-dark btn-sm btn-block mt-1 py-1" style="font-size: 0.75rem;">UPDATE LIMITS</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection