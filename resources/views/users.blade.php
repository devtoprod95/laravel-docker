@extends('layouts.app')

@section('title', '사용자 관리')

@section('content')
<div class="row row-cards">

    {{-- 1. 검색 필터 영역 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">검색 필터</h3></div>
            <div class="card-body">
                <form action="#" method="GET">
                    {{-- 기존 필터 복구 및 셀렉트박스 유지 --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">권한 등급</label>
                            <select class="form-select" name="role">
                                <option value="">모든 권한</option>
                                <option value="admin">관리자</option>
                                <option value="user">일반 사용자</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">권한 등급2</label>
                            <select class="form-select" name="role2">
                                <option value="">모든 권한</option>
                                <option value="admin">관리자</option>
                                <option value="user">일반 사용자</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">상태 필터</label>
                        <div class="d-flex gap-3">
                            <label class="form-check"><input type="checkbox" class="form-check-input" checked> 활성</label>
                            <label class="form-check"><input type="checkbox" class="form-check-input"> 휴면</label>
                            <label class="form-check"><input type="checkbox" class="form-check-input"> 정지</label>
                        </div>
                    </div>

                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">검색 타입</label>
                            <select class="form-select"><option>이메일</option><option>전화번호</option></select>
                        </div>
                        <div class="col">
                            <label class="form-label">검색어</label>
                            <input type="text" class="form-control" placeholder="검색어 입력">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary px-4">검색</button>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-outline-secondary px-4">초기화</a>
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
