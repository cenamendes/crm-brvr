<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('custom-scripts')

        {{-- <script>
        window.addEventListener('swalModalQuestion',function(e){
            if(e.detail.confirm) {
                swal.fire({
                    title: e.detail.title,
                    html: e.detail.message,
                    type: e.detail.status,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: e.detail.confirmButtonText,
                    cancelButtonText: e.detail.cancellButtonText})
                .then((result) => {
                    if(result.value) {
                        console.log(e.function);
                        Livewire.emit(e.function);
                    } else {
                        console.log("cancel");
                    }
                });
            } else {
                swal(e.detail.title, e.detail.message, e.detail.status);
            }
        });
        </script> --}}
    </body>
</html>
