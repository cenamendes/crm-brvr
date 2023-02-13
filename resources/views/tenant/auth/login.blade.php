<x-guest-layout title="{ __('Pelase login') }" :action="$action">
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <div class="auth-form">
                        <div class="text-center mb-3">
                            <a href="{!! url('/') !!}"><img
                                    src="http://moto.brvr/assets/resources/images/logo-text.png" alt=""></a>
                        </div>
                        <h4 class="text-center mb-4 text-white">Acesso Proensal</h4>
                        <form method="POST" action="{{ route('tenant.verify') }}">
                            @csrf
                            <div class="form-group">
                                <x-label for="username" class="mb-1 text-white" :value="__('Username')" />
                                <x-input id="username" class="block mt-1 w-full form-control" type="text" name="username"
                                    :value="old('username')" required autofocus />
                            </div>
                            <div class="form-group">
                                <x-label for="password" class="mb-1 text-white" :value="__('Senha')" />
                                <x-input id="password" class="block mt-1 w-full form-control" type="password"
                                    name="password" required autocomplete="current-password" />
                            </div>
                            <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                <div class="form-group d-none">
                                    <div class="custom-control custom-checkbox ml-1 text-white">
                                        <label for="remember_me" class="inline-flex items-center">
                                            <input id="remember_me" type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                name="remember">
                                            <span class="ml-2 text-sm text-gray-600">{{ __('Lembrar-') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <a class="text-white" href="{!! url('/forgot-password') !!}">{{ __('Perdeu a senha?') }}</a>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit"
                                    class="btn bg-white text-primary btn-block">{{ __('Entrar') }}</button>
                            </div>
                        </form>
                        {{-- <div class="new-account mt-3">
                            <p class="text-white">{{ __('Don\'t have an account?') }} <a class="text-white"
                                    href="{!! url('/sign-up') !!}">{{ __('Sign up') }}</a></p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
