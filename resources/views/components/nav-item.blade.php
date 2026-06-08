@props(['href', 'active' => false])

<li class="nav-item {{ $active ? 'active' : '' }}">
    <a class="nav-link" href="{{ $href }}">
        {{ $slot }}
    </a>
</li>
