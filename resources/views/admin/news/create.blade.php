@extends('admin.layout.main')

@section('title', 'Add News')

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1>Add News Update</h1>
            <p class="text-secondary">Publish a new alert that will instantly appear in the mobile app.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back to News
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.news.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="title" class="form-label font-weight-bold">News Headline (Visible as the main text in
                        app)</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" required
                        placeholder="e.g. Gold, silver firmer in choppy, 2-sided trading">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="content" class="form-label font-weight-bold">Detailed Content (Optional display)</label>
                    <textarea name="content" id="content" rows="4"
                        class="form-control @error('content') is-invalid @enderror" required
                        placeholder="Enter full news content here...">{{ old('content', 'Full coverage placeholder.') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-paper-plane mr-2"></i> Publish News
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection