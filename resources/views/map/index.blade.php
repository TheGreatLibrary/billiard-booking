@extends('admin.layout.app') {{-- или другой layout, если публично --}}

@section('title', 'Карта зала')

@section('content')
<h1 class="text-2xl font-bold mb-4">Карта заведения</h1>

<div id="hall" class="relative bg-gray-100 border border-gray-300 rounded-lg" style="width:100%; height:600px;">
    @foreach($resources as $r)
        <div class="table-item absolute"
             data-id="{{ $r->id }}"
             data-shape="{{ $r->shape }}"
             data-image="{{ $r->image_url ?? '' }}"
             style="
                left: {{ $r->x }}px;
                top: {{ $r->y }}px;
                width: {{ $r->width }}px;
                height: {{ $r->height }}px;
                transform: translate(0,0) rotate({{ $r->rotation }}deg);
             ">
            {{ $r->code ?? $r->id }}
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    function applyShapeStyles(el) {
        const shape = el.dataset.shape || 'rect';
        const image = el.dataset.image || '';
        el.style.border = '2px solid #1f2937';
        el.style.background = '#1f2937';
        el.style.borderRadius = '0';
        el.style.backgroundSize = 'cover';
        el.style.backgroundPosition = 'center';
        if (shape === 'circle') el.style.borderRadius = '50%';
        else if (shape === 'ellipse') el.style.borderRadius = '50% / 30%';
        else if (shape === 'image' && image) el.style.background = `url(${image}) center/cover no-repeat`;
        else { el.style.borderRadius = '6px'; el.style.background = '#1f2937'; }
        el.style.color = '#fff';
    }

    document.querySelectorAll('.table-item').forEach(el => applyShapeStyles(el));

    // клик — выбрать и открыть окно бронирования
    document.querySelectorAll('.table-item').forEach(el => {
        el.addEventListener('click', () => {
            const id = el.dataset.id;
            // можно перенаправить на страницу создания брони с preselected resource_id
            window.location.href = `{{ url('/bookings/create') }}?resource_id=${id}`;
        });
    });
});
</script>

<style>
.table-item { position: absolute; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; cursor:pointer; user-select:none; }
</style>
@endsection
