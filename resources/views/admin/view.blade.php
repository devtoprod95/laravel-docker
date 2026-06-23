@php
    use App\Enums\Admin;
@endphp

@extends('layouts.app')

@section('title')
    관리자 {{ empty($adminObj) ? '등록' : '수정' }}
@endsection

@section('content')
    <div class="row row-cards mt-2">
        <div class="col-12">
            <form id="ajaxForm" >
                <input type="hidden" name="id" value="{{ $adminObj->id ?? '' }}">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">관리자 정보 입력</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">아이디</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-user"></i>
                                    </span>
                                    <input type="text" class="form-control" name="username" placeholder="아이디를 입력하세요" value="{{ $adminObj->username ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">비밀번호</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="password" placeholder="비밀번호를 입력하세요" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">이름</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-id"></i>
                                    </span>
                                    <input type="text" class="form-control" name="name" placeholder="이름을 입력하세요" value="{{ $adminObj->name ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">권한</label>
                                <div class="form-selectgroup form-selectgroup-boxes">
                                    @foreach ($roles as $role)
                                        <label class="form-selectgroup-item">
                                            <input type="checkbox"
                                                name="roles[]"
                                                value="{{ $role->id }}"
                                                class="form-selectgroup-input"
                                                {{ !empty($adminObj) && in_array($role->id, $adminObj->roles->pluck('id')->toArray()) ? 'checked' : '' }}>

                                            <span class="form-selectgroup-label d-flex align-items-center p-2">
                                                {{ $role->display_name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">활성여부</label>
                                <div class="form-selectgroup">
                                    @foreach (Admin::isActives() as $value => $label)
                                        <label class="form-selectgroup-item">
                                            <input type="radio"
                                                name="is_active"
                                                value="{{ $value }}"
                                                class="form-selectgroup-input"
                                                {{ (isset($adminObj) && $adminObj->is_active == $value) || (!isset($adminObj) && $value == \App\Enums\Admin::ACTIVE->value) ? 'checked' : '' }}>

                                            <span class="form-selectgroup-label">
                                                <i class="ti {{ $value == \App\Enums\Admin::ACTIVE->value ? 'ti-check' : 'ti-x' }} me-1"></i>
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">뒤로가기</button>
                        <button type="button" class="btn btn-primary btn-save">{{ empty($adminObj) ? '등록' : '수정' }}하기</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.btn-save').on('click', async function(e) {
                var formData = new FormData($('#ajaxForm')[0]);
                let username = $('input[name=username]').val();
                let password = $('input[name=password]').val();
                let name     = $('input[name=name]').val();
                let id       = $('input[name=id]').val();
                let roles    = $('input[name="roles[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                let is_active = $('input[name=is_active]:checked').val();

                if( !username ){
                    await salert({text: '아이디를 입력해주세요.', cancel: false});
                    return false;
                }
                if( !password ){
                    await salert({text: '비밀번호를 입력해주세요.', cancel: false});
                    return false;
                }
                if( !name ){
                    await salert({text: '이름을 입력해주세요.', cancel: false});
                    return false;
                }
                if( roles.length < 1 ){
                    await salert({text: '권한을 최소 1개 선택해주세요.', cancel: false});
                    return false;
                }

                let confirmText = '관리자를 등록하시겠습니까?';
                if(id){
                    confirmText = '관리자를 수정하시겠습니까?';
                }
                if( await salert({text: confirmText}) ){
                    $.ajax({
                        url: "{{ route('admin.store') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            alert(res?.msg);
                            if( res?.status === 200 ){
                                location.href = "{{ route('admin.index') }}";
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
@endsection
