<nav x-data="{ open: false }" class="bg-bg-200 border-b border-bg-300">
    <!-- Menú de navegación principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-text-100" />
                    </a>
                </div>

                <!-- Enlaces de navegación -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->isCliente())
                        <x-nav-link :href="route('cliente.panel')" :active="request()->routeIs('cliente.panel')">
                            {{ __('Panel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.catalogo')" :active="request()->routeIs('cliente.catalogo')">
                            {{ __('Catálogo') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.ventas.index')" :active="request()->routeIs('cliente.ventas.*')">
                            {{ __('Mis ventas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.abonos.index')" :active="request()->routeIs('cliente.abonos.*')">
                            {{ __('Mis abonos') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Panel') }}
                        </x-nav-link>
                    @endif
                </div>

                @unless (Auth::user()->isCliente())
                <!-- Módulo de inventario -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('categorias.*') ? 'border-primary-300 text-text-100 focus:border-primary-200' : 'border-transparent text-text-200 hover:text-text-100 hover:border-bg-300 focus:outline-none focus:text-text-100 focus:border-bg-300' }}">
                                {{ __('Inventario') }}
                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs font-semibold text-text-200 uppercase">{{ __('Catálogo') }}</div>
                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('categorias.index')">
                                    {{ __('Categorías') }}
                                </x-dropdown-link>
                            @endif
                            <x-coming-soon>{{ __('Productos') }}</x-coming-soon>
                            <x-coming-soon>{{ __('Proveedores') }}</x-coming-soon>
                            <div class="px-4 py-2 text-xs font-semibold text-text-200 uppercase">{{ __('Movimientos') }}</div>
                            <x-coming-soon>{{ __('Entradas de mercancía') }}</x-coming-soon>
                            <x-coming-soon>{{ __('Ajustes de inventario') }}</x-coming-soon>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Módulo de ventas -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 border-transparent text-text-200 hover:text-text-100 hover:border-bg-300 focus:outline-none focus:text-text-100 focus:border-bg-300 transition duration-150 ease-in-out">
                                {{ __('Ventas') }}
                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-coming-soon>{{ __('Nueva venta') }}</x-coming-soon>
                            <x-coming-soon>{{ __('Historial de ventas') }}</x-coming-soon>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Módulo de clientes y crédito -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 border-transparent text-text-200 hover:text-text-100 hover:border-bg-300 focus:outline-none focus:text-text-100 focus:border-bg-300 transition duration-150 ease-in-out">
                                {{ __('Clientes') }}
                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-coming-soon>{{ __('Clientes y crédito') }}</x-coming-soon>
                            <x-coming-soon>{{ __('Registrar abono') }}</x-coming-soon>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endunless

                <!-- Reportes (solo administrador) -->
                @if (Auth::user()->isAdmin())
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 border-transparent text-text-200 hover:text-text-100 hover:border-bg-300 focus:outline-none focus:text-text-100 focus:border-bg-300 transition duration-150 ease-in-out">
                                    {{ __('Reportes') }}
                                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-coming-soon>{{ __('Productos con stock bajo') }}</x-coming-soon>
                                <x-coming-soon>{{ __('Ventas por fecha') }}</x-coming-soon>
                                <x-coming-soon>{{ __('Cartera de clientes') }}</x-coming-soon>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Seguridad (solo administrador) -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 border-transparent text-text-200 hover:text-text-100 hover:border-bg-300 focus:outline-none focus:text-text-100 focus:border-bg-300 transition duration-150 ease-in-out">
                                    {{ __('Seguridad') }}
                                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-coming-soon>{{ __('Usuarios y roles') }}</x-coming-soon>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
            </div>

            <!-- Menú de usuario -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-text-200 bg-bg-200 hover:text-text-100 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-text-200">
                            {{ Auth::user()->isAdmin() ? 'Administrador' : (Auth::user()->isCliente() ? 'Cliente' : 'Empleado') }}
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi perfil') }}
                        </x-dropdown-link>

                        <!-- Cerrar sesión -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Botón de menú móvil -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-text-200 hover:text-text-100 hover:bg-bg-300 focus:outline-none focus:bg-bg-300 focus:text-text-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú de navegación responsive -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->isCliente())
                <x-responsive-nav-link :href="route('cliente.panel')" :active="request()->routeIs('cliente.panel')">
                    {{ __('Panel') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.catalogo')" :active="request()->routeIs('cliente.catalogo')">
                    {{ __('Catálogo') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.ventas.index')" :active="request()->routeIs('cliente.ventas.*')">
                    {{ __('Mis ventas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.abonos.index')" :active="request()->routeIs('cliente.abonos.*')">
                    {{ __('Mis abonos') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Panel') }}
                </x-responsive-nav-link>

                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                        {{ __('Categorías') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

            <div class="px-4 py-2 text-xs font-semibold text-text-200 uppercase">{{ __('Inventario') }}</div>
            <span class="block px-4 py-2 text-sm text-text-200">{{ __('Productos · próximamente') }}</span>
            <span class="block px-4 py-2 text-sm text-text-200">{{ __('Proveedores · próximamente') }}</span>
            <span class="block px-4 py-2 text-sm text-text-200">{{ __('Entradas y ajustes · próximamente') }}</span>

            <div class="px-4 py-2 text-xs font-semibold text-text-200 uppercase">{{ __('Ventas y clientes') }}</div>
            <span class="block px-4 py-2 text-sm text-text-200">{{ __('Nueva venta · próximamente') }}</span>
            <span class="block px-4 py-2 text-sm text-text-200">{{ __('Clientes y abonos · próximamente') }}</span>
        </div>

        <!-- Opciones del usuario -->
        <div class="pt-4 pb-1 border-t border-bg-300">
            <div class="px-4">
                <div class="font-medium text-base text-text-100">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-text-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Mi perfil') }}
                </x-responsive-nav-link>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>