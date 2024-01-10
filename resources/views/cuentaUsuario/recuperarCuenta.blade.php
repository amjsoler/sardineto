@extends("layout")

@section("title")
    {{ __("vistas.cuentaUsuario.recuperarcuenta.title") }}
@endsection

@section("content")
    <section class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
                     alt="logo">
                {{ env("app_name") }}
            </a>
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                @if($response["code"] == 0)
                    <h2 class="mb-1 text-xl font-bold leading-tight text-center tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        {{ __("vistas.cuentaUsuario.recuperarcuenta.title") }}
                    </h2>
                    @error("password")
                    <p class="mt-2 mb-4 text-pink-600 dark:text-pink-600">
                        {{ $errors->first("password") }}
                    </p>
                    @enderror
                    <form class="mt-4 space-y-8 lg:mt-5 md:space-y-5" action="{{ route("recuperarcuentapost") }}"
                          method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{ $response["data"] }}" name="token" id="token">
                        <label>
                            <span class="block my-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("vistas.cuentaUsuario.recuperarcuenta.nuevacontrasena") }}
                            </span>
                            <input type="password" name="password" placeholder="*******"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600
                                 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                                 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                        </label>
                        <label class="block mt-5">
                            <span class="block my-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("vistas.cuentaUsuario.recuperarcuenta.confirmarcontrasena") }}
                            </span>
                            <input type="password" name="password_confirmation" placeholder="*******"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg
                                   focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700
                                   dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                                   dark:focus:border-blue-500">
                        </label>
                        <button type="submit"
                                class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            {{ __("vistas.cuentaUsuario.recuperarcuenta.guardarnuevacontrasena") }}
                        </button>
                    </form>
                @else
                    <svg class="mb-4 block mx-auto stroke-red-600 w-1/4 h-1/4" fill="none" stroke="currentColor"
                         stroke-linecap="round" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="var(--geist-fill)"></circle>
                        <path d="M15 9L9 15" stroke="var(--geist-stroke)" strokeWidth="1.5" strokeLinecap="round"
                              strokeLinejoin="round"></path>
                        <path d="M9 9L15 15" stroke="var(--geist-stroke)" strokeWidth="1.5" strokeLinecap="round"
                              strokeLinejoin="round"></path>
                    </svg>
                    <p class="block mb-2 font-medium text-center text-gray-900 dark:text-white">
                        {{ __("vistas.cuentaUsuario.verificarcuenta.ko") }}
                    </p>
                    <p></p>
                @endif
            </div>
        </div>
    </section>
@endsection
