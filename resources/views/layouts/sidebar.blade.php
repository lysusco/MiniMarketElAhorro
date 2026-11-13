@php
    $user = Auth::user();
    $isCliente = $user?->isCliente() ?? false;
@endphp

<aside x-data="{ collapsed: false }"
       :class="collapsed ? 'w-16' : 'w-60'"
       class="shrink-0 bg-bg-200 border-r border-bg-300 flex flex-col transition-all duration-200 ease-in-out overflow-hidden">
    <!-- Marca -->
    <div class="h-16 flex items-center gap-3 px-4 border-b border-bg-300 shrink-0">
        <a href="{{ $isCliente ? route('cliente.panel') : route('dashboard') }}" class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="shrink-0 w-8 h-8 rounded-lg object-cover" />
            <span x-show="!collapsed" class="font-semibold text-text-100 truncate">{{ config('app.name', 'Minimercado') }}</span>
        </a>
        <button @click="collapsed = !collapsed" class="ms-auto shrink-0 text-text-200 hover:text-text-100 p-1 rounded" aria-label="Colapsar menú">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-4">
        @if ($isCliente)
            <div>
                <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">General</p>
                <x-responsive-nav-link :href="route('cliente.panel')" :active="request()->routeIs('cliente.panel')">
                    <span class="truncate" x-show="!collapsed">Panel</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.catalogo')" :active="request()->routeIs('cliente.catalogo')">
                    <span class="truncate" x-show="!collapsed">Catálogo</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.ventas.index')" :active="request()->routeIs('cliente.ventas.*')">
                    <span class="truncate" x-show="!collapsed">Mis ventas</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.abonos.index')" :active="request()->routeIs('cliente.abonos.*')">
                    <span class="truncate" x-show="!collapsed">Mis abonos</span>
                </x-responsive-nav-link>
            </div>
        @else
            <div>
                <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">General</p>
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <span class="truncate" x-show="!collapsed">Panel</span>
                </x-responsive-nav-link>
            </div>

            @unless ($user?->isCliente())
                <div>
                    <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">Inventario</p>
                    <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                        <span class="truncate" x-show="!collapsed">Categorías</span>
                    </x-responsive-nav-link>
                    <x-coming-soon x-show="!collapsed">Productos</x-coming-soon>
                    <x-coming-soon x-show="!collapsed">Proveedores</x-coming-soon>
                    <x-coming-soon x-show="!collapsed">Entradas y ajustes</x-coming-soon>
                </div>

                <div>
                    <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">Ventas</p>
                    <x-coming-soon x-show="!collapsed">Nueva venta</x-coming-soon>
                    <x-coming-soon x-show="!collapsed">Historial</x-coming-soon>
                </div>

                <div>
                    <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">Clientes</p>
                    <x-coming-soon x-show="!collapsed">Clientes y crédito</x-coming-soon>
                    <x-coming-soon x-show="!collapsed">Registrar abono</x-coming-soon>
                </div>
            @endunless

            @if ($user?->isAdmin())
                <div>
                    <p x-show="!collapsed" class="px-2 mb-1 text-[11px] uppercase tracking-wider text-text-200">Sistema</p>
                    <x-coming-soon x-show="!collapsed">Reportes</x-coming-soon>
                    <x-coming-soon x-show="!collapsed">Usuarios y roles</x-coming-soon>
                </div>
            @endif
        @endif
    </nav>

    <!-- Pie -->
    <div class="border-t border-bg-300 px-3 py-3 shrink-0">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-sm text-text-200 hover:text-text-100 px-2 py-1.5 rounded-lg hover:bg-bg-300">
            <span class="shrink-0 w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
            <span x-show="!collapsed" class="truncate">{{ $user->name ?? '' }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 text-sm text-text-200 hover:text-text-100 px-2 py-1.5 rounded-lg hover:bg-bg-300">
                <span class="shrink-0">⎋</span>
                <span x-show="!collapsed">Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>