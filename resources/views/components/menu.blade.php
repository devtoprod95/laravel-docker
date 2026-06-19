<ul class="navbar-nav">
    @foreach(App\Enums\Menu::cases() as $menu)
        @php $item = $menu->info(); @endphp

        <li class="nav-item {{ isset($item['children']) ? 'dropdown' : '' }} {{ request()->is($item['pattern'] ?? 'none') ? 'active' : '' }}">
            <a class="nav-link {{ isset($item['children']) ? 'dropdown-toggle' : '' }}"
               href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
               data-bs-toggle="{{ isset($item['children']) ? 'dropdown' : '' }}">

                {{-- 아이콘 추가 --}}
                @isset($item['icon'])
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <i class="ti ti-{{ $item['icon'] }} fs-2"></i>
                    </span>
                @endisset

                <span class="nav-link-title">{{ $item['name'] }}</span>
            </a>

            @isset($item['children'])
                <div class="dropdown-menu">
                    @foreach($item['children'] as $child)
                        <x-menu-item :item="$child" />
                    @endforeach
                </div>
            @endisset
        </li>
    @endforeach
</ul>
