<x-app-layout>
    <x-slot name="header">
        <h1>Mi perfil</h1>
    </x-slot>

    <style>
        .profile-grid { display: flex; flex-direction: column; gap: 24px; max-width: 720px; }

        .profile-card {
            background: rgba(18,18,20,0.8);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 28px 32px;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .card-desc {
            font-size: 13px;
            color: #52525b;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .divider { height: 1px; background: rgba(255,255,255,0.06); margin-bottom: 24px; }

        .field { margin-bottom: 18px; }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #71717a;
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            max-width: 420px;
            padding: 10px 14px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px;
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .field input:focus {
            border-color: rgba(99,102,241,0.5);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }

        .field input::placeholder { color: #3f3f46; }

        .field-error { font-size: 12px; color: #f87171; margin-top: 5px; }

        .field-note {
            font-size: 12px;
            color: #52525b;
            margin-top: 5px;
        }

        .alert-success {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #86efac;
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.2);
            border-radius: 8px;
            padding: 7px 12px;
            margin-bottom: 18px;
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(79,70,229,0.3);
            transition: all 0.18s;
        }

        .btn-save:hover {
            background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,0.45);
        }

        .btn-save:active { transform: translateY(0); }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #f87171;
            transition: all 0.18s;
        }

        .btn-danger:hover {
            background: rgba(239,68,68,0.18);
            color: #fca5a5;
        }

        /* Modal eliminar cuenta */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: #111113;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 28px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.6);
        }

        .modal-title { font-size: 16px; font-weight: 700; color: #f4f4f5; margin-bottom: 8px; }
        .modal-desc  { font-size: 13.5px; color: #71717a; margin-bottom: 22px; line-height: 1.6; }

        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

        .btn-cancel {
            padding: 9px 18px;
            border-radius: 9px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            color: #a1a1aa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-cancel:hover { background: rgba(255,255,255,0.09); color: #e4e4e7; }
    </style>

    <div class="profile-grid">

        {{-- Información de perfil --}}
        <div class="profile-card">
            <h2 class="card-title">Información del perfil</h2>
            <p class="card-desc">Actualiza tu nombre y correo electrónico.</p>
            <div class="divider"></div>

            @if (session('status') === 'profile-updated')
                <div class="alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    Perfil actualizado correctamente.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('patch')

                <div class="field">
                    <label for="name">Nombre completo</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="field-note">⚠ Tu correo no está verificado.</p>
                    @endif
                </div>

                <button type="submit" class="btn-save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    Guardar cambios
                </button>
            </form>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="profile-card">
            <h2 class="card-title">Cambiar contraseña</h2>
            <p class="card-desc">Usa una contraseña larga y aleatoria para mayor seguridad.</p>
            <div class="divider"></div>

            @if (session('status') === 'password-updated')
                <div class="alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    Contraseña actualizada correctamente.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('put')

                <div class="field">
                    <label for="current_password">Contraseña actual</label>
                    <input id="current_password" type="password" name="current_password"
                           placeholder="••••••••" autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="new_password">Nueva contraseña</label>
                    <input id="new_password" type="password" name="password"
                           placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           placeholder="Repite tu nueva contraseña" autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                    Actualizar contraseña
                </button>
            </form>
        </div>

        {{-- Eliminar cuenta --}}
        <div class="profile-card">
            <h2 class="card-title">Eliminar cuenta</h2>
            <p class="card-desc">Una vez eliminada, todos los datos serán borrados permanentemente. Descarga cualquier información antes de continuar.</p>
            <div class="divider"></div>

            <button class="btn-danger" onclick="document.getElementById('delete-modal').classList.add('open')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
                Eliminar mi cuenta
            </button>
        </div>
    </div>

    {{-- Modal confirmar eliminación --}}
    <div class="modal-overlay" id="delete-modal">
        <div class="modal">
            <h2 class="modal-title">¿Eliminar tu cuenta?</h2>
            <p class="modal-desc">Esta acción es irreversible. Ingresa tu contraseña para confirmar que deseas eliminar tu cuenta permanentemente.</p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf @method('delete')

                <div class="field" style="margin-bottom: 20px;">
                    <label for="del_password">Contraseña</label>
                    <input id="del_password" type="password" name="password"
                           placeholder="Tu contraseña actual" style="max-width:100%;">
                    @error('password', 'userDeletion')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel"
                            onclick="document.getElementById('delete-modal').classList.remove('open')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-danger">Sí, eliminar cuenta</button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>