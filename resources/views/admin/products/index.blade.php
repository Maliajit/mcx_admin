@extends('admin.layout.main')

@section('title', 'Manage Products')
@section('page_title', 'Manage Products')

@section('content')
<div class="section-card">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Product Rows</h3>
    </div>
    
    @if(session('success'))
        <div style="padding: 16px; color: green;">{{ session('success') }}</div>
    @endif

    <div style="padding: 32px;">
        <form action="{{ route('admin.products.store') }}" method="POST" style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 1px solid #ddd;">
            @csrf
            <h4>Add New Product</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="gold">Gold</option>
                        <option value="silver">Silver</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Margin (Rs)</label>
                    <input type="number" step="0.01" name="margin" class="form-control" value="0" required>
                </div>
                <div class="form-group">
                    <label>Adjustment (Rs)</label>
                    <input type="number" step="0.01" name="adjustment" class="form-control" value="0" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" checked> Active
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Margin</th>
                        <th>Adjustment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ ucfirst($product->type) }}</td>
                            <td>{{ $product->margin }}</td>
                            <td>{{ $product->adjustment }}</td>
                            <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
