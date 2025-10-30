@extends('admin.layout.app')

@section('title', 'Карта зала')

@section('content')
<h1 class="text-2xl font-bold mb-4"> Редактор карты зала</h1>
<p class="text-sm text-gray-600 mb-4">Перетаскивай столы. Выбери стол → поменяй форму → Сохранить.</p>

<div id="hall" class="relative bg-gray-100 border border-gray-300 rounded-lg" style="width:100%; height:600px;">
    @foreach($resources as $r)
        <div class="table-item absolute flex items-center justify-center text-sm text-white"
             data-id="{{ $r->id }}"
             data-shape="{{ $r->shape }}"
             data-image="{{ $r->image_url ?? '' }}"
             data-x="{{ $r->x }}" data-y="{{ $r->y }}" data-rotation="{{ $r->rotation }}"
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

<!-- Панель редактирования выбранного стола -->
<div class="mt-4 flex gap-3 items-start">
    <div>
        <label class="block text-sm font-medium">Форма</label>
        <select id="shapeSelect" class="border rounded px-2 py-1">
            <option value="rect">Прямоугольник</option>
            <option value="circle">Круг</option>
            <option value="ellipse">Эллипс</option>
            <option value="image">Изображение</option>
        </select>
    </div>

    <div id="imageUrlWrap" style="display:none;">
        <label class="block text-sm font-medium">URL изображения</label>
        <input id="imageUrl" type="text" class="border rounded px-2 py-1" placeholder="https://...">
    </div>

    <div>
        <button id="applyShape" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Применить</button>
    </div>

    <div class="ml-auto">
        <button id="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"> Сохранить</button>
    </div>
</div>

<!-- Interact.js (перетаскивание/масштабирование) -->
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация: выставляем CSS для фигур по shape
    function applyShapeStyles(el) {
        const shape = el.dataset.shape || 'rect';
        const image = el.dataset.image || '';
        el.style.border = '2px solid #16a34a';
        el.style.background = '#16a34a';
        el.style.borderRadius = '0';
        el.style.backgroundSize = 'cover';
        el.style.backgroundPosition = 'center';

        if (shape === 'circle') {
            el.style.borderRadius = '50%';
        } else if (shape === 'ellipse') {
            el.style.borderRadius = '50% / 30%';
        } else if (shape === 'image' && image) {
            el.style.background = `url(${image}) center/cover no-repeat`;
            el.style.border = '2px solid rgba(0,0,0,0.2)';
        } else {
            // rect
            el.style.borderRadius = '6px';
            el.style.background = '#16a34a';
        }
    }

    const items = document.querySelectorAll('.table-item');
    items.forEach(el => {
        // начальная стилизация
        applyShapeStyles(el);
        // init dataset position storage
        el.dataset.x = el.dataset.x || 0;
        el.dataset.y = el.dataset.y || 0;
    });

    // Interact: drag + resize (resize — опция)
    interact('.table-item').draggable({
        inertia: true,
        modifiers: [ interact.modifiers.restrictRect({ restriction: '#hall', endOnly: true }) ],
        listeners: {
            move (event) {
                const target = event.target;
                const dx = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                const dy = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                target.style.transform = `translate(${dx}px, ${dy}px) rotate(${target.dataset.rotation || 0}deg)`;
                target.setAttribute('data-x', dx);
                target.setAttribute('data-y', dy);
            }
        }
    }).resizable({
        edges: { left: true, right: true, bottom: true, top: true },
        modifiers: [ interact.modifiers.restrictSize({ min: { width: 30, height: 30 }, max: { width: 800, height: 800 } }) ],
        listeners: {
            move (event) {
                const target = event.target;
                let x = parseFloat(target.getAttribute('data-x')) || 0;
                let y = parseFloat(target.getAttribute('data-y')) || 0;

                // update the element's style
                target.style.width = event.rect.width + 'px';
                target.style.height = event.rect.height + 'px';

                // translate when resizing from top or left edges
                x += event.deltaRect.left;
                y += event.deltaRect.top;

                target.style.transform = `translate(${x}px, ${y}px) rotate(${target.dataset.rotation || 0}deg)`;
                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
            }
        }
    });

    // Выбор элемента
    let selected = null;
    document.addEventListener('click', (e) => {
        const el = e.target.closest('.table-item');
        if (!el) return;
        selected = el;
        // отметим
        document.querySelectorAll('.table-item').forEach(x => x.style.outline = 'none');
        el.style.outline = '3px dashed rgba(59,130,246,0.6)';
        // загрузим данные в панель
        const shape = el.dataset.shape || 'rect';
        const image = el.dataset.image || '';
        document.getElementById('shapeSelect').value = shape;
        document.getElementById('imageUrl').value = image;
        document.getElementById('imageUrlWrap').style.display = (shape === 'image') ? 'block' : 'none';
    });

    // UI: показать/скрыть поле URL
    document.getElementById('shapeSelect').addEventListener('change', (e) => {
        document.getElementById('imageUrlWrap').style.display = (e.target.value === 'image') ? 'block' : 'none';
    });

    // Применить форму к выбранному
    document.getElementById('applyShape').addEventListener('click', () => {
        if (!selected) { alert('Выберите стол на карте'); return; }
        const shape = document.getElementById('shapeSelect').value;
        const imageUrl = document.getElementById('imageUrl').value.trim();
        selected.dataset.shape = shape;
        selected.dataset.image = imageUrl;
        applyShapeStyles(selected);
    });

    // Сохранить все позиции (и формы)
    document.getElementById('saveBtn').addEventListener('click', async () => {
        const nodes = Array.from(document.querySelectorAll('.table-item'));
        const positions = nodes.map(el => ({
            id: el.dataset.id,
            x: parseFloat(el.dataset.x || 0),
            y: parseFloat(el.dataset.y || 0),
            width: el.offsetWidth,
            height: el.offsetHeight,
            rotation: parseFloat(el.dataset.rotation || 0),
            shape: el.dataset.shape || 'rect',
            image_url: el.dataset.image || null,
        }));

        const resp = await fetch('{{ route('admin.hall.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ positions })
        });

        if (resp.ok) {
            alert('Сохранено');
        } else {
            alert('Ошибка при сохранении');
        }
    });
});
</script>

<style>
.table-item {
    cursor: move;
    user-select: none;
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    box-sizing: border-box;
}
</style>
@endsection
