# Few-Shot Example: Blade Component

## Input
Create a reusable Blade component for a currency input field that handles Bolívares and USD with BCV rate display.

## Output

### `resources/views/components/input-currency.blade.php`
```blade
@props([
    'name' => 'amount',
    'label' => 'Amount',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'showBcvRate' => false,
    'bcvRate' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        <div class="input-group-prepend">
            <select class="form-control form-control-sm" id="{{ $name }}_currency" name="{{ $name }}_currency">
                <option value="VES">Bs.</option>
                <option value="USD">$ USD</option>
            </select>
        </div>

        <input
            type="number"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            value="{{ old($name, $value) }}"
            step="0.01"
            min="0"
            @if($required) required @endif
            @if($disabled) disabled @endif
            placeholder="0.00"
        >

        @if($showBcvRate && $bcvRate)
            <div class="input-group-append">
                <span class="input-group-text input-group-text-sm" title="Tasa BCV: {{ $bcvRate }}">
                    BCV: {{ number_format($bcvRate, 2) }}
                </span>
            </div>
        @endif
    </div>

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

@push('scripts')
<script>
document.getElementById('{{ $name }}_currency').addEventListener('change', function() {
    const input = document.getElementById('{{ $name }}');
    const bcvRate = {{ $bcvRate ?? 'null' }};

    if (this.value === 'USD' && bcvRate) {
        input.placeholder = '0.00 USD';
    } else {
        input.placeholder = '0.00 Bs.';
    }
});
</script>
@endpush
```

### Usage Example
```blade
@include('components.input-currency', [
    'name' => 'amount',
    'label' => 'Monto',
    'required' => true,
    'showBcvRate' => true,
    'bcvRate' => $bcvRate->rate,
])
```
