@extends("layout")

@section("title")
    {{ __("vistas.cuentaUsuario.verificarcuenta.title") }}
@endsection

@section("content")
    <section class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <!-- TODO: Cambiar el logo este -->
                <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                {{ env("APP_NAME") }}
            </a>
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    @if($response["code"] == 0)
                        <svg class="mb-4 block mx-auto stroke-emerald-400 w-1/4 h-1/4" fill="none" stroke="currentColor" stroke-linecap="round"  stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        <p class="block mb-2 font-medium text-center text-gray-900 dark:text-white">
                            {{ __("vistas.cuentaUsuario.recuperarcuentaresult.ok1") }}
                        </p>
                        <p class="block mb-2 text-sm text-center text-gray-900 dark:text-white">
                            {{ __("vistas.cuentaUsuario.recuperarcuentaresult.ok2") }}
                        </p>
                    @else
                        <svg class="mb-4 block mx-auto stroke-red-600 w-1/4 h-1/4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="var(--geist-fill)"></circle><path d="M15 9L9 15" stroke="var(--geist-stroke)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path><path d="M9 9L15 15" stroke="var(--geist-stroke)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path></svg>
                        <p class="block mb-2 font-medium text-center text-gray-900 dark:text-white">
                            {{ __("vistas.cuentaUsuario.recuperarcuentaresult.ko1") }}
                        </p>
                        <p class="block mb-2 text-sm text-center text-gray-900 dark:text-white">
                            {{ __("vistas.cuentaUsuario.recuperarcuentaresult.ko2") }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

