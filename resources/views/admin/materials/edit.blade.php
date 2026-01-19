@extends('layouts.app')

@section('title', 'Edit Material - Admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Edit Material</h1>
        <p class="mt-1 text-sm text-gray-600">Update material details</p>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('admin.materials.update', $material->id) }}" enctype="multipart/form-data" class="space-y-6 bg-white rounded-lg shadow p-6" id="materialForm">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="title" 
                id="title" 
                required
                maxlength="255"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('title', $material->title) }}"
                placeholder="Enter material title"
            >
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
                name="description" 
                id="description" 
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                placeholder="Enter material description"
            >{{ old('description', $material->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Material Type <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input 
                        type="radio" 
                        name="type" 
                        value="pdf" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        {{ old('type', $material->type) === 'pdf' ? 'checked' : '' }}
                        onchange="toggleContentField()"
                    >
                    <span class="ml-2 text-sm text-gray-700">PDF Document</span>
                </label>
                <label class="inline-flex items-center">
                    <input 
                        type="radio" 
                        name="type" 
                        value="youtube" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        {{ old('type', $material->type) === 'youtube' ? 'checked' : '' }}
                        onchange="toggleContentField()"
                    >
                    <span class="ml-2 text-sm text-gray-700">YouTube Video</span>
                </label>
            </div>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- PDF Upload Field --}}
        <div id="pdfField" class="{{ old('type', $material->type) === 'pdf' ? '' : 'hidden' }}">
            <label for="pdf_file" class="block text-sm font-medium text-gray-700">Upload PDF</label>
            @if($material->type === 'pdf')
                <p class="mt-1 text-sm text-gray-600">
                    Current file: <span class="font-medium">{{ basename($material->content) }}</span>
                </p>
            @endif
            <input 
                type="file" 
                name="pdf_file" 
                id="pdf_file" 
                accept=".pdf"
                class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100
                "
            >
            <p class="mt-1 text-xs text-gray-500">Maximum file size: {{ $maxPdfSize }} MB. Leave empty to keep current file.</p>
            @error('pdf_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- YouTube URL Field --}}
        <div id="youtubeField" class="{{ old('type', $material->type) === 'youtube' ? '' : 'hidden' }}">
            <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube URL <span class="text-red-500">*</span></label>
            <input 
                type="url" 
                name="youtube_url" 
                id="youtube_url" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('youtube_url', $material->type === 'youtube' ? $material->content : '') }}"
                placeholder="https://www.youtube.com/watch?v=..."
            >
            <p class="mt-1 text-xs text-gray-500">Enter a valid YouTube video URL</p>
            @error('youtube_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="access_type" class="block text-sm font-medium text-gray-700">Access Type <span class="text-red-500">*</span></label>
            <select 
                name="access_type" 
                id="access_type" 
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
            >
                <option value="all" {{ old('access_type', $material->access_type) === 'all' ? 'selected' : '' }}>All Members</option>
                <option value="marketing_only" {{ old('access_type', $material->access_type) === 'marketing_only' ? 'selected' : '' }}>Marketing Only</option>
                <option value="non_marketing_only" {{ old('access_type', $material->access_type) === 'non_marketing_only' ? 'selected' : '' }}>Non-Marketing Only</option>
            </select>
            @error('access_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
            <input 
                type="number" 
                name="order" 
                id="order" 
                min="0"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('order', $material->order) }}"
                placeholder="0"
            >
            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first (default: 0)</p>
            @error('order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                Update Material
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleContentField() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const pdfField = document.getElementById('pdfField');
    const youtubeField = document.getElementById('youtubeField');
    const pdfFileInput = document.getElementById('pdf_file');
    const youtubeUrlInput = document.getElementById('youtube_url');
    
    if (type === 'pdf') {
        pdfField.classList.remove('hidden');
        youtubeField.classList.add('hidden');
        youtubeUrlInput.removeAttribute('required');
    } else {
        pdfField.classList.add('hidden');
        youtubeField.classList.remove('hidden');
        pdfFileInput.removeAttribute('required');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleContentField();
});
</script>
@endpush
@endsection
