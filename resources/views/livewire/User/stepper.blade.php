<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Flash сообщения -->
    @if (session()->has('error'))
        <x-shared.flash-message type="error" :message="session('error')" title="Ошибка" />
    @endif
    @if (session()->has('success'))
        <x-shared.flash-message type="success" :message="session('success')" title="Успех" />
    @endif
    @if (session()->has('info'))
        <x-shared.flash-message type="info" :message="session('info')" />
    @endif
    @if (session()->has('warning'))
        <x-shared.flash-message type="warning" :message="session('warning')" />
    @endif

    <!-- Прогресс шагов -->
    <x-booking.stepper-progress :current="$step" />

    <!-- Шаг 1: Выбор места -->
    @if($step === 1)
        <x-booking.steps.step-place 
            :places="$places"
            wireSelectPlace="selectPlace"
        />
    @endif

    <!-- Шаг 2: Дата + Время (РАНЬШЕ ЧЕМ СТОЛ) -->
    @if($step === 2)
        <x-booking.steps.step-time 
            :placeData="$placeData"
            :resource_id="null"
            :date="$date"
            :selectedSlots="$selectedSlots"
            :availableSlots="$availableSlots"
            :totalAmount="$totalAmount"
            wireToggleSlot="toggleSlot"
            wireQuickSelect="quickSelect"
            wireClearSlots="clearSlots"
            wireProceedToEquipment="proceedToTables"
            wireGoBack="goBack"
            :multiTable="true"
        />
    @endif

    <!-- Шаг 3: Выбор столов (МУЛЬТИ) -->
    @if($step === 3)
        <x-booking.steps.step-table-multi
            :placeData="$placeData"
            :selectedResources="$selectedResources"
            :availableResourceIds="$availableResourceIds"
            :resourcePrices="$resourcePrices"
            :selectedSlots="$selectedSlots"
            :date="$date"
            :totalAmount="$totalAmount"
        />
    @endif

    <!-- Шаг 4: Оборудование -->
    @if($step === 4)
        <x-booking.steps.step-equipment 
            :availableEquipment="$availableEquipment"
            :equipment="$equipment"
            :totalAmount="$totalAmount"
            wireAddEquipment="addEquipment"
            wireUpdateEquipmentQty="updateEquipmentQty"
            wireRemoveEquipment="removeEquipment"
            wireSkipEquipment="skipEquipment"
            wireProceedToClientData="proceedToClientData"
            wireGoBack="goBack"
        />
    @endif

    <!-- Шаг 5: Данные клиента -->
    @if($step === 5)
        @php
            $selectedResourcesData = $this->getSelectedResourcesData();
        @endphp
        <x-booking.steps.step-client-data-multi
            :placeData="$placeData"
            :selectedResourcesData="$selectedResourcesData"
            :date="$date"
            :selectedSlots="$selectedSlots"
            :equipment="$equipment"
            :totalAmount="$totalAmount"
            :comment="$comment"
            wireCreatePendingBooking="createPendingBooking"
            wireGoBack="goBack"
        />
    @endif

    <!-- Шаг 6: Оплата -->
    @if($step === 6 && $booking)
        <x-booking.steps.step-payment 
            :booking="$booking"
            :totalAmount="$totalAmount"
            wirePayBooking="payBooking"
            wireSkipPayment="skipPayment"
            wireGoBack="goBack"
        />
    @endif

    <!-- Шаг 7: Успех -->
    @if($step === 7 && $booking)
        <x-booking.steps.step-success-multi
            :booking="$booking"
            :totalAmount="$totalAmount"
        />
    @endif
</div>

