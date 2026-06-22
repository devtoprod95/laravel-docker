@extends('layouts.app')

@section('title', '관리자')

@section('content')
<div class="row row-cards mt-2">

    {{-- 1. 검색 필터 영역 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">검색 필터</h3></div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <label class="form-label">권한 등급</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role"
                                    id="role_all" value=""
                                    {{ empty($role) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="role_all">전체</label>
                                </div>
                                @foreach ($roles as $obj)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role"
                                        id="role_{{ $obj->value }}" value="{{ $obj->value }}"
                                        {{ $role == $obj->value ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="role_{{ $obj->value }}">{{ $obj->label() }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">활성화 여부</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="isActive"
                                    id="isActive_all" value=""
                                    {{ empty($isActive) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="isActive_all">전체</label>
                                </div>
                                @foreach ($actives as $value => $obj)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="isActive"
                                        id="isActive_{{ $value }}" value="{{ $value }}"
                                        {{ $isActive == $value ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="isActive_{{ $value }}">{{ $obj }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">

                    <div class="row">
                        <div class="col-md-6 border-end">
                            <label class="form-label">접근 불가 페이지</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($deninedRoutesObjs as $obj)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="deninedRoute[]"
                                        id="deninedRoute_{{ $obj->id }}" value="{{ $obj->id }}"
                                        {{ in_array($obj->id, $deninedRoute) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="deninedRoute_{{ $obj->id }}">{{ $obj->route_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">회원가입 시작일</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                                        </span>
                                        <input class="form-control datepicker" name="startDate" placeholder="YYYY-MM-DD" value='{{ $startDate }}' />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">회원가입 종료일</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                                        </span>
                                        <input class="form-control datepicker" name="endDate" placeholder="YYYY-MM-DD" value='{{ $endDate }}' />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <hr class="my-3">

                    <div class="mb-0">
                        <div class="col-12 d-flex gap-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">검색 타입</label>
                                <select class="form-select" name="searchType">
                                    @foreach ($searchTypes as $value => $label)
                                        <option value="{{ $value }}" {{ ($searchType == $value) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">검색어</label>
                                <input type="text" class="form-control" name="searchText" value="{{ $searchText }}" placeholder="검색어 입력">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary px-4 btn-submit">검색</button>
                                <a href="{{ request()->url() }}" class="btn btn-outline-secondary px-4">초기화</a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- 2. 결과 목록 영역 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">관리자 목록</h3>
                <a href="{{ route('manage.view') }}" class="btn btn-success"><i class="ti ti-plus me-1"></i> 관리자 생성</a>
            </div>

            <div id="example-table"></div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var table;
        const apiUrl = "{{ route('manage.list') }}";

        function initTable(params = {}) {
            table = new Tabulator("#example-table", {
                ajaxURL: apiUrl,
                initialSort:[
                    {column: "{{ $sort['field'] }}", dir: "{{ $sort['dir'] }}"},
                ],
                paginationInitialPage: parseInt("{{ $page }}") || 1,
                columns: [
                    {
                        formatter: "rowSelection",
                        titleFormatter: "rowSelection",
                        headerSort: false,
                        width: 60,
                        clipboard: false,
                    },
                    { title: "No", field: "id", width: 80, clipboard: false, headerSort: true,
                        formatter: function(cell) {
                            var table = cell.getTable();
                            var totalRows = table.getDataCount("active");  // 현재 필터된 전체 수
                            var rowIndex = cell.getRow().getPosition();     // 현재 행 위치 (1부터)
                            return totalRows - rowIndex + 1;
                        }
                    },
                    { title: "아이디", field: "username", headerSort: true, headerMenu: window.tabulatorHeaderMenu },
                    { title: "이름", field: "name", headerSort: true, headerMenu: window.tabulatorHeaderMenu },
                    {
                        title: "권한",
                        field: "roles",
                        headerSort: true,
                        headerMenu: window.tabulatorHeaderMenu,
                        formatter: function(cell) {
                            const roles = cell.getValue();
                            if (!roles || (Array.isArray(roles) && roles.length === 0)) {
                                return '<span class="text-muted">-</span>';
                            }
                            return roles.map(role => {
                                return `<div class="mb-1"><span class="badge bg-blue-lt">${role.display_name}</span></div>`;
                            }).join("");
                        }
                    },
                    { title: "활성여부", field: "is_active", hozAlign: "center", formatter: "tickCross", width: 130, headerSort: true, headerMenu: window.tabulatorHeaderMenu },
                    { title: "가입일", field: "created_at", width: 200, headerSort: true, headerMenu: window.tabulatorHeaderMenu,
                        formatter: function(cell) {
                            return new Date(cell.getValue()).toLocaleString('ko-KR');
                        },
                        accessorClipboard: function(value, data) {
                            return new Date(value).toLocaleString('ko-KR');
                        }
                    },
                    { title: "마지막 로그인일<br>마지막 로그인IP", field: "last_login_at", width: 200, headerSort: true, headerMenu: window.tabulatorHeaderMenu,
                        formatter: function(cell) {
                            var rowData = cell.getRow().getData();
                            var loginAt = rowData.last_login_at ? new Date(rowData.last_login_at).toLocaleString('ko-KR') : '-';
                            var loginIp = rowData.last_login_ip || '-';
                            return `<div>${loginAt}</div><span style="font-size: 0.9em;">${loginIp}</span>`;
                        },
                        accessorClipboard: function(value, data) {
                            return new Date(value).toLocaleString('ko-KR');
                        }
                    },
                    {
                        title: "관리",
                        width: 120,
                        clipboard: false,
                        formatter: function(cell, formatterParams, onRendered) {
                            var $container = $("<div>").addClass("d-flex flex-column align-items-center gap-1 py-1");

                            var $editBtn = $("<button>")
                                .addClass("btn btn-sm btn-primary w-100") // w-100으로 너비 통일
                                .text("수정")
                                .on("click", function() {
                                    var data = cell.getRow().getData();
                                    console.log("수정:", data);
                                });

                            // 삭제 버튼 (btn-danger, btn-sm)
                            var $deleteBtn = $("<button>")
                                .addClass("btn btn-sm btn-danger w-100")
                                .text("삭제")
                                .on("click", function() {
                                    var data = cell.getRow().getData();
                                    console.log("삭제:", data);
                                });

                            return $container.append($editBtn, $deleteBtn).get(0);
                        }
                    }
                ],
            });
        }

        // 초기 로딩
        initTable();

        function getSelectedIds() {
            let selectedRows = table.getSelectedRows();
            let ids = selectedRows.map(row => row.getData().id);

            console.log("선택된 ID 목록:", ids);
            return ids;
        }

        $('.btn-submit').click(function(){
            getSelectedIds();
            table.setData();
        });
    })
</script>
@endsection
