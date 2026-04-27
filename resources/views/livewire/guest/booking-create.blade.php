<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
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

    <x-booking.stepper-progress :current="$step" />

    @if($step === 1)
        <x-booking.steps.step-place :places="$places" wireSelectPlace="selectPlace" />
    @endif

    @if($step === 2)
        <x-booking.steps.step-time 
            :placeData="$placeData" :resource_id="null" :date="$date"
            :selectedSlots="$selectedSlots" :availableSlots="$availableSlots"
            :totalAmount="$totalAmount" wireToggleSlot="toggleSlot"
            wireQuickSelect="quickSelect" wireClearSlots="clearSlots"
            wireProceedToEquipment="proceedToTables" wireGoBack="goBack"
            :multiTable="true"
        />
    @endif

    @if($step === 3)
        <x-booking.steps.step-table-multi
            :placeData="$placeData" :selectedResources="$selectedResources"
            :availableResourceIds="$availableResourceIds" :resourcePrices="$resourcePrices"
            :selectedSlots="$selectedSlots" :date="$date" :totalAmount="$totalAmount"
        />
    @endif

    @if($step === 4)
        <x-booking.steps.step-equipment 
            :availableEquipment="$availableEquipment" :equipment="$equipment"
            :totalAmount="$totalAmount" wireAddEquipment="addEquipment"
            wireUpdateEquipmentQty="updateEquipmentQty" wireRemoveEquipment="removeEquipment"
            wireSkipEquipment="skipEquipment" wireProceedToClientData="proceedToClientData"
            wireGoBack="goBack"
        />
    @endif

    @if($step === 5)
        @php $selectedResourcesData = $this->getSelectedResourcesData(); @endphp
        <x-booking.steps.step-client-data-multi
            :placeData="$placeData" :selectedResourcesData="$selectedResourcesData"
            :date="$date" :selectedSlots="$selectedSlots" :equipment="$equipment"
            :totalAmount="$totalAmount" :comment="$comment"
            wireCreatePendingBooking="createPendingBooking" wireGoBack="goBack"
        />
    @endif

    @if($step === 6 && $booking)
        <x-booking.steps.step-payment 
            :booking="$booking" :totalAmount="$totalAmount"
            wirePayBooking="payBooking" wireSkipPayment="skipPayment" wireGoBack="goBack"
        />
    @endif

    @if($step === 7 && $booking)
        <x-booking.steps.step-success-multi :booking="$booking" :totalAmount="$totalAmount" />
    @endif
</div>

<script>
window.PayGate = window.PayGate || (function(){ /* loaded from stepper */ return window.PayGate; })();
</script>