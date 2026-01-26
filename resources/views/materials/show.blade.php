@extends('layouts.app')

@section('title', $material->title . ' - Sumber Belajar')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('materials.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Materi
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @if($material->type === 'youtube')
                    {{-- YouTube Player --}}
                    <div class="relative pb-[56.25%] h-0">
                        <iframe 
                            id="video-player"
                            class="absolute top-0 left-0 w-full h-full"
                            src="{{ $material->youtube_embed_url }}"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            controlsList="nodownload"
                        ></iframe>
                    </div>
                @else
                    {{-- PDF Viewer --}}
                    <div class="relative bg-gray-100" style="height: 600px;">
                        <iframe 
                            id="pdf-viewer"
                            class="w-full h-full"
                            src="{{ $material->pdf_url }}#toolbar=0"
                            type="application/pdf"
                        ></iframe>
                        <div class="absolute inset-0 pointer-events-none" style="background: transparent;"></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6 sticky top-4">
                {{-- Material Info --}}
                <div>
                    <div class="mb-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                            {{ $material->type === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800' }}
                        ">
                            @if($material->type === 'pdf')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                PDF Document
                            @else
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                                YouTube Video
                            @endif
                        </span>
                    </div>

                    <h1 class="text-xl font-bold text-gray-900 mb-3">
                        {{ $material->title }}
                    </h1>

                    @if($material->description)
                        <p class="text-sm text-gray-600">
                            {{ $material->description }}
                        </p>
                    @endif
                </div>

                {{-- Completion Status --}}
                <div class="border-t pt-6">
                    <div id="completion-status">
                        @if($completion)
                            <div class="flex items-center text-green-600 mb-3">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold">Selesai</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                Diselesaikan pada {{ $completion->completed_at->format('d M Y, H:i') }}
                            </p>
                        @else
                            <button 
                                id="complete-btn"
                                onclick="markAsCompleted('{{ $material->id }}')"
                                class="w-full inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Selesai
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Notes/Tips --}}
                <div class="border-t pt-6">
                    <div class="rounded-lg bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Pelajari materi dengan seksama untuk memaksimalkan pembelajaran Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Disable right-click on PDF and video
document.addEventListener('DOMContentLoaded', function() {
    const pdfViewer = document.getElementById('pdf-viewer');
    const videoPlayer = document.getElementById('video-player');
    
    if (pdfViewer) {
        pdfViewer.addEventListener('contextmenu', (e) => e.preventDefault());
    }
    
    if (videoPlayer) {
        videoPlayer.addEventListener('contextmenu', (e) => e.preventDefault());
    }
    
    // Disable keyboard shortcuts for download/print
    document.addEventListener('keydown', function(e) {
        // Disable Ctrl+P (print)
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            return false;
        }
        // Disable Ctrl+S (save)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            return false;
        }
    });
});

// AJAX mark as completed
function markAsCompleted(materialId) {
    fetch(`/materials/${materialId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const statusDiv = document.getElementById('completion-status');
            const completedDate = new Date(data.completed_at);
            const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const formattedDate = completedDate.toLocaleDateString('id-ID', options);
            
            statusDiv.innerHTML = `
                <div class="flex items-center text-green-600 mb-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Selesai</span>
                </div>
                <p class="text-xs text-gray-500">
                    Diselesaikan pada ${formattedDate}
                </p>
            `;
            
            // Show success message
            alert('Materi berhasil ditandai sebagai selesai!');
        } else {
            alert('Gagal menandai materi sebagai selesai: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}
</script>
@endpush
@endsection
