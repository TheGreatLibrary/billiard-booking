<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 p-4 sm:p-6">
        <div class="max-w-screen-2xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Редактор зон</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Рисуйте зоны на карте зала</p>
                </div>
                <a href="{{ route('admin.zones.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">← К списку зон</a>
            </div>

            @if (session()->has('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Заведение</label>
                <select wire:model.live="placeId"
                        class="w-full max-w-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Выберите заведение</option>
                    @foreach($places as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($place)
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

                    {{-- КАРТА: полный wire:ignore, Alpine управляет всем --}}
                    <div class="xl:col-span-3">
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $place->name }}</h2>
                                    <span class="text-xs text-gray-400">{{ $gridWidth }}×{{ $gridHeight }}</span>
                                </div>
                                <div id="zm-indicator"></div>
                            </div>

                            <div wire:ignore>
                                <div id="zone-map-root"></div>
                            </div>
                        </div>
                    </div>

                    {{-- БОКОВАЯ ПАНЕЛЬ --}}
                    <div class="xl:col-span-1 space-y-5">
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5
                                    {{ $editingZoneId ? 'ring-2 ring-blue-500/30' : '' }}">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">
                                {{ $editingZoneId ? 'Редактирование зоны' : 'Новая зона' }}
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Название</label>
                                    <input type="text" wire:model="zoneName" placeholder="VIP зона"
                                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('zoneName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Цвет</label>
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        @foreach($colorPresets as $c)
                                            <button type="button" wire:click="$set('zoneColor', '{{ $c }}')"
                                                    class="w-7 h-7 rounded-lg border-2 transition-transform hover:scale-110
                                                           {{ $zoneColor === $c ? 'border-gray-900 dark:border-white scale-110 shadow-md' : 'border-transparent' }}"
                                                    style="background-color: {{ $c }};"></button>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="color" wire:model.live="zoneColor" class="h-8 w-12 border-gray-300 rounded-lg cursor-pointer">
                                        <input type="text" wire:model.live="zoneColor"
                                               class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg text-xs focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Коэффициент цены</label>
                                    <input type="number" step="0.1" min="0" wire:model="zonePriceCoef" placeholder="1.0"
                                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-[10px] text-gray-400 mt-1">1.0 = базовая, 1.5 = +50%, 0.8 = −20%</p>
                                    @error('zonePriceCoef') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div id="zm-cell-count" class="bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ячеек: <strong class="text-blue-600">0</strong>
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button type="button" wire:click="resetForm"
                                            class="flex-1 px-3 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">
                                        Отмена
                                    </button>
                                    <button type="button" id="zm-save-btn"
                                            class="flex-1 px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition shadow-sm">
                                        {{ $editingZoneId ? 'Сохранить' : 'Создать' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                                Зоны <span class="text-gray-400 font-normal">({{ count($zones) }})</span>
                            </h3>
                            @if(count($zones) === 0)
                                <p class="text-sm text-gray-400 text-center py-6">Зоны не созданы</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($zones as $zone)
                                        <div class="group flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-800 hover:bg-gray-50 transition
                                                    {{ $editingZoneId === $zone['id'] ? 'ring-2 ring-blue-500/50 bg-blue-50/50' : '' }}">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="w-4 h-4 rounded-full flex-shrink-0 shadow-sm" style="background-color:{{ $zone['color'] }}"></div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $zone['name'] }}</p>
                                                    <p class="text-[10px] text-gray-400">×{{ $zone['price_coef'] }} · {{ count($zone['coordinates']) }} яч.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="editZone({{ $zone['id'] }})" class="p-1.5 text-blue-500 hover:bg-blue-100 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button wire:click="deleteZone({{ $zone['id'] }})" wire:confirm="Удалить зону «{{ $zone['name'] }}»?" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
                    <p class="text-sm text-gray-500">Выберите заведение</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 
    Вся логика карты — чистый JS, вне Blade.
    Не использует Alpine, не использует x-data.
    Livewire общается через dispatch events.
    Карта рендерится через innerHTML.
--}}
<script>
(function(){
    var ZM = {
        gw: {{ $gridWidth ?? 20 }},
        gh: {{ $gridHeight ?? 10 }},
        zones: {!! json_encode($zones ?? []) !!},
        cells: [],
        editId: null,
        color: '#3B82F6',
        painting: false,
        erasing: false,
        last: null,
        root: null,
        overlay: null,

        boot: function() {
            this.root = document.getElementById('zone-map-root');
            this.bindLivewire();
            if (this.root) {
                this.render();
                this.bindEvents();
                this.updateCounter();
            }
        },

        findRoot: function() {
            this.root = document.getElementById('zone-map-root');
            return !!this.root;
        },

        render: function() {
            var pct = (this.gh / this.gw * 100);
            var gridBg = 'background-image:linear-gradient(rgba(0,0,0,.2) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.2) 1px,transparent 1px);background-size:' + (100/this.gw) + '% ' + (100/this.gh) + '%';

            var html = '<div style="position:relative;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;background:#f8fafc">';
            html += '<div style="position:absolute;inset:0;pointer-events:none;opacity:0.08;' + gridBg + '"></div>';
            html += '<div style="position:relative;width:100%;padding-bottom:' + pct + '%">';

            // Zones
            for (var i = 0; i < this.zones.length; i++) {
                var z = this.zones[i];
                var coords = z.coordinates || [];
                var op = (this.editId === z.id) ? 0.15 : 0.35;
                for (var j = 0; j < coords.length; j++) {
                    var c = coords[j];
                    html += '<div style="position:absolute;pointer-events:none;left:' + (c.x/this.gw*100) + '%;top:' + (c.y/this.gh*100) + '%;width:' + (100/this.gw) + '%;height:' + (100/this.gh) + '%;background:' + z.color + ';opacity:' + op + '"></div>';
                }
            }

            // Selected cells
            for (var i = 0; i < this.cells.length; i++) {
                var c = this.cells[i];
                html += '<div style="position:absolute;pointer-events:none;border:2px solid rgba(255,255,255,0.6);border-radius:2px;left:' + (c.x/this.gw*100) + '%;top:' + (c.y/this.gh*100) + '%;width:' + (100/this.gw) + '%;height:' + (100/this.gh) + '%;background:' + this.color + 'cc"></div>';
            }

            // Zone labels
            for (var i = 0; i < this.zones.length; i++) {
                var z = this.zones[i];
                var cs = z.coordinates || [];
                if (!cs.length) continue;
                var x1=Infinity,y1=Infinity,x2=0,y2=0;
                for(var j=0;j<cs.length;j++){x1=Math.min(x1,cs[j].x);y1=Math.min(y1,cs[j].y);x2=Math.max(x2,cs[j].x);y2=Math.max(y2,cs[j].y);}
                html += '<div style="position:absolute;pointer-events:none;display:flex;align-items:center;justify-content:center;left:'+(x1/this.gw*100)+'%;top:'+(y1/this.gh*100)+'%;width:'+((x2-x1+1)/this.gw*100)+'%;height:'+((y2-y1+1)/this.gh*100)+'%"><span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:'+z.color+';filter:drop-shadow(0 1px 1px rgba(0,0,0,.15))">'+z.name+'</span></div>';
            }

            // Overlay
            html += '<div id="zm-overlay" style="position:absolute;inset:0;z-index:50;cursor:crosshair;touch-action:none"></div>';
            html += '</div></div>';
            html += '<div style="margin-top:8px;display:flex;justify-content:space-between;font-size:12px;color:#9ca3af"><div><span style="margin-right:16px">ЛКМ — рисовать</span><span>ПКМ — стирать</span></div>';
            if (this.cells.length > 0) {
                html += '<button id="zm-clear" style="color:#ef4444;cursor:pointer;background:none;border:none;font-size:12px">Очистить</button>';
            }
            html += '</div>';

            this.root.innerHTML = html;
            this.overlay = document.getElementById('zm-overlay');

            var clearBtn = document.getElementById('zm-clear');
            if (clearBtn) {
                var self = this;
                clearBtn.onclick = function() { self.cells = []; self.render(); self.updateCounter(); };
            }
        },

        bindEvents: function() {
            var self = this;

            this.root.addEventListener('pointerdown', function(e) {
                if (e.target.id !== 'zm-overlay') return;
                e.preventDefault();
                e.stopPropagation();
                self.painting = true;
                self.erasing = (e.button === 2);
                self.last = null;
                self.applyAt(e);
                try { e.target.setPointerCapture(e.pointerId); } catch(err){}
            });

            this.root.addEventListener('pointermove', function(e) {
                if (!self.painting) return;
                e.preventDefault();
                self.applyAt(e);
            });

            this.root.addEventListener('pointerup', function(e) {
                self.painting = false;
                self.last = null;
            });

            this.root.addEventListener('pointerleave', function() {
                self.painting = false;
                self.last = null;
            });

            this.root.addEventListener('contextmenu', function(e) { e.preventDefault(); });

            // Save button
            var saveBtn = document.getElementById('zm-save-btn');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    if (!self.cells.length) return;
                    var wireEl = document.querySelector('[wire\\:id]');
                    if (wireEl) {
                        Livewire.find(wireEl.getAttribute('wire:id')).call('saveZoneWithCells', JSON.parse(JSON.stringify(self.cells)));
                    }
                });
            }
        },

        bindLivewire: function() {
            var self = this;

            Livewire.on('zm:place-changed', function(data) {
                var d = data[0];
                self.gw = d.gw;
                self.gh = d.gh;
                self.zones = d.zones;
                self.cells = [];
                self.editId = null;
                // DOM мог появиться после Livewire рендера — ищем root заново
                setTimeout(function() {
                    if (self.findRoot()) {
                        self.render();
                        self.bindEvents();
                        self.updateCounter();
                    }
                }, 50);
            });

            Livewire.on('zm:zones-updated', function(data) {
                self.zones = data[0].zones;
                self.cells = [];
                self.editId = null;
                if (self.findRoot()) {
                    self.render();
                    self.bindOverlayOnly();
                    self.updateCounter();
                }
            });

            Livewire.on('zm:edit-zone', function(data) {
                var d = data[0];
                self.editId = d.id;
                self.cells = JSON.parse(JSON.stringify(d.cells || []));
                self.color = d.color;
                if (self.findRoot()) {
                    self.render();
                    self.bindOverlayOnly();
                    self.updateCounter();
                }
            });

            Livewire.on('zm:reset', function() {
                self.cells = [];
                self.editId = null;
                self.color = '#3B82F6';
                if (self.findRoot()) {
                    self.render();
                    self.bindOverlayOnly();
                    self.updateCounter();
                }
            });

            // Ловим смену цвета из Livewire
            // Livewire 3: отслеживаем изменения $zoneColor
            var observer = new MutationObserver(function() {
                var colorInput = document.querySelector('input[type="color"]');
                if (colorInput && colorInput.value !== self.color) {
                    self.color = colorInput.value;
                    if (self.cells.length > 0) { self.render(); self.bindOverlayOnly(); }
                }
            });
            observer.observe(document.body, { subtree: true, attributes: true, attributeFilter: ['value'] });

            // Также слушаем input события на color picker
            document.addEventListener('input', function(e) {
                if (e.target && e.target.type === 'color') {
                    self.color = e.target.value;
                    if (self.cells.length > 0) { self.render(); self.bindOverlayOnly(); }
                }
            });
        },

        bindOverlayOnly: function() {
            this.overlay = document.getElementById('zm-overlay');
            var clearBtn = document.getElementById('zm-clear');
            if (clearBtn) {
                var self = this;
                clearBtn.onclick = function() { self.cells = []; self.render(); self.bindOverlayOnly(); self.updateCounter(); };
            }
        },

        applyAt: function(e) {
            if (!this.overlay) return;
            var r = this.overlay.getBoundingClientRect();
            var rx = e.clientX - r.left, ry = e.clientY - r.top;
            if (rx < 0 || ry < 0 || rx > r.width || ry > r.height) return;
            var x = Math.max(0, Math.min(this.gw-1, Math.floor((rx/r.width)*this.gw)));
            var y = Math.max(0, Math.min(this.gh-1, Math.floor((ry/r.height)*this.gh)));
            if (this.last && this.last.x === x && this.last.y === y) return;
            this.last = {x:x, y:y};

            var idx = -1;
            for (var i=0; i<this.cells.length; i++) {
                if (this.cells[i].x === x && this.cells[i].y === y) { idx = i; break; }
            }

            if (this.erasing) {
                if (idx !== -1) this.cells.splice(idx, 1);
            } else {
                if (idx === -1) this.cells.push({x:x, y:y});
            }

            this.render();
            this.bindOverlayOnly();
            this.updateCounter();
        },

        updateCounter: function() {
            var el = document.getElementById('zm-cell-count');
            if (el) el.innerHTML = 'Ячеек: <strong class="text-blue-600">' + this.cells.length + '</strong>';
        }
    };

    // Boot when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { ZM.boot(); });
    } else {
        ZM.boot();
    }

    // Re-boot after Livewire navigation
    document.addEventListener('livewire:navigated', function() { ZM.boot(); });
})();
</script>