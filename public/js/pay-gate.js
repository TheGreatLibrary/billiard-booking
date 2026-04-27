(function(){
    var PG = {
        _wireId: null,
        amt: 0, af: '0',
        card: {number:'',expiry:'',cvv:'',holder:''},

        init: function(a,f) {
            this.amt=a; this.af=f;
            // Кешируем wire:id
            var overlays = document.getElementById('pg-card-overlay') || document.getElementById('pg-sbp-overlay');
            if (overlays) {
                var w = overlays.closest('[wire\\:id]');
                if (w) this._wireId = w.getAttribute('wire:id');
            }
        },
        getWire: function() {
            if (this._wireId) try{return Livewire.find(this._wireId);}catch(e){}
            var all=document.querySelectorAll('[wire\\:id]');
            if(all.length) return Livewire.find(all[all.length-1].getAttribute('wire:id'));
            return null;
        },

        el: function(id) { return document.getElementById(id); },
        showEl: function(id) { var e=this.el(id); if(e) e.style.display='flex'; },
        hideEl: function(id) { var e=this.el(id); if(e) e.style.display='none'; },
        showBlock: function(id) { var e=this.el(id); if(e) e.style.display='block'; },
        hideBlock: function(id) { var e=this.el(id); if(e) e.style.display='none'; },

        openCard: function() {
            this.showEl('pg-card-overlay');
            this.showBlock('pg-card-form');
            this.hideBlock('pg-card-processing');
            this.hideBlock('pg-card-error');
            this.hideBlock('pg-error');
            var ids=['pg-num','pg-exp','pg-cvv','pg-holder'];
            for(var i=0;i<ids.length;i++){var inp=this.el(ids[i]);if(inp)inp.value='';}
            this.card={number:'',expiry:'',cvv:'',holder:''};
        },

        openSbp: function() {
            this.showEl('pg-sbp-overlay');
            this.showBlock('pg-sbp-form');
            this.hideBlock('pg-sbp-processing');
            var s=this; setTimeout(function(){s.drawQR();},200);
        },

        close: function() {
            this.hideEl('pg-card-overlay');
            this.hideEl('pg-sbp-overlay');
        },

        cardRetry: function() {
            this.hideBlock('pg-card-error');
            this.showBlock('pg-card-form');
        },

        onNum: function(el) {
            var v=el.value.replace(/[^0-9]/g,'').substring(0,16);
            this.card.number=v.replace(/(.{4})/g,'$1 ').trim();
            el.value=this.card.number;
            var b=this.el('pg-brand');
            if(b) b.textContent=this.getBrand();
            this.hideBlock('pg-error');
        },
        onExp: function(el) {
            var v=el.value.replace(/[^0-9]/g,'').substring(0,4);
            if(v.length>=2) v=v.substring(0,2)+'/'+v.substring(2);
            this.card.expiry=v; el.value=v;
        },
        getBrand: function() {
            var n=this.card.number.replace(/\s/g,'');
            if(n.charAt(0)==='4')return'Visa';
            if(n.charAt(0)==='5'||n.charAt(0)==='2')return'MC';
            return'';
        },
        luhn: function(n){var s=0,a=false;for(var i=n.length-1;i>=0;i--){var d=parseInt(n.charAt(i),10);if(a){d*=2;if(d>9)d-=9;}s+=d;a=!a;}return s%10===0;},

        readFields: function() {
            var num=this.el('pg-num'), exp=this.el('pg-exp'), cvv=this.el('pg-cvv'), hld=this.el('pg-holder');
            if(num)this.card.number=num.value;
            if(exp)this.card.expiry=exp.value;
            if(cvv)this.card.cvv=cvv.value;
            if(hld)this.card.holder=hld.value;
        },
        showError: function(msg) {
            var e=this.el('pg-error');
            if(e){e.style.display='block';e.textContent=msg;}
        },
        validate: function() {
            var n=this.card.number.replace(/\s/g,'');
            if(n.length<13){this.showError('Введите номер карты');return false;}
            if(!this.luhn(n)){this.showError('Неверный номер карты');return false;}
            if(!this.card.expiry||this.card.expiry.length<5){this.showError('Введите срок');return false;}
            if(!this.card.cvv||this.card.cvv.length<3){this.showError('Введите CVV');return false;}
            if(!this.card.holder||this.card.holder.trim().length<3){this.showError('Введите имя');return false;}
            return true;
        },

        payCard: function() {
            this.readFields();
            if(!this.validate()) return;
            var n=this.card.number.replace(/\s/g,''), self=this;
            this.hideBlock('pg-card-form');
            this.showBlock('pg-card-processing');
            var bar=this.el('pg-card-bar');
            var st=[20,45,65,80,95,100],i=0;
            var iv=setInterval(function(){if(i<st.length){if(bar)bar.style.width=st[i++]+'%';}else clearInterval(iv);},400);
            setTimeout(function(){
                clearInterval(iv);
                if(n==='4000000000000002'){
                    self.hideBlock('pg-card-processing');
                    self.showBlock('pg-card-error');
                    var msg=self.el('pg-card-error-msg');
                    if(msg) msg.textContent='Недостаточно средств. Попробуйте другую карту.';
                    return;
                }
                self.close();
                var w=self.getWire(); if(w) w.call('payBooking','card');
            },2500);
        },

        paySbp: function() {
            var self=this;
            this.hideBlock('pg-sbp-form');
            this.showBlock('pg-sbp-processing');
            var bar=this.el('pg-sbp-bar');
            var st=[15,35,55,75,90,100],i=0;
            var iv=setInterval(function(){if(i<st.length){if(bar)bar.style.width=st[i++]+'%';}else clearInterval(iv);},500);
            setTimeout(function(){
                clearInterval(iv);
                self.close();
                var w=self.getWire(); if(w) w.call('payBooking','online');
            },3000);
        },

        drawQR: function() {
            var c=this.el('pg-qr'); if(!c)return;
            var ctx=c.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,180,180);
            var m=25,cs=180/m,seed=this.amt+42;ctx.fillStyle='#000';
            for(var y=0;y<m;y++)for(var x=0;x<m;x++){
                var f=(x<7&&y<7)||(x>=m-7&&y<7)||(x<7&&y>=m-7),fill=false;
                if(f){var bx=x<7?0:m-7,by=y<7?0:m-7,lx=x-bx,ly=y-by;fill=(lx===0||lx===6||ly===0||ly===6)||(lx>=2&&lx<=4&&ly>=2&&ly<=4);}
                else{seed=((seed*1103515245+12345)&0x7fffffff);fill=(seed%3)===0;}
                if(fill)ctx.fillRect(x*cs,y*cs,cs,cs);
            }
        }
    };

    window.PayGate = PG;

    // Делегированный обработчик кликов — работает с Livewire
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-pg]');
        if (!btn) return;

        var action = btn.getAttribute('data-pg');
        var amt = btn.getAttribute('data-pg-amt');
        var af = btn.getAttribute('data-pg-af');

        switch(action) {
            case 'card':
                PG.init(parseInt(amt)||0, af||'0');
                PG.openCard();
                break;
            case 'sbp':
                PG.init(parseInt(amt)||0, af||'0');
                PG.openSbp();
                break;
            case 'close':
                PG.close();
                break;
            case 'pay-card':
                PG.payCard();
                break;
            case 'pay-sbp':
                PG.paySbp();
                break;
            case 'card-retry':
                PG.cardRetry();
                break;
        }
    });

    // Клик на overlay фон — закрыть
    document.addEventListener('click', function(e) {
        if (e.target.id === 'pg-card-overlay' || e.target.id === 'pg-sbp-overlay') {
            PG.close();
        }
    });

    // Input handlers через делегирование
    document.addEventListener('input', function(e) {
        if (e.target.id === 'pg-num') PG.onNum(e.target);
        if (e.target.id === 'pg-exp') PG.onExp(e.target);
        if (e.target.id === 'pg-cvv') PG.card.cvv = e.target.value;
        if (e.target.id === 'pg-holder') PG.card.holder = e.target.value.toUpperCase();
    });
})();