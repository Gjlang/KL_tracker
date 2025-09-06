{{-- resources/views/dashboard/master/_tabs.blade.php --}}
<div class="flex gap-2 border-b border-gray-200 mb-4">
    <a href="{{ route('dashboard.master.kltg') }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ ($active ?? '')==='kltg' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        KLTG Master Files
    </a>
    <a href="{{ route('dashboard.master.outdoor') }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ ($active ?? '')==='outdoor' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Outdoor Master Files
    </a>
</div>
