@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'default', // 'sm', 'default', 'lg', 'xl'
    'closable' => true
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'default' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl'
    ];
    $modalSize = $sizeClasses[$size] ?? $sizeClasses['default'];
@endphp

<!-- Modal Backdrop -->
<div id="{{ $id }}" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" onclick="closeModalOnBackdrop(event, '{{ $id }}')">
    <!-- Modal Container -->
    <div class="relative top-20 mx-auto p-5 border {{ $modalSize }} shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $title }}
            </h3>
            @if($closable)
                <button type="button" onclick="closeModal('{{ $id }}')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Modal Body -->
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    function closeModalOnBackdrop(event, modalId) {
        if (event.target === event.currentTarget) {
            closeModal(modalId);
        }
    }
</script>
