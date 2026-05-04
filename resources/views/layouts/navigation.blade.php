<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto max-w-[110px]" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('charts.index')" :active="request()->routeIs('charts.*')">
                        {{ __('Charts') }}
                    </x-nav-link>
		</div>
		<div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Master data
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (auth()->user()?->isAdmin())
                                <x-dropdown-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    {{ __('Users') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('societies.index')" :active="request()->routeIs('societies.*')">
                                {{ __('Societies') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')">
                                {{ __('Vehicles') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                                {{ __('Customers') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                {{ __('Products') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('consumables.index')" :active="request()->routeIs('consumables.*')">
                                {{ __('Consumables') }}
                            </x-dropdown-link>
                            @if (auth()->user()?->isAdmin())
                                <x-dropdown-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                                    {{ __('Units') }}
                                </x-dropdown-link>
                            @endif
                               <!-- Add more links -->
                        </x-slot>
                    </x-dropdown>
		    </div>
		   <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <!--button class="flex items-center text-sm font-medium text-gray-500"-->
                            <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Operations
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('logistics.index')" :active="request()->routeIs('logistics.*')">
                                {{ __('Logistics') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('weights.index')" :active="request()->routeIs('weights.*')">
                                {{ __('Weights') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('trips.index')" :active="request()->routeIs('trips.*')">
                                {{ __('Trips') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('windrow.index')" :active="request()->routeIs('windrow.*')">
                                {{ __('Windrow') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('turning.index')" :active="request()->routeIs('turning.*')">
                                {{ __('Turnings') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('supplyitems.index')" :active="request()->routeIs('supplyitems.*')">
                                {{ __('Supply Items') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('stock.index')" :active="request()->routeIs('stock.*')">
                                {{ __('Stock') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('sale.index')" :active="request()->routeIs('sale.*')">
                                {{ __('Sale') }}
                            </x-dropdown-link>
                            <!-- Add more links -->
                        </x-slot>
                    </x-dropdown>

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium text-green-700 bg-green-50 focus:outline-none focus:text-green-800 focus:bg-green-100 transition duration-150 ease-in-out">
                                Master data
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (auth()->user()?->isAdmin())
                                <x-dropdown-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    {{ __('Users') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('societies.index')" :active="request()->routeIs('societies.*')">
                                {{ __('Societies') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')">
                                {{ __('Vehicles') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                                {{ __('Customers') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                {{ __('Products') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('consumables.index')" :active="request()->routeIs('consumables.*')">
                                {{ __('Consumables') }}
                            </x-dropdown-link>
                            @if (auth()->user()?->isAdmin())
                                <x-dropdown-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                                    {{ __('Units') }}
                                </x-dropdown-link>
                            @endif
                               <!-- Add more links -->
                        </x-slot>
                    </x-dropdown>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium text-green-700 bg-green-50 focus:outline-none focus:text-green-800 focus:bg-green-100 transition duration-150 ease-in-out">
                                Operations
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('logistics.index')" :active="request()->routeIs('logistics.*')">
                                {{ __('Logistics') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('weights.index')" :active="request()->routeIs('weights.*')">
                                {{ __('Weights') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('trips.index')" :active="request()->routeIs('trips.*')">
                                {{ __('Trips') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('windrow.index')" :active="request()->routeIs('windrow.*')">
                                {{ __('Windrow') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('turning.index')" :active="request()->routeIs('turning.*')">
                                {{ __('Turnings') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('supplyitems.index')" :active="request()->routeIs('supplyitems.*')">
                                {{ __('Supply Items') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('stock.index')" :active="request()->routeIs('stock.*')">
                                {{ __('Stock') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('sale.index')" :active="request()->routeIs('sale.*')">
                                {{ __('Sale') }}
                            </x-dropdown-link>
                            <!-- Add more links -->
                        </x-slot>
                    </x-dropdown>
		    </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
