@extends('layouts.app')

@section('content')
<div class="w-screen min-h-screen bg-[#F7F7F9]">
  <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">Add Backlog Entry</h1>
        <p class="text-sm text-neutral-500">Create a new client feed/backlog item</p>
      </div>
      <a href="{{ route('information.booth.index') }}"
         class="rounded border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50">
        Back to List
      </a>
    </div>

    {{-- Full Form --}}
    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-6">
      <form action="{{ route('information.booth.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Date</label>
            <input type="date" name="date" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Expected Finish Date</label>
            <input type="date" name="expected_finish_date" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Company</label>
            <input type="text" name="company" placeholder="Company name" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Client</label>
            <input type="text" name="client" placeholder="Client name" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Product</label>
            <input type="text" name="product" placeholder="Product name" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Servicing</label>
            <input type="text" name="servicing" placeholder="Service type" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Location</label>
            <input type="text" name="location" placeholder="Service location" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Attended By</label>
            <input type="text" name="attended_by" placeholder="PIC / Team member" class="w-full rounded-xl border-neutral-300">
          </div>

          <div>
            <label class="block text-xs font-medium text-neutral-600 mb-1">Status</label>
            <select name="status" class="w-full rounded-xl border-neutral-300">
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div class="md:col-span-2 xl:col-span-3">
            <label class="block text-xs font-medium text-neutral-600 mb-1">Reasons</label>
            <textarea name="reasons" rows="2" placeholder="Notes / follow-up reasons"
                      class="w-full rounded-xl border-neutral-300"></textarea>
          </div>
        </div>

        <div class="pt-4">
          <button type="submit"
                  class="rounded-xl bg-neutral-900 text-white px-4 py-2 text-sm hover:bg-neutral-800">
            Save Entry

          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
