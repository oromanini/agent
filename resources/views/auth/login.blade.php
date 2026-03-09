<x-guest-layout>
    <div class="login-flex">
        <div class="login-side w50">

        </div>
        <div class="w50">
            <x-auth-card>
                <x-slot name="logo">
                    <div class="logo">
                        <a href="/">
                            <img src="/img/logo/alluz-horizontal.png" alt="Alluz Energia" style="width: 200px; height: auto;">
                        </a>
                    </div>
                </x-slot>

                <div style="text-align:center; margin-bottom: 1rem;">
                    <h2 class="typing-title"><span id="login-typing-text"></span><span class="typing-caret">|</span></h2>
                    <p style="color: #9ca5ba; margin-top: .3rem;">SGP - Sistema gerador de proposta</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')"/>

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors"/>

                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-label for="email" :value="__('Email')"/>

                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                                 required autofocus/>
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-label for="password" :value="__('Senha')"/>

                        <x-input id="password" class="block mt-1 w-full"
                                 type="password"
                                 name="password"
                                 required autocomplete="current-password"/>
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   name="remember">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Lembre-me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900"
                               href="{{ route('password.request') }}">
                                {{ __('Esqueceu sua senha?') }}
                            </a>
                        @endif

                        <x-button id="login-submit" class="ml-3 login-submit-button">
                            <span class="login-submit-label">{{ __('Entrar') }}</span>
                            <span class="sun-loader" aria-hidden="true"></span>
                        </x-button>
                    </div>
                </form>
            </x-auth-card>

        </div>
    </div>

    <style>
        @media (max-width: 866px) {

            .login-side {
                display: none !important;
            }

            .w50 {
                width: 100%;
            }

            .w-full {
                width: 90%;
            }

            .logo {
                margin-bottom: 100px;
            }
        }
    </style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const words = ['Persista', 'Insista', 'Conquiste', 'Melhore', 'Evolua', 'Cresça', 'Vença', 'Destaque-se'];
        const target = document.getElementById('login-typing-text');

        if (!target) return;

        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;

        const type = () => {
            const currentWord = words[wordIndex];

            if (isDeleting) {
                charIndex = Math.max(charIndex - 1, 0);
            } else {
                charIndex = Math.min(charIndex + 1, currentWord.length);
            }

            target.textContent = currentWord.slice(0, charIndex);

            let timeout = isDeleting ? 45 : 85;

            if (!isDeleting && charIndex === currentWord.length) {
                timeout = 1100;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                wordIndex = (wordIndex + 1) % words.length;
                timeout = 240;
            }

            setTimeout(type, timeout);
        };

        type();

        const form = document.getElementById('login-form');
        const submitButton = document.getElementById('login-submit');

        if (form && submitButton) {
            form.addEventListener('submit', function () {
                submitButton.classList.add('is-loading-sun');
                submitButton.setAttribute('disabled', 'disabled');
                const label = submitButton.querySelector('.login-submit-label');
                if (label) {
                    label.textContent = 'Entrando...';
                }
            });
        }
    });
</script>
</x-guest-layout>
