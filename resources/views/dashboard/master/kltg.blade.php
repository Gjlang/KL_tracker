@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>window.mfBatchMode = true;</script>
@endpush

@section('content')
<div class="mx-auto max-w-7xl p-4">
  @include('dashboard.master._tabs', ['active' => $active ?? 'kltg'])

  <div class="flex items-center justify-between mb-3">
    <h1 class="text-xl font-semibold">KLTG MASTER CLIENTELE</h1>

    {{-- Sticky top-right Save --}}
    <button id="mf-save-all"
      class="sticky top-2 z-30 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-50">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2"/></svg>
      Save changes
    </button>
  </div>

  @include('dashboard.master._filters', [
      'action'   => route('dashboard.master.kltg'),
      'clearUrl' => route('dashboard.master.kltg'),
  ])

  @include('dashboard.master._table', [
      'rows'    => $rows,
      'columns' => $columns,

      'editable' => [
        'company'           => 'text',
        'product'           => 'text',
        'product_category'  => 'text',
        'kltg_industry'     => 'text',
        'kltg_x'            => 'text',
        'kltg_edition'      => 'text',
        'kltg_material_cbp' => 'text',
        'kltg_print'        => 'text',
        'kltg_article'      => 'text',
        'kltg_video'        => 'text',
        'kltg_leaderboard'  => 'text',
        'kltg_qr_code'      => 'text',
        'kltg_blog'         => 'text',
        'kltg_em'           => 'text',
        'kltg_remarks'      => 'text',
        'date'              => 'date',
        'date_finish'       => 'date',
        'barter'            => 'text',
      ],

      'updateUrl'          => route('clientele.inline.update'), // not used in batch, harmless
      'updatePayloadExtra' => ['scope' => 'kltg'],
  ])
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const saveBtn = document.getElementById('mf-save-all');
  const batchUrl = @json(route('clientele.batch.update'));

  // Dirty store: key -> {id, column, value, extra}
  const dirty = new Map();

  const keyOf = (el) => {
    const extra = (() => { try { return JSON.parse(el.dataset.extra||'{}'); } catch { return {}; }})();
    const oi = extra.outdoor_item_id ?? ''; // KLTG usually none
    return `${el.dataset.id}:${el.dataset.col}:${oi}`;
  };

  const markDirty = (el) => {
    const extra = (() => { try { return JSON.parse(el.dataset.extra||'{}'); } catch { return {}; }})();
    const value = (el.type === 'checkbox') ? (el.checked ? 1 : 0) : el.value;
    const payload = { id: Number(el.dataset.id), column: el.dataset.col, value, ...extra };
    dirty.set(keyOf(el), payload);
    el.classList.add('ring-2','ring-yellow-300'); // cue: changed but unsaved
  };

  // Intercept edits (batch mode means no autosave)
  document.body.addEventListener('change', (e) => {
    if (e.target.classList.contains('mf-edit')) markDirty(e.target);
  });
  document.body.addEventListener('input', (e) => {
    if (e.target.classList.contains('mf-edit')) markDirty(e.target);
  });

  saveBtn.addEventListener('click', async () => {
    if (dirty.size === 0) return;

    const changes = Array.from(dirty.values());
    saveBtn.disabled = true;

    try {
      const res = await fetch(batchUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
        body: JSON.stringify({ scope: 'kltg', changes })
      });
      const json = await res.json();

      if (!res.ok) throw new Error(json?.message || 'Batch save failed');

      // success: clear cues
      const keys = new Set(changes.map(c => `${c.id}:${c.column}:${c.outdoor_item_id??''}`));
      document.querySelectorAll('.mf-edit').forEach(el => {
        if (keys.has(keyOf(el))) {
          el.classList.remove('ring-yellow-300');
          el.classList.add('ring-2','ring-green-300');
          setTimeout(()=> el.classList.remove('ring-2','ring-green-300'), 800);
        }
      });
      dirty.clear();

      if (json.failed?.length) {
        alert('Some cells failed to save:\n' + json.failed.map(f => `#${f.id}.${f.column}: ${f.error}`).join('\n'));
      }
    } catch (e) {
      console.error(e);
      alert(e.message);
    } finally {
      saveBtn.disabled = false;
    }
  });
});
</script>
@endpush
