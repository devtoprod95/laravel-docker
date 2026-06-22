<?php

namespace App\Services;

use App\Dtos\ManageListRequestDto;
use App\Dtos\ManageStoreRequestDto;
use App\Models\Admin;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class ManageService
{
    protected array $returnMsg = [];

    public function __construct()
    {
        $this->returnMsg = helpersDefaultMessage();
    }

    public function list(ManageListRequestDto $dto): LengthAwarePaginator
    {
        $query = Admin::query()->with('roles');

        // 역할 필터
        if (!empty($dto->role)) {
            $query->whereHas('roles', function($q) use ($dto) {
                $q->where('name', $dto->role);
            });
        }

        // 활성여부 필터
        if ($dto->isActive !== '' && $dto->isActive !== null) {
            $query->where('is_active', $dto->isActive);
        }

        // 차단 라우트 필터
        if (!empty($dto->deninedRoute)) {
            $query->whereHas('roles', function($q) use ($dto) {
                $q->whereHas('deniedRoutes', function($q2) use ($dto) {
                    $q2->whereIn('denied_routes.id', $dto->deninedRoute);
                });
            });
        }

        // 가입일 범위
        if (!empty($dto->startDate)) {
            $query->whereDate('created_at', '>=', $dto->startDate);
        }
        if (!empty($dto->endDate)) {
            $query->whereDate('created_at', '<=', $dto->endDate);
        }

        // 검색
        if (!empty($dto->searchText)) {
            $query->where($dto->searchType, 'like', '%' . $dto->searchText . '%');
        }

        // 정렬
        if (!empty($dto->sort)) {
            $query->orderBy($dto->sort['field'], $dto->sort['dir']);
        }

        return $query->paginate($dto->size, ['*'], 'page', $dto->page);
    }

    public function store(ManageStoreRequestDto $dto): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $admin = Admin::find($dto->id) ?? new Admin();
            if ($admin->exists) {
                $hashPassWord = Hash::make($dto->password);
                if( $admin->password !== $hashPassWord ){
                    throw new Exception('기존 비밀번호가 다릅니다.');
                }
            } else {
                $existing = Admin::where('username', $dto->username)->first();
                if ($existing !== null) {
                    throw new Exception('이미 사용 중인 아이디입니다.');
                }
            }

            $admin->username  = $dto->username;
            $admin->password  = Hash::make($dto->password);
            $admin->name      = $dto->name;
            $admin->is_active = $dto->isActive;
            $admin->save();

            $admin->roles()->sync($dto->roles);

            $returnMsg = helpersSuccessMessage();
        } catch (\Throwable $th) {
            $returnMsg = helpersFailMessage($th->getMessage());
        }

        return $returnMsg;
    }
}
