@extends('admin.layout.app')

@section('title', 'Редактор зала')

@section('content')
<h1 class="text-2xl font-bold mb-4">🧩 Редактор карты заведения</h1>
<p class="text-gray-600 mb-3">Перетаскивайте столы и нажмите «Сохранить» для применения изменений.</p>

<canvas id="hallEditor" width="1000" height="600" class="border border-gray-400"></canvas>

<div class="mt-4">
    <button id="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">💾 Сохранить</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = new fabric.Canvas('hallEditor');
    const resources = @json($resources);

    const objects = {};

    resources.forEach(r => {
        const rect = new fabric.Rect({
            left: r.x,
            top: r.y,
            width: r.width,
            height: r.height,
            fill: 'rgba(72,187,120,0.6)',
            stroke: '#38A169',
            strokeWidth: 2,
            rx: 8, ry: 8,
            hasControls: true,
            hasBorders: true,
        });
        rect.set('resourceId', r.id);
        canvas.add(rect);
        objects[r.id] = rect;
    });

    // Сохранение координат
    document.getElementById('saveBtn').addEventListener('click', async () => {
        const data = Object.values(objects).map(obj => ({
            id: obj.resourceId,
            x: obj.left,
            y: obj.top,
            width: obj.width * obj.scaleX,
            height: obj.height * obj.scaleY,
            rotation: obj.angle,
        }));

        const response = await fetch('{{ route('admin.hall.save') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ resources: data }),
        });

        if (response.ok) alert('Изменения сохранены!');
    });
});
</script>
@endsection
