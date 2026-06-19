@extends('layouts.app')

@section('title', '사용자 관리')

@section('content')
<div class="row row-cards mt-2">

    {{-- 1. 검색 필터 영역 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">검색 필터</h3></div>
            <div class="card-body">
                <form method="GET">

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
                    <hr class="my-3">

                    <div class="row">
                        <div class="col-12 d-flex gap-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">정렬</label>
                                <select class="form-select" name="sort">
                                    <option value="desc" {{ $sort == 'desc' ? 'selected' : '' }}>최신순</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">표시 수</label>
                                <select class="form-select" name="perPage">
                                    @foreach ([30, 50, 100, 20] as $size)
                                        <option value="{{ $size }}" {{ $pageSize == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
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
                                <button type="submit" class="btn btn-primary px-4">검색</button>
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
                <h3 class="card-title">사용자 목록 (총 128명)</h3>
                <a href="#" class="btn btn-success"><i class="ti ti-plus me-1"></i> 유저 추가</a>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-nowrap">
                    <thead>
                        <tr>
                            <th class="w-10">ID</th>
                            <th class="w-25">이름</th>
                            <th class="w-15">권한</th>
                            <th class="w-20">상태</th>
                            <th class="text-center">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 5; $i++)
                        <tr>
                            <td>#100{{ $i }}</td>
                            <td>홍길동 {{ $i }}</td>
                            <td><span class="badge bg-purple-lt">관리자</span></td>
                            <td><span class="badge bg-green-lt">활성</span></td>
                            <td class="text-center">
                                <button class="btn btn-primary px-3 py-1 me-1">수정</button>
                                <button class="btn btn-danger px-3 py-1">삭제</button>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-center">
                <ul class="pagination">
                    <li class="page-item disabled"><a class="page-link" href="#">이전</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">다음</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
