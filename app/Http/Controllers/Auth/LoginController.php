<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show() {
        return view('auth.login');
    }

    // 로그인 로직 처리
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => '아이디를 입력해주세요.',
            'password.required' => '비밀번호를 입력해주세요.',
        ]);

        $member = Member::where('username', $request->username)->first();
        if (!$member) {
            return back()->withErrors([
                'username' => '존재하지 않는 아이디입니다.',
            ])->withInput();
        }

        // 2. 비밀번호 검증 (Hash::check 사용)
        if (!Hash::check($request->password, $member->password)) {
            return back()->withErrors([
                'password' => '비밀번호가 일치하지 않습니다.',
            ])->withInput();
        }

        Auth::guard('admin')->login($member);
        $request->session()->regenerate();

        // 사용자가 가려던 페이지가 있었다면 그곳으로 보내고, 없다면 기본값으로
        return redirect()->intended('/');
    }

    // 로그아웃
    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return apiRes(Response::HTTP_OK, helpersSuccessMessage());
        } catch (\Throwable $th) {
            return apiRes(Response::HTTP_BAD_GATEWAY, helpersFailMessage($th->getMessage()));
        }



    }
}
