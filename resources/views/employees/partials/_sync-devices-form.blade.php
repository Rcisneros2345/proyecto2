{{--
    Partial: employees/partials/_sync-devices-form.blade.php
     Formulario de selección de dispositivos para sincronización.
     Reutilizado en edit.blade.php y en _sync-preview-drawer.blade.php.
     Variables: $employee (Employee model)
               $syncDevices (Collection of devices to sync)
               $formId (string, ID del form — default: 'sync-devices-form')
               $showSelectAll (bool, mostrar botón "Seleccionar todos" — default: true)
               $showSubmit (bool, mostrar botón de submit — default: true)
               $submitLabel (string, texto del botón — default: 'Sincronizar seleccionados')
               $inlineClass (string, clases CSS extra para el form)
--}}
@php
    $formId = $formId ?? 'sync-devices-form';
    $showSelectAll = $showSelectAll ?? true;
    $showSubmit = $showSubmit ?? true;
    $submitLabel = $submitLabel ?? 'Sincronizar seleccionados';
    $inlineClass = $inlineClass ?? '';
@endphp
<form id="{{ $formId }}"
      action="{{ route('employees.sync-devices', $employee) }}"
      method="POST"
      data-diff-url="{{ route('employees.enrollment-diff', $employee) }}"
      class="{{ $inlineClass }}">
    @csrf
    <fieldset>
        <legend class="visually-hidden">Selecciona checadores destino</legend>
        <div class="employee-device-select-list">
            @foreach($syncDevices as $syncDevice)
                <label class="employee-device-select">
                    <input type="checkbox" name="device_ids[]" value="{{ $syncDevice->id }}"
                           @checked($employee->devices->contains('id', $syncDevice->id))>
                    <span>
                        <strong>{{ $syncDevice->name }}</strong>
                        <small>{{ $syncDevice->ip }} · {{ $employee->devices->contains('id', $syncDevice->id) ? 'Actualizar acceso' : 'Agregar acceso' }}</small>
                    </span>
                </label>
            @endforeach
        </div>
    </fieldset>
    @if($showSubmit)
        <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-send me-1"></i> {{ $submitLabel }}</button>
        <div class="form-text mt-2">Verás qué cambiará en cada checador antes de confirmar. Envía nombre, PIN, tarjeta, rol y todas las huellas. Cada checador genera una tarea en la cola.</div>
    @endif
</form>
