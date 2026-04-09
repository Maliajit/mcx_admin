@extends('admin.layout.main')

@section('title', 'KYC Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">KYC Requests</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Name</th>
                                <th>PAN</th>
                                <th>Aadhaar</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kycRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->user->email }}</td>
                                <td>{{ $request->name }}</td>
                                <td>{{ $request->pan }}</td>
                                <td>{{ $request->aadhaar }}</td>
                                <td>
                                    <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                    <form method="POST" action="{{ route('admin.kyc.approve', $request) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.kyc.reject', $request) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
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
@endsection