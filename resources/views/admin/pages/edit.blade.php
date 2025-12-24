@extends('admin.layout')

@section('title', 'Edit Page - Admin')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Edit Page</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               name="title" value="{{ old('title', $page->title) }}" required>
                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text">/pages/</span>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   name="slug" value="{{ old('slug', $page->slug) }}" required>
                        </div>
                        @error('slug')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content (HTML)</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  name="content" rows="8" required>{{ old('content', $page->content) }}</textarea>
                        @error('content')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <input type="text" class="form-control @error('meta_description') is-invalid @enderror" 
                               name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" placeholder="SEO description">
                        @error('meta_description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                               name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="keyword1, keyword2">
                        @error('meta_keywords')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" id="is_published" value="1" 
                               {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            Published
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Page
                        </button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