{{-- PayGate — определяется всегда, используется на шаге 6 --}}
<script>
window.PayGate = window.PayGate || (function(){
    return {
        modal: null, stage: 'form', processing: false, progress: 0,
        errorMessage: '', cardError: '',
        card: {number:'',expiry:'',cvv:'',holder:''},
        amt: 0, af: '0',

        init: function(a, f) { this.amt = a; this.af = f; },

        openCard: function() {
            this.modal='card'; this.stage='form';
            this.card={number:'',expiry:'',cvv:'',holder:''}; this.cardError='';
            this.render();
        },
        openSbp: function() {
            this.modal='sbp'; this.stage='form'; this.render();
            var s=this; setTimeout(function(){s.drawQR();},200);
        },
        close: function() {
            if(this.stage==='processing')return;
            this.modal=null; this.getRoot().innerHTML='';
        },
        getRoot: function() { return document.getElementById('pay-modal-root'); },
        getWire: function() {
            var el=document.querySelector('[wire\\:id]');
            return el?Livewire.find(el.getAttribute('wire:id')):null;
        },

        render: function() {
            var r=this.getRoot(); if(!r)return;
            if(!this.modal){r.innerHTML='';return;}
            var af=this.af, h='<div class="pg-overlay" onclick="if(event.target===this)PayGate.close()">';

            if(this.modal==='card'){
                h+='<div class="pg-modal pg-card-modal">';
                h+='<div class="pg-header pg-header-blue"><div style="display:flex;align-items:center;gap:12px"><div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center">🔒</div><div><p style="color:#fff;font-weight:600;font-size:14px;margin:0">Безопасная оплата</p><p style="color:#93c5fd;font-size:12px;margin:0">Billiard Booking</p></div></div><button class="pg-close" onclick="PayGate.close()">✕</button></div>';
                h+='<div class="pg-sum"><p style="color:#6b7280;font-size:13px;margin:0">К оплате</p><p style="font-size:28px;font-weight:700;color:#111;margin:4px 0 0">'+af+' ₽</p></div>';
                if(this.stage==='form'){
                    h+='<div class="pg-form">';
                    h+='<div class="pg-field"><label class="pg-label">Номер карты</label><div style="position:relative"><input id="pg-num" type="text" class="pg-input'+(this.cardError?' pg-input-err':'')+'" maxlength="19" placeholder="0000 0000 0000 0000" value="'+this.card.number+'" oninput="PayGate.onNum(this)"><span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6b7280">'+this.getBrand()+'</span></div>';
                    if(this.cardError) h+='<p class="pg-err">'+this.cardError+'</p>';
                    h+='</div>';
                    h+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="pg-field"><div><label class="pg-label">Срок</label><input type="text" class="pg-input pg-input-sm" maxlength="5" placeholder="MM/YY" value="'+this.card.expiry+'" oninput="PayGate.onExp(this)"></div><div><label class="pg-label">CVV</label><input type="password" class="pg-input pg-input-sm" maxlength="3" placeholder="•••" value="'+this.card.cvv+'" oninput="PayGate.card.cvv=this.value"></div></div>';
                    h+='<div class="pg-field"><label class="pg-label">Имя держателя</label><input type="text" class="pg-input" style="text-transform:uppercase;letter-spacing:1px" placeholder="IVAN IVANOV" value="'+this.card.holder+'" oninput="PayGate.card.holder=this.value.toUpperCase()"></div>';
                    h+='<button class="pg-btn pg-btn-blue" onclick="PayGate.payCard()">Оплатить '+af+' ₽</button>';
                    h+='<p style="text-align:center;color:#9ca3af;font-size:10px;margin-top:12px">🔒 Данные защищены</p>';
                    h+='<p style="text-align:center;color:#d1d5db;font-size:10px;margin-top:4px">Тест: 4242 4242 4242 4242 — успех · 4000 0000 0000 0002 — отказ</p>';
                    h+='</div>';
                } else if(this.stage==='processing'){
                    h+='<div style="padding:48px 24px;text-align:center"><div class="pg-spinner"></div><p style="font-weight:600;font-size:16px;color:#111;margin:0 0 8px">Обработка платежа...</p><p style="color:#6b7280;font-size:13px">Не закрывайте окно</p><div class="pg-progress"><div class="pg-progress-bar pg-progress-blue" style="width:'+this.progress+'%"></div></div></div>';
                } else if(this.stage==='error'){
                    h+='<div style="padding:32px 24px;text-align:center"><div style="width:48px;height:48px;margin:0 auto 16px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px">❌</div><p style="font-weight:600;font-size:16px;color:#111;margin:0 0 8px">Платёж отклонён</p><p style="color:#6b7280;font-size:13px;margin:0 0 20px">'+this.errorMessage+'</p><button onclick="PayGate.stage=\'form\';PayGate.render()" style="padding:10px 24px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;font-size:13px">Попробовать снова</button></div>';
                }
                h+='</div>';
            } else if(this.modal==='sbp'){
                h+='<div class="pg-modal pg-sbp-modal">';
                h+='<div class="pg-header pg-header-green"><div style="display:flex;align-items:center;gap:12px"><div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center">📱</div><div><p style="color:#fff;font-weight:600;font-size:14px;margin:0">Оплата по СБП</p><p style="color:#6ee7b7;font-size:12px;margin:0">Система быстрых платежей</p></div></div><button class="pg-close" onclick="PayGate.close()">✕</button></div>';
                if(this.stage==='form'){
                    h+='<div style="padding:24px;text-align:center"><p style="color:#6b7280;font-size:13px;margin:0 0 4px">Сумма к оплате</p><p style="font-size:28px;font-weight:700;color:#111;margin:0 0 20px">'+af+' ₽</p><div style="background:#fff;padding:16px;border-radius:12px;display:inline-block;border:1px solid #e5e7eb;margin-bottom:16px"><canvas id="pg-qr" width="180" height="180" style="display:block"></canvas></div><p style="color:#374151;font-size:14px;margin:0 0 4px">Отсканируйте QR-код</p><p style="color:#9ca3af;font-size:12px;margin:0 0 20px">в мобильном приложении вашего банка</p><button class="pg-btn pg-btn-green" onclick="PayGate.paySbp()">Я оплатил</button></div>';
                } else if(this.stage==='processing'){
                    h+='<div style="padding:48px 24px;text-align:center"><div class="pg-spinner pg-spinner-green"></div><p style="font-weight:600;font-size:16px;color:#111;margin:0 0 8px">Проверяем оплату...</p><div class="pg-progress"><div class="pg-progress-bar pg-progress-green" style="width:'+this.progress+'%"></div></div></div>';
                }
                h+='</div>';
            }
            h+='</div>';
            r.innerHTML=h;
        },

        getBrand: function(){var n=this.card.number.replace(/\s/g,'');if(n.charAt(0)==='4')return'Visa';if(n.charAt(0)==='5'||n.charAt(0)==='2')return'MC';return'';},
        onNum: function(el){var v=el.value.replace(/[^0-9]/g,'').substring(0,16);this.card.number=v.replace(/(.{4})/g,'$1 ').trim();el.value=this.card.number;this.cardError='';},
        onExp: function(el){var v=el.value.replace(/[^0-9]/g,'').substring(0,4);if(v.length>=2)v=v.substring(0,2)+'/'+v.substring(2);this.card.expiry=v;el.value=v;},
        luhn: function(n){var s=0,a=false;for(var i=n.length-1;i>=0;i--){var d=parseInt(n.charAt(i),10);if(a){d*=2;if(d>9)d-=9;}s+=d;a=!a;}return s%10===0;},
        validate: function(){var n=this.card.number.replace(/\s/g,'');if(n.length<13){this.cardError='Введите номер карты';return false;}if(!this.luhn(n)){this.cardError='Неверный номер карты';return false;}if(!this.card.expiry||this.card.expiry.length<5){this.cardError='Введите срок';return false;}if(!this.card.cvv||this.card.cvv.length<3){this.cardError='Введите CVV';return false;}if(!this.card.holder||this.card.holder.trim().length<3){this.cardError='Введите имя';return false;}return true;},

        payCard: function(){
            if(!this.validate()){this.render();return;}
            var n=this.card.number.replace(/\s/g,''),self=this;
            this.stage='processing';this.progress=0;this.render();
            var st=[20,45,65,80,95,100],i=0;
            var iv=setInterval(function(){if(i<st.length){self.progress=st[i++];self.updProg();}else clearInterval(iv);},400);
            setTimeout(function(){clearInterval(iv);if(n==='4000000000000002'){self.stage='error';self.errorMessage='Недостаточно средств. Попробуйте другую карту.';self.render();return;}self.getRoot().innerHTML='';var w=self.getWire();if(w)w.call('payBooking','card');},2500);
        },
        paySbp: function(){
            var self=this;this.stage='processing';this.progress=0;this.render();
            var st=[15,35,55,75,90,100],i=0;
            var iv=setInterval(function(){if(i<st.length){self.progress=st[i++];self.updProg();}else clearInterval(iv);},500);
            setTimeout(function(){clearInterval(iv);self.getRoot().innerHTML='';var w=self.getWire();if(w)w.call('payBooking','online');},3000);
        },
        updProg: function(){var b=this.getRoot().querySelector('.pg-progress-bar');if(b)b.style.width=this.progress+'%';},
        drawQR: function(){var c=document.getElementById('pg-qr');if(!c)return;var ctx=c.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,180,180);var m=25,cs=180/m,seed=this.amt+42;ctx.fillStyle='#000';for(var y=0;y<m;y++)for(var x=0;x<m;x++){var f=(x<7&&y<7)||(x>=m-7&&y<7)||(x<7&&y>=m-7),fill=false;if(f){var bx=x<7?0:m-7,by=y<7?0:m-7,lx=x-bx,ly=y-by;fill=(lx===0||lx===6||ly===0||ly===6)||(lx>=2&&lx<=4&&ly>=2&&ly<=4);}else{seed=((seed*1103515245+12345)&0x7fffffff);fill=(seed%3)===0;}if(fill)ctx.fillRect(x*cs,y*cs,cs,cs);}
        }
    };
})();
</script>