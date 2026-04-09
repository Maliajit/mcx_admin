@extends('admin.layout.main')

@section('title', 'Trading Settings')
@section('page_title', 'Trading Settings')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 32px;">
    <!-- General Trading Settings -->
    <div class="section-card">
        <div class="section-header">
            <h3>Global Pricing Settings</h3>
        </div>
        <div style="padding: 32px;">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                @if(session('success'))
                    <div style="color: green; margin-bottom: 16px;">{{ session('success') }}</div>
                @endif
                
                <div class="form-group">
                    <label>Price Source</label>
                    <select name="price_source" class="form-control">
                        <option value="manual" {{ ($settings['price_source'] ?? 'manual') === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="api" {{ ($settings['price_source'] ?? '') === 'api' ? 'selected' : '' }}>API (Live Rates)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gold_base_price">Gold Base Price (Manual)</label>
                    <input type="number" step="0.01" name="gold_base_price" id="gold_base_price" class="form-control" value="{{ $settings['gold_base_price'] ?? '0' }}">
                </div>
                <div class="form-group">
                    <label for="silver_base_price">Silver Base Price (Manual)</label>
                    <input type="number" step="0.01" name="silver_base_price" id="silver_base_price" class="form-control" value="{{ $settings['silver_base_price'] ?? '0' }}">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Base Prices</button>
            </form>
        </div>
    </div>

    <!-- Taxes and Theme -->
    <div class="section-card">
        <div class="section-header">
            <h3>Taxes & App Theme</h3>
        </div>
        <div style="padding: 32px;">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="gst_percentage">GST Percentage (%)</label>
                    <input type="number" step="0.01" name="gst_percentage" id="gst_percentage" class="form-control" value="{{ $settings['gst_percentage'] ?? '3.0' }}">
                </div>
                <div class="form-group">
                    <label for="tds_percentage">TDS Percentage (%)</label>
                    <input type="number" step="0.01" name="tds_percentage" id="tds_percentage" class="form-control" value="{{ $settings['tds_percentage'] ?? '1.0' }}">
                </div>
                <div class="form-group">
                    <label for="primary_color">App Primary Color</label>
                    <input type="color" name="primary_color" id="primary_color" class="form-control" style="height: 50px;" value="{{ $settings['primary_color'] ?? '#FFAA00' }}">
                </div>
                <div class="form-group">
                    <label for="secondary_color">App Secondary Color</label>
                    <input type="color" name="secondary_color" id="secondary_color" class="form-control" style="height: 50px;" value="{{ $settings['secondary_color'] ?? '#000000' }}">
                </div>
                
                <button type="submit" class="btn btn-accent" style="width: 100%;">Update Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection
