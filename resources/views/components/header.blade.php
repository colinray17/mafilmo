<header class="app-header">
    <a href="{{ route('dashboard') }}" class="logo">Ma<span>Filmo</span></a>

    {{-- Nav desktop --}}
    <nav class="nav">
        <a href="{{ route('search') }}"
           class="{{ request()->routeIs('search') ? 'active' : '' }}">Rechercher</a>
        <a href="{{ route('lists') }}"
           class="{{ request()->routeIs('lists') ? 'active' : '' }}">Mes listes</a>
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Tableau de bord</a>
    </nav>

    {{-- Avatar + dropdown --}}
    <div class="dropdown" id="userDropdown">
        <div class="d-flex align-items-center gap-2"
             onclick="toggleDropdown()" style="cursor:pointer;">
            <div class="user-avatar">{{ Auth::user()->initials() }}</div>
            <span style="color:white; font-size:14px; font-weight:500;">
                {{ Auth::user()->name }}
            </span>
        </div>
        <div class="dropdown-menu-custom" id="dropdownMenu">
            <a href="{{ route('profile') }}">👤 Mon profil</a>
            <hr style="margin:0; border-color:#F3F4F6;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">🚪 Se déconnecter</button>
            </form>
        </div>
    </div>

    {{-- Burger mobile --}}
    <button class="burger" id="burgerBtn" onclick="toggleMobileMenu()">
        <span></span><span></span><span></span>
    </button>
</header>

{{-- Menu mobile --}}
<div class="mobile-menu" id="mobileMenu">
    <div class="menu-user">
        <div class="menu-avatar">{{ Auth::user()->initials() }}</div>
        {{ Auth::user()->name }}
    </div>
    <div class="menu-divider"></div>
    <a href="{{ route('search') }}">🔍 Rechercher</a>
    <a href="{{ route('lists') }}">🎬 Mes listes</a>
    <a href="{{ route('dashboard') }}">🏠 Tableau de bord</a>
    <a href="{{ route('profile') }}">👤 Mon profil</a>
    <div class="menu-divider"></div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">🚪 Se déconnecter</button>
    </form>
</div>
