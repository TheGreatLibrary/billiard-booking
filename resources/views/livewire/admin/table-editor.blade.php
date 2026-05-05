<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 p-4 sm:p-6">
        <div class="max-w-screen-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Редактор столов</h1>
                <p class="text-sm text-gray-500 mt-1">Размещайте и перетаскивайте столы на карте зала</p>
            </div>

            @if (session()->has('warning'))
                <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl text-sm">{{ session('warning') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Заведение</label>
                <select wire:model.live="placeId"
                        class="w-full max-w-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Выберите заведение</option>
                    @foreach($places as $place)
                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($place)
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                    {{-- КАРТА --}}
                    <div class="xl:col-span-3">
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $place->name }}</h2>
                                    <span class="text-xs text-gray-400">{{ $gridWidth }}×{{ $gridHeight }}</span>
                                </div>
                                @if($selectedTableId)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full animate-pulse">
                                        Кликните на карту для размещения
                                    </span>
                                @endif
                            </div>

                            <div wire:ignore>
                                <div id="te-map-root"></div>
                            </div>

                            {{-- Легенда --}}
                            @if(count($zones) > 0)
                                <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                    @foreach($zones as $zone)
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-3 h-3 rounded-full" style="background:{{ $zone['color'] }}"></div>
                                            <span>{{ $zone['name'] }} ×{{ $zone['price_coef'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- БОКОВАЯ ПАНЕЛЬ --}}
                    <div class="xl:col-span-1 space-y-5">
                        {{-- Выбранный стол на карте --}}
                        @if($selectedGridTableId)
                            @php $sel = collect($tablesOnGrid)->firstWhere('id', $selectedGridTableId); @endphp
                            @if($sel)
                                <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-400 dark:border-amber-600 rounded-xl p-5">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Управление: {{ $sel['code'] }}</h3>
                                    <div class="space-y-2 text-sm mb-4">
                                        <div class="flex justify-between"><span class="text-gray-500">Модель:</span><span class="font-medium">{{ $sel['model_name'] }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">Зона:</span><span class="font-medium">{{ $sel['zone_name'] }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">Позиция:</span><span class="font-mono">{{ $sel['grid_x'] }}, {{ $sel['grid_y'] }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">Поворот:</span><span>{{ $sel['rotation'] }}°</span></div>
                                    </div>
                                    <div class="space-y-2">
                                        <button wire:click="rotateTable({{ $sel['id'] }})" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">Повернуть 90°</button>
                                        <button wire:click="removeTable({{ $sel['id'] }})" wire:confirm="Убрать стол {{ $sel['code'] }} с карты?" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">Убрать с карты</button>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-3">Перетаскивайте стол мышкой для перемещения</p>
                                </div>
                            @endif
                        @endif

                        {{-- Доступные столы --}}
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Доступные ({{ count($tablesAvailable) }})</h3>
                            @if(count($tablesAvailable) === 0)
                                <p class="text-sm text-gray-400 text-center py-4">Все столы размещены</p>
                            @else
                                <div class="space-y-2 max-h-72 overflow-y-auto">
                                    @foreach($tablesAvailable as $table)
                                        <button wire:click="selectTable({{ $table['id'] }})"
                                                class="w-full text-left p-3 border-2 rounded-lg transition
                                                       {{ $selectedTableId === $table['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}">
                                            <div class="font-bold text-sm">{{ $table['code'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $table['model_name'] }} · {{ $table['grid_width'] }}×{{ $table['grid_height'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- На карте --}}
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">На карте ({{ count($tablesOnGrid) }})</h3>
                            @if(count($tablesOnGrid) === 0)
                                <p class="text-sm text-gray-400 text-center py-4">Столы не размещены</p>
                            @else
                                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                    @foreach($tablesOnGrid as $t)
                                        <button wire:click="selectGridTable({{ $t['id'] }})"
                                                class="w-full text-left p-2 rounded-lg text-sm transition hover:bg-gray-50 dark:hover:bg-gray-800
                                                       {{ $selectedGridTableId === $t['id'] ? 'bg-amber-50 dark:bg-amber-900/20 ring-1 ring-amber-400' : '' }}">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full" style="background:{{ $t['zone_color'] }}"></div>
                                                <span class="font-medium">{{ $t['code'] }}</span>
                                                <span class="text-xs text-gray-400">({{ $t['grid_x'] }},{{ $t['grid_y'] }})</span>
                                            </div>
                                        </button>
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

<script>
(function(){
    var TE = {
        gw: {{ $gridWidth ?? 20 }},
        gh: {{ $gridHeight ?? 10 }},
        zones: {!! json_encode($zones ?? []) !!},
        tables: {!! json_encode($tablesOnGrid ?? []) !!},
        root: null,
        overlay: null,
        dragging: null,
        _pendingRender: false,
        hallImg: '',
        _evBound: false,

        boot: function() {
            this.root = document.getElementById('te-map-root');
            this.bindLivewire();
            if (this.root) { this.render(); if (!this._evBound) { this.bindEvents(); this._evBound = true; } }
        },

        findRoot: function() {
            this.root = document.getElementById('te-map-root');
            return !!this.root;
        },

        render: function() {
            if (!this.root) return;
            var pct = (this.gh / this.gw * 100);
            var cw = 100/this.gw, ch = 100/this.gh;
            var h = '';
            var hallImg = this.hallImg || '{{ $hallImage ? asset("storage/" . $hallImage) : "" }}';
            var bgStyle = hallImg ? 'background:url('+hallImg+') center/cover no-repeat' : 'background:#f8fafc';
            h += '<div style="position:relative;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;' + bgStyle + '">';
            if (hallImg) { h += '<div style="position:absolute;inset:0;background:rgba(0,0,0,.2)"></div>'; }
            h += '<div style="position:absolute;inset:0;pointer-events:none;opacity:0.08;background-image:linear-gradient(rgba(0,0,0,.2) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.2) 1px,transparent 1px);background-size:'+cw+'% '+ch+'%"></div>';
            h += '<div style="position:relative;width:100%;padding-bottom:'+pct+'%">';

            // Zones per-cell
            for (var i=0;i<this.zones.length;i++) {
                var z=this.zones[i],cs=z.coordinates||[];
                for (var j=0;j<cs.length;j++) h += '<div style="position:absolute;pointer-events:none;left:'+(cs[j].x/this.gw*100)+'%;top:'+(cs[j].y/this.gh*100)+'%;width:'+cw+'%;height:'+ch+'%;background:'+z.color+';opacity:0.2"></div>';
            }
            // Zone labels
            for (var i=0;i<this.zones.length;i++) {
                var z=this.zones[i],cs=z.coordinates||[];
                if(!cs.length)continue;
                var x1=Infinity,y1=Infinity,x2=0,y2=0;
                for(var j=0;j<cs.length;j++){x1=Math.min(x1,cs[j].x);y1=Math.min(y1,cs[j].y);x2=Math.max(x2,cs[j].x);y2=Math.max(y2,cs[j].y);}
                h += '<div style="position:absolute;pointer-events:none;display:flex;align-items:center;justify-content:center;left:'+(x1/this.gw*100)+'%;top:'+(y1/this.gh*100)+'%;width:'+((x2-x1+1)/this.gw*100)+'%;height:'+((y2-y1+1)/this.gh*100)+'%"><span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:'+z.color+';opacity:.5">'+z.name+'</span></div>';
            }

            // Tables
            for (var i=0;i<this.tables.length;i++) {
                var t=this.tables[i];
                h += '<div data-tid="'+t.id+'" style="position:absolute;left:'+(t.grid_x/this.gw*100)+'%;top:'+(t.grid_y/this.gh*100)+'%;width:'+(t.grid_width/this.gw*100)+'%;height:'+(t.grid_height/this.gh*100)+'%;z-index:10;cursor:grab;display:flex;align-items:center;justify-content:center">';
                // Anchor dot (top-left)
                h += '<div style="position:absolute;top:2px;left:2px;width:6px;height:6px;background:#fff;border-radius:50%;border:1.5px solid rgba(0,0,0,.3);z-index:5;pointer-events:none"></div>';
                // Table body
                h += '<div style="width:90%;height:82%;border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1px;background:rgba(16,185,129,.85);box-shadow:0 4px 12px rgba(16,185,129,.25);position:relative">';
                h += '<div style="position:absolute;inset:3px;border-radius:6px;opacity:.3;background:#a7f3d0"></div>';
                h += '<span style="position:relative;color:#fff;font-weight:700;font-size:11px;line-height:1;text-shadow:0 1px 2px rgba(0,0,0,.2)">'+t.code+'</span>';
                h += '<span style="position:relative;color:rgba(255,255,255,.7);font-size:8px;line-height:1">'+t.model_name+'</span>';
                h += '</div></div>';
            }

            // Ghost (hidden initially)
            h += '<div id="te-ghost" style="position:absolute;display:none;pointer-events:none;z-index:99;border:2px dashed rgba(59,130,246,.5);border-radius:8px;background:rgba(59,130,246,.1)"></div>';
            // Overlay
            h += '<div id="te-overlay" style="position:absolute;inset:0;z-index:5;touch-action:none"></div>';
            h += '</div></div>';

            // Toast container
            h += '<div id="te-toast" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none"></div>';

            this.root.innerHTML = h;
            this.overlay = document.getElementById('te-overlay');
        },

        bindEvents: function() {
            var self = this;

            this.root.addEventListener('pointerdown', function(e) {
                var tel = e.target.closest('[data-tid]');
                if (tel) {
                    e.preventDefault(); e.stopPropagation();
                    var id = parseInt(tel.getAttribute('data-tid'));
                    var t = null;
                    for(var i=0;i<self.tables.length;i++) if(self.tables[i].id===id){t=self.tables[i];break;}
                    if(!t) return;

                    var rect = tel.getBoundingClientRect();
                    self.dragging = {
                        id: id, el: tel, t: t,
                        offsetX: e.clientX - rect.left,
                        offsetY: e.clientY - rect.top,
                        startX: tel.style.left, startY: tel.style.top,
                        startClientX: e.clientX, startClientY: e.clientY,
                        moved: false // ещё не двигали
                    };
                    try{e.target.setPointerCapture(e.pointerId);}catch(err){}
                    return;
                }
                if (e.target.id==='te-overlay') {
                    var c = self.cellAt(e);
                    if(c){var w=document.querySelector('[wire\\:id]');if(w)Livewire.find(w.getAttribute('wire:id')).call('placeTable',c.x,c.y);}
                }
            });

            this.root.addEventListener('pointermove', function(e) {
                if(!self.dragging) return;
                e.preventDefault();

                // Порог: начинаем drag только после 5px сдвига
                if (!self.dragging.moved) {
                    var dx = Math.abs(e.clientX - self.dragging.startClientX);
                    var dy = Math.abs(e.clientY - self.dragging.startClientY);
                    if (dx < 5 && dy < 5) return;
                    // Начинаем реальный drag
                    self.dragging.moved = true;
                    self.dragging.el.style.zIndex='100';
                    self.dragging.el.style.opacity='0.6';
                    self.dragging.el.style.cursor='grabbing';
                }

                var ov=self.overlay; if(!ov) return;
                var r=ov.getBoundingClientRect();
                var nx = e.clientX - r.left - self.dragging.offsetX;
                var ny = e.clientY - r.top - self.dragging.offsetY;
                self.dragging.el.style.left = (nx/r.width*100)+'%';
                self.dragging.el.style.top = (ny/r.height*100)+'%';

                var anchorPx = e.clientX - r.left - self.dragging.offsetX;
                var anchorPy = e.clientY - r.top - self.dragging.offsetY;
                var cell = self.cellAtXY(anchorPx, anchorPy, r.width, r.height);
                var ghost = document.getElementById('te-ghost');
                if(cell && ghost) {
                    var t = self.dragging.t;
                    ghost.style.display='block';
                    ghost.style.left=(cell.x/self.gw*100)+'%';
                    ghost.style.top=(cell.y/self.gh*100)+'%';
                    ghost.style.width=(t.grid_width/self.gw*100)+'%';
                    ghost.style.height=(t.grid_height/self.gh*100)+'%';
                }
            });

            this.root.addEventListener('pointerup', function(e) {
                if(!self.dragging) return;
                e.preventDefault();
                var d=self.dragging; self.dragging=null;

                var ghost=document.getElementById('te-ghost');
                if(ghost) ghost.style.display='none';

                if (!d.moved) {
                    // Просто клик — выбрать стол, не перемещать
                    var w=document.querySelector('[wire\\:id]');
                    if(w) Livewire.find(w.getAttribute('wire:id')).call('selectGridTable',d.id);
                    return;
                }

                // Был реальный drag
                d.el.style.zIndex='10'; d.el.style.opacity='1'; d.el.style.cursor='grab';

                var ov=self.overlay; if(!ov){d.el.style.left=d.startX;d.el.style.top=d.startY;return;}
                var r=ov.getBoundingClientRect();
                var anchorX = e.clientX - r.left - d.offsetX;
                var anchorY = e.clientY - r.top - d.offsetY;
                var cell = self.cellAtXY(anchorX, anchorY, r.width, r.height);

                if(cell) {
                    var w=document.querySelector('[wire\\:id]');
                    if(w) Livewire.find(w.getAttribute('wire:id')).call('moveTable',d.id,cell.x,cell.y);
                } else {
                    d.el.style.left=d.startX; d.el.style.top=d.startY;
                }

                if(self._pendingRender){self._pendingRender=false;setTimeout(function(){if(self.findRoot())self.render();},100);}
            });

            this.root.addEventListener('contextmenu', function(e){e.preventDefault();});
        },

        cellAt: function(e) {
            if(!this.overlay) return null;
            var r=this.overlay.getBoundingClientRect();
            return this.cellAtXY(e.clientX-r.left, e.clientY-r.top, r.width, r.height);
        },

        cellAtXY: function(px, py, w, h) {
            if(px<0||py<0||px>w||py>h) return null;
            return {
                x: Math.max(0,Math.min(this.gw-1,Math.floor((px/w)*this.gw))),
                y: Math.max(0,Math.min(this.gh-1,Math.floor((py/h)*this.gh)))
            };
        },

        showToast: function(type, msg) {
            var container = document.getElementById('te-toast');
            if(!container) {
                container = document.createElement('div');
                container.id='te-toast';
                container.style.cssText='position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none';
                document.body.appendChild(container);
            }
            var colors = {error:'#fef2f2;border:1px solid #fecaca;color:#991b1b',success:'#f0fdf4;border:1px solid #bbf7d0;color:#166534',warning:'#fffbeb;border:1px solid #fde68a;color:#92400e'};
            var el = document.createElement('div');
            el.style.cssText='padding:10px 16px;border-radius:10px;font-size:13px;max-width:320px;background:'+( colors[type]||colors.error)+';box-shadow:0 4px 12px rgba(0,0,0,.1);pointer-events:auto;animation:te-toast-in .3s ease';
            el.textContent = msg;
            container.appendChild(el);
            setTimeout(function(){el.style.animation='te-toast-out .3s ease forwards';setTimeout(function(){el.remove();},300);},3000);
        },

        bindLivewire: function() {
            var self = this;

            Livewire.on('te:update', function(data) {
                var d=data[0];
                self.gw=d.gw; self.gh=d.gh; self.zones=d.zones; self.tables=d.tables;
                self.hallImg = d.hallImage || '';
                if(self.dragging){self._pendingRender=true;return;}
                setTimeout(function(){if(self.findRoot())self.render();},30);
            });

            Livewire.on('te:toast', function(data) {
                var d=data[0];
                self.showToast(d.type, d.msg);
            });
        }
    };

    // Toast animations
    var style = document.createElement('style');
    style.textContent = '@keyframes te-toast-in{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}@keyframes te-toast-out{to{opacity:0;transform:translateX(40px)}}';
    document.head.appendChild(style);

    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded',function(){TE.boot();});
    else TE.boot();
    document.addEventListener('livewire:navigated',function(){TE.boot();});
})();
</script>