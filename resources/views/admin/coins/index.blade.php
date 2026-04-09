@extends('admin.layout.main')

@section('title', 'Manage Coins')
@section('page_title', 'Manage Coins')

@section('content')
<div class="section-card">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Coins</h3>
    </div>
    
    @if(session('success'))
        <div style="padding: 16px; color: green;">{{ session('success') }}</div>
    @endif

    <div style="padding: 32px;">
        <form action="{{ route('admin.coins.store') }}" method="POST" style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 1px solid #ddd;">
            @csrf
            <h4>Add New Coin</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. 5g Gold Coin" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="gold">Gold</option>
                        <option value="silver">Silver</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Weight (g)</label>
                    <input type="number" step="0.0001" name="weight_in_grams" class="form-control" value="0.0" required>
                </div>
                <div class="form-group">
                    <label>Margin (Rs)</label>
                    <input type="number" step="0.01" name="margin" class="form-control" value="0" required>
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
                        <th>Weight</th>
                        <th>Margin</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coins as $coin)
                        <tr>
                            <td>{{ $coin->id }}</td>
                            <td>{{ $coin->name }}</td>
                            <td>{{ ucfirst($coin->type) }}</td>
                            <td>{{ $coin->weight_in_grams }} g</td>
                            <td>{{ $coin->margin }}</td>
                            <td>{{ $coin->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form action="{{ route('admin.coins.destroy', $coin->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
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
