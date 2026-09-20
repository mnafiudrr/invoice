<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen lg:flex" x-data="{ drawerOpen: false, sidebarCollapsed: false }">
        {{-- Mobile drawer backdrop --}}
        <div x-show="drawerOpen" x-transition.opacity
             class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
             @click="drawerOpen = false"></div>

        {{-- Sidebar: mobile drawer / desktop full-height column --}}
        <aside
            :class="[
                drawerOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'lg:hidden' : '',
            ]"
            class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col bg-white shadow-lg transition-transform duration-150 ease-in-out lg:static lg:z-auto lg:h-screen lg:translate-x-0 lg:shadow-none"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-200 px-6">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold">{{ config('app.name') }}</a>
                <button type="button" class="lg:hidden text-gray-500 hover:text-gray-900" @click="drawerOpen = false" aria-label="Close menu">
                    &times;
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                @php
                    $route = request()->route()?->getName() ?? '';
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'M3.75 13.5 10.5 19.5l10.25-14.25'],
                        ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15'],
                        ['label' => 'Invoices', 'route' => 'admin.invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z'],
                    ];
                @endphp
                <ul class="space-y-1">
                    @foreach ($navItems as $item)
                        @php
                            if ($item['route'] === 'admin.dashboard') {
                                $active = $route === 'admin.dashboard';
                            } else {
                                $prefix = rtrim($item['route'], 'index') . '.';
                                $active = $route === $item['route'] || str_starts_with($route, $prefix);
                            }
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium',
                                   'bg-accent-50 text-accent-700' => $active,
                                   'text-gray-700 hover:bg-gray-100 hover:text-gray-900' => ! $active,
                               ])>
                                <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        {{-- Main column --}}
        <div class="flex min-w-0 flex-1 flex-col lg:h-screen">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="lg:hidden text-gray-500 hover:text-gray-900" @click="drawerOpen = true" aria-label="Open menu">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <button type="button" class="hidden lg:inline-flex text-gray-500 hover:text-gray-900" @click="sidebarCollapsed = !sidebarCollapsed"
                                :aria-expanded="!sidebarCollapsed" aria-label="Toggle sidebar">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                 :class="sidebarCollapsed ? 'rotate-180' : ''" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold lg:hidden">{{ config('app.name') }}</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    @include('partials.admin.flash')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>