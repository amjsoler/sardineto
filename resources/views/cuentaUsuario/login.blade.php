@extends("layout")

@section("title")
    {{ __("vistas.cuentaUsuario.login.title") }}
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
                <h2 class="mb-1 text-xl font-bold leading-tight text-center tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    {{ __("vistas.cuentaUsuario.login.title") }}
                </h2>
                <form class="mt-4 space-y-8 lg:mt-5 md:space-y-5" action="{{ route("web-post-login") }}" method="POST">
                    {{ csrf_field() }}
                    <label>
                            <span class="block my-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("vistas.cuentaUsuario.login.email") }}
                            </span>
                        <input type="email" name="email"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600
                                 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                                 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                    </label>
                    <label class="block mt-5">
                            <span class="block my-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("vistas.cuentaUsuario.login.password") }}
                            </span>
                        <input type="password" name="password"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg
                                   focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700
                                   dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                                   dark:focus:border-blue-500">
                    </label>
                    <button type="submit"
                            class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        {{ __("vistas.cuentaUsuario.login.botonlogin") }}
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
