@props(['item'])

@if(isset($item['children']))
    {{-- 3뎁스 이상인 경우: Dropend (또는 Dropdown) --}}
    <div class="dropend">
        <a href="#" class="dropdown-item dropdown-toggle {{ request()->is($item['pattern']) ? 'active' : '' }}"
           data-bs-toggle="dropdown"
           onclick="event.stopPropagation();"
        >
            {{ $item['name'] }}
        </a>
        <div class="dropdown-menu">
            @foreach($item['children'] as $child)
                <x-menu-item :item="$child" />
            @endforeach
        </div>
    </div>
@else
    {{-- 2뎁스 이하인 경우: 일반 링크 --}}
    <a href="{{ route($item['route']) }}" class="dropdown-item {{ request()->is($item['pattern']) ? 'active' : '' }}">
        {{ $item['name'] }}
    </a>
@endif
