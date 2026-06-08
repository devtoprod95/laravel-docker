<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/tabler.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="antialiased">
    <div class="wrapper">

        <div class="sticky-top">
            <header class="navbar navbar-expand-md sticky-top d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <a href="/" class="navbar-brand">MyApp</a>

                    <div class="navbar-nav flex-row order-md-last">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm">홍</span>
                                <div class="d-none d-xl-block ps-2">
                                    <div>홍길동</div>
                                    <div class="mt-1 small text-muted">관리자</div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="/profile" class="dropdown-item">프로필</a>
                                <a href="/logout" class="dropdown-item text-danger">로그아웃</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <header class="navbar-expand-md">
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="navbar navbar-light">
                        <div class="container-xl">
                            <div class="row flex-column flex-md-row flex-fill align-items-center">
                                <div class="col">
                                    <ul class="navbar-nav">
                                        <x-nav-item href="/" :active="request()->is('/')">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home fs-2"></i></span>
                                            <span class="nav-link-title">홈</span>
                                        </x-nav-item>

                                        <x-nav-item href="/users" :active="request()->is('users*')">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-users fs-2"></i></span>
                                            <span class="nav-link-title">유저</span>
                                        </x-nav-item>

                                        <li class="nav-item dropdown {{ request()->is('settings*') ? 'active' : '' }}">
                                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-settings fs-2"></i></span>
                                                <span class="nav-link-title">설정</span>
                                            </a>
                                            <div class="dropdown-menu">
                                                <a href="/settings/general" class="dropdown-item">
                                                    <i class="ti ti-adjustments me-2 fs-2 fs-2"></i>일반
                                                </a>
                                                <a href="/settings/security" class="dropdown-item">
                                                    <i class="ti ti-lock me-2 fs-2"></i>보안
                                                </a>

                                                <div class="dropend">
                                                    <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="ti ti-bell me-2 fs-2"></i>알림
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <a href="#" class="dropdown-item">이메일 알림</a>
                                                        <a href="#" class="dropdown-item">SMS 알림</a>
                                                        <a href="#" class="dropdown-item">푸시 알림</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </div>

        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <h2 class="page-title">@yield('title')</h2>
                </div>
            </div>
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/tabler.min.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ko.js"></script>
    <script src="{{ asset('js/common.js') }}" defer></script>
</body>
</html>
