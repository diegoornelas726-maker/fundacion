<style>
    .form-card {
        background: rgba(18,18,20,0.8);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
        padding: 28px 32px;
        max-width: 760px;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-grid .full { grid-column: 1 / -1; }

    .section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #3f3f46;
        margin: 20px 0 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        grid-column: 1 / -1;
    }

    .field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #71717a;
        margin-bottom: 6px;
    }

    .field input,
    .field select,
    .field textarea {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 10px;
        color: #f4f4f5;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        outline: none;
        transition: border-color 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .field select option { background: #18181a; }
    .field input::placeholder,
    .field textarea::placeholder { color: #3f3f46; }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.18), 0 4px 12px rgba(99, 102, 241, 0.08);
        background: rgba(255, 255, 255, 0.06);
    }

    .field textarea { resize: vertical; min-height: 90px; }

    .field-error { font-size: 12px; color: #f87171; margin-top: 5px; }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 22px;
        border: none;
        border-radius: 10px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        color: #fff;
        box-shadow: 0 4px 14px rgba(79,70,229,0.3);
        transition: all 0.18s;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #6366f1, #60a5fa);
        transform: translateY(-1px);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 22px;
        border-radius: 10px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        color: #71717a;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.18s;
    }

    .btn-cancel:hover { background: rgba(255,255,255,0.08); color: #e4e4e7; }

    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-grid .full { grid-column: 1; }
    }
</style>

<div class="form-card {{ $errors->any() ? 'animate-shake-card' : '' }}">
    <form method="POST" action="{{ $route }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method($method)

        <div class="form-grid">

            <p class="section-label">Datos personales</p>

            <div class="field">
                <label>Nombre(s) *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $beneficiario->nombre) }}" placeholder="Juan" required>
                @error('nombre') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Apellido paterno *</label>
                <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $beneficiario->apellido_paterno) }}" placeholder="Pérez" required>
                @error('apellido_paterno') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Apellido materno</label>
                <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $beneficiario->apellido_materno) }}" placeholder="García">
                @error('apellido_materno') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $beneficiario->fecha_nacimiento?->format('Y-m-d')) }}">
                @error('fecha_nacimiento') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Sexo</label>
                <select name="sexo">
                    <option value="">— Selecciona —</option>
                    @foreach(['Masculino','Femenino','Otro'] as $s)
                        <option value="{{ $s }}" {{ old('sexo', $beneficiario->sexo) === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @error('sexo') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>CURP</label>
                <input type="text" name="curp" value="{{ old('curp', $beneficiario->curp) }}" placeholder="XXXX000000XXXXXX00" maxlength="18" style="text-transform:uppercase;">
                @error('curp') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <p class="section-label">Contacto y ubicación</p>

            <div class="field">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $beneficiario->telefono) }}" placeholder="477 000 0000">
                @error('telefono') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Colonia</label>
                <input type="text" name="colonia" value="{{ old('colonia', $beneficiario->colonia) }}" placeholder="Los Ramírez">
                @error('colonia') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field full">
                <label>Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $beneficiario->direccion) }}" placeholder="Calle, número, referencia">
                @error('direccion') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <p class="section-label">Estado del registro</p>

            <div class="field">
                <label>Estado *</label>
                <select name="estado">
                    <option value="Activo"   {{ old('estado', $beneficiario->estado) === 'Activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ old('estado', $beneficiario->estado) === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field full">
                <label>Observaciones</label>
                <textarea name="observaciones" placeholder="Notas adicionales sobre el beneficiario…">{{ old('observaciones', $beneficiario->observaciones) }}</textarea>
                @error('observaciones') <p class="field-error">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save" :disabled="submitting">
                <!-- Spinner -->
                <svg x-show="submitting" class="animate-spin" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="15" height="15">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <!-- Icono de guardar -->
                <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                <span x-text="submitting ? 'Guardando...' : 'Guardar'">Guardar</span>
            </button>
            <a href="{{ route('beneficiarios.index') }}" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formCard = document.querySelector('.form-card');
        const form = formCard ? formCard.querySelector('form') : null;
        if (form) {
            form.addEventListener('invalid', (e) => {
                const field = e.target;
                field.classList.add('animate-shake');
                field.addEventListener('animationend', () => {
                    field.classList.remove('animate-shake');
                }, { once: true });
            }, true);
        }
    });
</script>