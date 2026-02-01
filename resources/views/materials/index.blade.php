@extends('layouts.app')

@section('title', 'Sumber Belajar')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Produk AISosial</h1>
        <p class="mt-1 text-sm text-gray-600">Akses produk dari AISosial</p>
    </div>

    {{-- Materials Grid --}}
    @if($materials->count() > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($materials as $material)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    {{-- Thumbnail --}}
                    <div class="relative h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                        @if($material->type === 'pdf')
                            {{-- PDF Icon --}}
                            <svg class="w-20 h-20 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            {{-- YouTube Thumbnail --}}
                            <div class="relative w-full h-full">
                                @php
                                    $videoId = null;
                                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $material->content, $matches);
                                    $videoId = $matches[1] ?? null;
                                @endphp
                                @if($videoId)
                                    <img 
                                        src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg" 
                                        alt="{{ $material->title }}"
                                        class="w-full h-full object-cover"
                                    >
                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                        <svg class="w-16 h-16 text-white opacity-90" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                        </svg>
                                    </div>
                                @else
                                    <svg class="w-20 h-20 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                @endif
                            </div>
                        @endif
                        
                        {{-- Completed Badge --}}
                        @if($material->is_completed)
                            <div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        {{-- Type Badge --}}
                        <div class="mb-2">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                                {{ $material->type === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800' }}
                            ">
                                @if($material->type === 'pdf')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                    </svg>
                                    PDF
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                    YouTube
                                @endif
                            </span>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ $material->title }}
                        </h3>

                        {{-- Description --}}
                        @if($material->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ $material->description }}
                            </p>
                        @endif

                        {{-- Action Button --}}
                        <a href="{{ route('materials.show', $material->id) }}" class="inline-flex items-center justify-center w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                            @if($material->is_completed)
                                Lihat Lagi
                            @else
                                Mulai Belajar
                            @endif
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Materi</h3>
            <p class="mt-2 text-sm text-gray-500">Materi pembelajaran belum tersedia untuk Anda.</p>
        </div>
    @endif
</div>
@endsection
