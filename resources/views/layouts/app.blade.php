<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>

    <meta name="description" content="Laravel 13 기반의 관리자 전용 페이지 보일러플레이트 프로젝트입니다. 새 프로젝트를 시작할 때 바로 이어서 작업할 수 있도록 관리자 페이지의 기본 레이아웃(헤더, 사이드 네비게이션, 인증 등)을 미리 구축해 둬 프로젝트의 작업속도를 보장하기 위해 개발되졌습니다.">
    <meta name="keywords" content="대시보드, 웹개발, 어드민">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="apple-touch-icon" href="https://github.githubassets.com/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="https://github.githubassets.com/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="https://github.githubassets.com/apple-touch-icon-114x114.png">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Dashboard')">
    <meta property="og:description" content="Laravel 13 기반의 관리자 전용 페이지 보일러플레이트 프로젝트입니다. 새 프로젝트를 시작할 때 바로 이어서 작업할 수 있도록 관리자 페이지의 기본 레이아웃(헤더, 사이드 네비게이션, 인증 등)을 미리 구축해 둬 프로젝트의 작업속도를 보장하기 위해 개발되졌습니다.">
    <meta property="og:image" content="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Dashboard')">
    <meta name="twitter:description" content="Laravel 13 기반의 관리자 전용 페이지 보일러플레이트 프로젝트입니다. 새 프로젝트를 시작할 때 바로 이어서 작업할 수 있도록 관리자 페이지의 기본 레이아웃(헤더, 사이드 네비게이션, 인증 등)을 미리 구축해 둬 프로젝트의 작업속도를 보장하기 위해 개발되졌습니다.">
    <meta name="twitter:image" content="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png">

    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/tabler.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.3.0/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="antialiased">

    <div class="wrapper">

        <x-header />

        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <h2 class="page-title">@yield('title')</h2>
                </div>
            </div>
            <div class="page-body mt-0">
                <div class="container-xl">
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="{{ asset('js/tabler.min.js') }}?v={{ filemtime(public_path('js/tabler.min.js')) }}"></script>
                    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                    <script src="https://npmcdn.com/flatpickr/dist/l10n/ko.js"></script>
                    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.3.0/js/tabulator.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script src="{{ asset('js/common.js') }}?v={{ filemtime(public_path('js/common.js')) }}"></script>

                    <script>
                        $(document).ready(function() {
                            var loadingSwal;

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                beforeSend: function() {
                                    loadingSwal = Swal.fire({
                                        title: '처리 중...',
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        background: 'transparent',
                                        color: '#ffffff',
                                        didOpen: () => {
                                            Swal.showLoading();
                                        },
                                    });
                                },
                                complete: function() {
                                    if (loadingSwal) {
                                        loadingSwal.close();
                                    }
                                }
                            });

                            $('.btn-logout').on('click', async function(e){
                                e.preventDefault();

                                if(await salert({text: '정말 로그아웃 하겠습니까?'})){
                                    $.ajax({
                                        url: '/logout',
                                        type: 'POST',
                                        success: function(res) {
                                            alert(res?.msg);
                                            if( res?.status === 200 ){
                                                location.href = "/";
                                            }
                                        },
                                        error: function(xhr) {
                                            var res = xhr?.responseJSON;
                                            alert(res?.error?.message ?? '오류가 발생했습니다.');
                                        }
                                    });
                                }

                            });
                        });
                    </script>


                    @if (session('alert'))
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: '알림',
                                text: "{{ session('alert') }}",
                                confirmButtonText: '확인',
                                timer: 1300, // 후에 자동으로 닫힘
                                timerProgressBar: true // 진행률 바를 보여주어 남은 시간을 시각화
                            });
                        </script>
                    @endif

                    @yield('content')
                </div>
            </div>

            <x-footer />
        </div>

    </div>

</body>
</html>
