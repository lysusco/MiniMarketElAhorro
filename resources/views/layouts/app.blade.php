<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-bg-100 flex">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0">
                <!-- Topbar -->
                <header class="h-16 bg-bg-200 border-b border-bg-300 flex items-center px-4 sm:px-6 gap-4 shrink-0">
                    @isset($header)
                        <div class="text-base font-semibold text-text-100 truncate">
                            {{ $header }}
                        </div>
                    @endisset
                    <div class="ms-auto flex items-center gap-3 text-sm text-text-200">
                        <span class="hidden sm:inline">{{ Auth::user()->email }}</span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    @if (session('success'))
                        <div class="px-4 sm:px-6 lg:px-8 mt-4">
                            <div class="bg-green-500/15 border border-green-500/30 text-green-300 rounded-lg p-4 text-sm">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="px-4 sm:px-6 lg:px-8 mt-4">
                            <div class="bg-red-500/15 border border-red-500/30 text-red-300 rounded-lg p-4 text-sm">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <div class="p-4 sm:p-6 lg:p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>