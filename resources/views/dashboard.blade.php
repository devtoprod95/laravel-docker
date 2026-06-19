@extends('layouts.app')

@section('title', '대시보드')

@section('content')
<div class="row row-cards mt-2">

    {{-- 1. 카드 크기 고정 및 텍스트 래핑 방지 --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar"><i class="ti ti-users"></i></span>
                    </div>
                    <div class="col text-truncate">
                        <div class="font-weight-medium">1,234</div>
                        <div class="text-muted small">총 사용자</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar"><i class="ti ti-eye"></i></span>
                    </div>
                    <div class="col text-truncate">
                        <div class="font-weight-medium">56</div>
                        <div class="text-muted small">오늘 방문자</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. 테이블 레이아웃 강제 고정 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">사용자 목록</h3>
            </div>
            {{-- table-nowrap를 추가하여 텍스트 줄바꿈 강제 방지 --}}
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-nowrap">
                    <thead>
                        <tr>
                            <th class="w-50">이름</th>
                            <th>이메일</th>
                            <th>가입일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2">홍</span>
                                    <div>홍길동</div>
                                </div>
                            </td>
                            <td class="text-muted">test@example.com</td>
                            <td class="text-muted">2023-01-01</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
