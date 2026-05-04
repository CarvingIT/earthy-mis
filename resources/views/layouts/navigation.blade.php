<nav x-data="{ open: false }" class="sticky top-0 z-[1000] backdrop-blur-md" style="background: rgba(255, 255, 255, 0.8); border-bottom: 1px solid rgba(0, 0, 0, 0.08);">
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .nav-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-link {
            position: relative;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #1d1d1f;
            text-decoration: none;
            transition: color 0.2s ease;
            letter-spacing: -0.01em;
        }

        .nav-link:hover {
            color: #0071e3;
        }

        .nav-link.active {
            color: #0071e3;
            font-weight: 600;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0071e3;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-link.active::after {
            transform: scaleX(1);
        }

        .dropdown-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #1d1d1f;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
            letter-spacing: -0.01em;
            padding: 0.25rem 0;
            position: relative;
        }

        .dropdown-trigger:hover {
            color: #0071e3;
        }

        .dropdown-trigger.active {
            color: #0071e3;
            font-weight: 600;
        }

        .dropdown-trigger.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0071e3;
        }

        .dropdown-trigger svg {
            width: 0.75rem;
            height: 0.75rem;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .dropdown-trigger.open svg {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 1rem;
            min-width: 220px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
            z-index: 1000;
            padding: 0.5rem 0;
        }

        .dropdown-trigger.open ~ .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem 1rem;
            color: #1d1d1f;
            background: transparent;
            border: none;
            text-align: left;
            font-size: 0.9375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: rgba(0, 113, 227, 0.08);
            color: #0071e3;
        }

        .dropdown-menu a.active {
            background: rgba(0, 113, 227, 0.08);
            color: #0071e3;
            font-weight: 600;
        }

        .user-menu-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 0.875rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #1d1d1f;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
        }

        .user-menu-trigger:hover {
            background: rgba(0, 0, 0, 0.08);
            border-color: rgba(0, 0, 0, 0.12);
        }

        .user-menu-trigger svg {
            width: 1rem;
            height: 1rem;
        }

        .user-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 1rem;
            min-width: 200px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
            z-index: 1000;
        }

        .user-menu.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .user-menu-header {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .user-menu-header p {
            margin: 0;
            padding: 0;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .user-menu-header .name {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #1d1d1f;
            margin-top: 0.25rem;
        }

        .user-menu-items {
            padding: 0.5rem;
        }

        .user-menu a,
        .user-menu button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 0.75rem;
            color: #1d1d1f;
            background: transparent;
            border: none;
            text-align: left;
            font-size: 0.9375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
        }

        .user-menu a:hover,
        .user-menu button:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .user-menu a.logout:hover,
        .user-menu button.logout:hover {
            background: rgba(255, 59, 48, 0.08);
            color: #ff3b30;
        }

        .hamburger {
            display: none;
            width: 2rem;
            height: 2rem;
            padding: 0.375rem;
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 0.75rem;
            color: #1d1d1f;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .hamburger:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1rem;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-menu-section {
            margin-bottom: 1rem;
        }

        .mobile-menu-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .mobile-menu-section-title.active {
            color: #0071e3;
            font-weight: 700;
            border-bottom: 2px solid #0071e3;
        }

        .mobile-menu a,
        .mobile-menu button {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            color: #1d1d1f;
            background: transparent;
            border: none;
            text-align: left;
            font-size: 0.9375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
        }

        .mobile-menu a:hover,
        .mobile-menu button:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .mobile-menu a.active {
            background: rgba(0, 113, 227, 0.08);
            color: #0071e3;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .desktop-nav {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .nav-container {
                padding: 0 1rem;
            }
        }

        @media (min-width: 769px) {
            .mobile-nav {
                display: none !important;
            }
        }
    </style>

    <div class="nav-container">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 transition-opacity hover:opacity-75">
                    <x-application-logo class="block h-8 w-auto max-w-[100px]" />
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="desktop-nav flex items-center gap-6">
                <!-- Main Links -->
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('charts.index') }}" class="nav-link {{ request()->routeIs('charts.*') ? 'active' : '' }}">
                    Analytics
                </a>

                <!-- Master Data Dropdown -->
                <div class="relative group">
                    <button class="dropdown-trigger group-hover:text-blue-600 {{ request()->routeIs('users.*', 'societies.*', 'vehicles.*', 'customers.*', 'products.*', 'consumables.*', 'units.*') ? 'active' : '' }}" x-data="{ open: false }" @click="open = !open" :class="{ 'open': open }">
                        <span>Master Data
                            @if (request()->routeIs('users.*'))
                                - Users
                            @elseif (request()->routeIs('societies.*'))
                                - Societies
                            @elseif (request()->routeIs('vehicles.*'))
                                - Vehicles
                            @elseif (request()->routeIs('customers.*'))
                                - Customers
                            @elseif (request()->routeIs('products.*'))
                                - Products
                            @elseif (request()->routeIs('consumables.*'))
                                - Consumables
                            @elseif (request()->routeIs('units.*'))
                                - Units
                            @endif
                        </span>
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
                        @endif
                        <a href="{{ route('societies.index') }}" class="{{ request()->routeIs('societies.*') ? 'active' : '' }}">Societies</a>
                        <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Vehicles</a>
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
                        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a>
                        <a href="{{ route('consumables.index') }}" class="{{ request()->routeIs('consumables.*') ? 'active' : '' }}">Consumables</a>
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('units.index') }}" class="{{ request()->routeIs('units.*') ? 'active' : '' }}">Units</a>
                        @endif
                    </div>
                </div>

                <!-- Operations Dropdown -->
                <div class="relative group">
                    <button class="dropdown-trigger group-hover:text-blue-600 {{ request()->routeIs('logistics.*', 'weights.*', 'trips.*', 'windrow.*', 'turning.*', 'supplyitems.*', 'stock.*', 'sale.*') ? 'active' : '' }}" x-data="{ open: false }" @click="open = !open" :class="{ 'open': open }">
                        <span>Operations
                            @if (request()->routeIs('logistics.*'))
                                - Logistics
                            @elseif (request()->routeIs('weights.*'))
                                - Weights
                            @elseif (request()->routeIs('trips.*'))
                                - Trips
                            @elseif (request()->routeIs('windrow.*'))
                                - Windrow
                            @elseif (request()->routeIs('turning.*'))
                                - Turnings
                            @elseif (request()->routeIs('supplyitems.*'))
                                - Supply Items
                            @elseif (request()->routeIs('stock.*'))
                                - Stock
                            @elseif (request()->routeIs('sale.*'))
                                - Sales
                            @endif
                        </span>
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('logistics.index') }}" class="{{ request()->routeIs('logistics.*') ? 'active' : '' }}">Logistics</a>
                        <a href="{{ route('weights.index') }}" class="{{ request()->routeIs('weights.*') ? 'active' : '' }}">Weights</a>
                        <a href="{{ route('trips.index') }}" class="{{ request()->routeIs('trips.*') ? 'active' : '' }}">Trips</a>
                        <a href="{{ route('windrow.index') }}" class="{{ request()->routeIs('windrow.*') ? 'active' : '' }}">Windrow</a>
                        <a href="{{ route('turning.index') }}" class="{{ request()->routeIs('turning.*') ? 'active' : '' }}">Turnings</a>
                        <a href="{{ route('supplyitems.index') }}" class="{{ request()->routeIs('supplyitems.*') ? 'active' : '' }}">Supply Items</a>
                        <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}">Stock</a>
                        <a href="{{ route('sale.index') }}" class="{{ request()->routeIs('sale.*') ? 'active' : '' }}">Sales</a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden md:flex items-center gap-4">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" :class="{ 'bg-gray-100': open }" class="user-menu-trigger">
                        <div class="flex flex-col items-start text-left">
                            <span class="text-xs font-semibold text-gray-500">Account</span>
                            <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                        </div>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="user-menu" :class="{ 'open': open }">
                        <div class="user-menu-header">
                            <p>Logged in as</p>
                            <p class="name">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="user-menu-items">
                            <a href="{{ route('profile.edit') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="logout w-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <button class="hamburger md:hidden" @click="open = !open" :class="{ 'bg-gray-100': open }">
                <svg x-show="!open" class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-menu" :class="{ 'open': open }">
        <div class="mobile-menu-section">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('charts.index') }}" class="nav-link {{ request()->routeIs('charts.*') ? 'active' : '' }}">
                Analytics
            </a>
        </div>

        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title {{ request()->routeIs('users.*', 'societies.*', 'vehicles.*', 'customers.*', 'products.*', 'consumables.*', 'units.*') ? 'active' : '' }}">
                Master Data
                @if (request()->routeIs('users.*'))
                    <span> - Users</span>
                @elseif (request()->routeIs('societies.*'))
                    <span> - Societies</span>
                @elseif (request()->routeIs('vehicles.*'))
                    <span> - Vehicles</span>
                @elseif (request()->routeIs('customers.*'))
                    <span> - Customers</span>
                @elseif (request()->routeIs('products.*'))
                    <span> - Products</span>
                @elseif (request()->routeIs('consumables.*'))
                    <span> - Consumables</span>
                @elseif (request()->routeIs('units.*'))
                    <span> - Units</span>
                @endif
            </div>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
            @endif
            <a href="{{ route('societies.index') }}" class="{{ request()->routeIs('societies.*') ? 'active' : '' }}">Societies</a>
            <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Vehicles</a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a>
            <a href="{{ route('consumables.index') }}" class="{{ request()->routeIs('consumables.*') ? 'active' : '' }}">Consumables</a>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('units.index') }}" class="{{ request()->routeIs('units.*') ? 'active' : '' }}">Units</a>
            @endif
        </div>

        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title {{ request()->routeIs('logistics.*', 'weights.*', 'trips.*', 'windrow.*', 'turning.*', 'supplyitems.*', 'stock.*', 'sale.*') ? 'active' : '' }}">
                Operations
                @if (request()->routeIs('logistics.*'))
                    <span> - Logistics</span>
                @elseif (request()->routeIs('weights.*'))
                    <span> - Weights</span>
                @elseif (request()->routeIs('trips.*'))
                    <span> - Trips</span>
                @elseif (request()->routeIs('windrow.*'))
                    <span> - Windrow</span>
                @elseif (request()->routeIs('turning.*'))
                    <span> - Turnings</span>
                @elseif (request()->routeIs('supplyitems.*'))
                    <span> - Supply Items</span>
                @elseif (request()->routeIs('stock.*'))
                    <span> - Stock</span>
                @elseif (request()->routeIs('sale.*'))
                    <span> - Sales</span>
                @endif
            </div>
            <a href="{{ route('logistics.index') }}" class="{{ request()->routeIs('logistics.*') ? 'active' : '' }}">Logistics</a>
            <a href="{{ route('weights.index') }}" class="{{ request()->routeIs('weights.*') ? 'active' : '' }}">Weights</a>
            <a href="{{ route('trips.index') }}" class="{{ request()->routeIs('trips.*') ? 'active' : '' }}">Trips</a>
            <a href="{{ route('windrow.index') }}" class="{{ request()->routeIs('windrow.*') ? 'active' : '' }}">Windrow</a>
            <a href="{{ route('turning.index') }}" class="{{ request()->routeIs('turning.*') ? 'active' : '' }}">Turnings</a>
            <a href="{{ route('supplyitems.index') }}" class="{{ request()->routeIs('supplyitems.*') ? 'active' : '' }}">Supply Items</a>
            <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}">Stock</a>
            <a href="{{ route('sale.index') }}" class="{{ request()->routeIs('sale.*') ? 'active' : '' }}">Sales</a>
        </div>

        <div class="border-t border-gray-200 pt-4 mt-4">
            <a href="{{ route('profile.edit') }}" class="mobile-menu a">Profile Settings</a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="mobile-menu button text-red-600 hover:bg-red-100">Log Out</button>
            </form>
        </div>
    </div>
</nav>
