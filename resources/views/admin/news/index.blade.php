@extends('admin.layout.main')

@section('title', 'News Updates')

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1>News Updates</h1>
            <p class="text-secondary">Manage news alerts shown in the mobile application.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add News
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Published At</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $post)
                    <tr>
                        <td class="font-weight-bold">{{ Str::limit($post->title, 40) }}</td>
                        <td>{{ Str::limit($post->content, 60) }}</td>
                        <td>{{ $post->published_at ? $post->published_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.news.destroy', $post) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this news post?');"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-secondary">
                            No news updates found. Click "Add News" to publish an alert.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($news->hasPages())
            <div class="mt-4 px-3">
                {{ $news->links() }}
            </div>
        @endif
    </div>
@endsection