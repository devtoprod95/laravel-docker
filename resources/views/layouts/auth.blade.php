<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>MyApp | 로그인</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/tabler.min.css') }}">
</head>
<body class="d-flex flex-column bg-light"> <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <div class="d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                    <img src="https://www.svgrepo.com/show/510437/logo-ladspa.svg"
                         height="48" width="48" class="shadow-sm rounded bg-white p-1" style="object-fit: contain;">
                    <span class="fs-1 fw-bold text-dark">MyApp</span>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="{{ asset('js/tabler.min.js') }}?v={{ filemtime(public_path('js/tabler.min.js')) }}"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script src="https://npmcdn.com/flatpickr/dist/l10n/ko.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('js/common.js') }}?v={{ filemtime(public_path('js/common.js')) }}"></script>

            @yield('content')
        </div>
    </div>

</body>
</html>
