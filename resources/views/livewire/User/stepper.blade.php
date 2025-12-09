<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Flash сообщения -->
    @if (session()->has('error'))
        <x-shared.flash-message 
            type="error" 
            :message="session('error')" 
            title="Ошибка"
        />
    @endif

    @if (session()->has('success'))
        <x-shared.flash-message 
            type="success" 
            :message="session('success')" 
            title="Успех"
        />
    @endif

    @if (session()->has('info'))
        <x-shared.flash-message 
            type="info" 
            :message="session('info')" 
        />
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

    <!-- Шаг 2: Выбор стола -->
    @if($step === 2)
        <x-booking.steps.step-table 
            :placeData="$placeData"
            :resource_id="$resource_id"
            wireSelectResource="selectResource"
            wireProceedToTimeSelection="proceedToTimeSelection"
            wireGoBack="goBack"
        />
    @endif

    <!-- Шаг 3: Выбор времени -->
    @if($step === 3)
        <x-booking.steps.step-time 
            :placeData="$placeData"
            :resource_id="$resource_id"
            :date="$date"
            :selectedSlots="$selectedSlots"
            :availableSlots="$availableSlots"
            :totalAmount="$totalAmount"
            wireToggleSlot="toggleSlot"
            wireQuickSelect="quickSelect"
            wireClearSlots="clearSlots"
            wireProceedToEquipment="proceedToEquipment"
            wireGoBack="goBack"
        />
    @endif

    <!-- Шаг 4: Оборудование -->
    @if($step === 4)
        <!-- Аналогично - создаем компонент для шага 4 -->
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
        <x-booking.steps.step-client-data 
            :placeData="$placeData"
            :resource_id="$resource_id"
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
        <x-booking.steps.step-success 
            :booking="$booking"
            :totalAmount="$totalAmount"
        />
    @endif
</div>

@push('styles')
<style>
    /* Кастомный скролл для тайм-слотов */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Анимация пульсации для текущего шага */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
        }
    }
    
    .animate-pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    /* Плавные переходы для всех элементов */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    /* Градиентные границы */
    .gradient-border {
        position: relative;
        background-clip: padding-box;
        border: 2px solid transparent;
    }
    
    .gradient-border::before {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        bottom: -2px;
        left: -2px;
        z-index: -1;
        border-radius: inherit;
        background: linear-gradient(45deg, #f59e0b, #ea580c);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gradient-border:hover::before {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    // Инициализация Alpine.js для компонентов
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingStepper', () => ({
            init() {
                // Инициализация темной темы
                this.initTheme();
                
                // Сохранение состояния в localStorage
                this.restoreState();
            },
            
            initTheme() {
                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            },
            
            restoreState() {
                // Восстановление состояния из localStorage
                const savedState = localStorage.getItem('bookingState');
                if (savedState) {
                    try {
                        const state = JSON.parse(savedState);
                        // Можно добавить логику восстановления состояния
                    } catch (e) {
                        console.error('Error restoring state:', e);
                    }
                }
            },
            
            saveState(state) {
                // Сохранение состояния в localStorage
                localStorage.setItem('bookingState', JSON.stringify(state));
            }
        }));
    });
</script>
@endpush