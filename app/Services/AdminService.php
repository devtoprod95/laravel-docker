<?php

namespace App\Services;

use App\Dtos\AdminListRequestDto;
use App\Dtos\AdminRoleListRequestDto;
use App\Dtos\AdminRoleStoreRequestDto;
use App\Dtos\AdminStoreRequestDto;
use App\Models\Admin;
use App\Models\Role;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    protected array $returnMsg = [];

    public function __construct()
    {
        $this->returnMsg = helpersDefaultMessage();
    }

    public function list(AdminListRequestDto $dto): LengthAwarePaginator
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

    public function store(AdminStoreRequestDto $dto): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $admin        = Admin::find($dto->id) ?? new Admin();
            $hashPassWord = Hash::make($dto->password);
            if ($admin->exists) {
                // 수정
                $passFlag = Hash::check($dto->password, $admin->password);
                if(!$passFlag){
                    throw new Exception('기존 비밀번호가 다릅니다.');
                }
            } else {
                // 신규
                $existing = Admin::where('username', $dto->username)->first();
                if ($existing !== null) {
                    throw new Exception('이미 사용 중인 아이디입니다.');
                }
                $admin->password = $hashPassWord;
            }

            $admin->username  = $dto->username;
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

    public function delete(array $ids): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $admins = Admin::whereIn('id', $ids)->get();
            if ($admins->isEmpty()) {
                throw new Exception('관리자를 찾을 수 없습니다.');
            }

            foreach ($admins as $admin) {
                $admin->delete();
            }

            $returnMsg = helpersSuccessMessage();
        } catch (\Throwable $th) {
            $returnMsg = helpersFailMessage($th->getMessage());
        }

        return $returnMsg;
    }

    public function updateActive(array $ids, string $isActive): array
    {
        $returnMsg = $this->returnMsg;

        try {
            Admin::whereIn('id', $ids)->update(['is_active' => $isActive]);

            $returnMsg = helpersSuccessMessage();
        } catch (\Throwable $th) {
            $returnMsg = helpersFailMessage($th->getMessage());
        }

        return $returnMsg;
    }

    public function roleList(AdminRoleListRequestDto $dto): LengthAwarePaginator
    {
        $query = Role::query()->withCount('deniedRoutes');

        // 차단 라우트 필터
        if (!empty($dto->deninedRoute)) {
            $query->whereHas('deniedRoutes', function($q2) use ($dto) {
                $q2->whereIn('denied_routes.id', $dto->deninedRoute);
            });
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

    public function roleInfo(int $id): array
    {
        $returnMsg = $this->returnMsg;
        $obj       = Role::with('deniedRoutes')->find($id);
        $returnMsg = helpersCustomArrayMessage(true, ['data' => $obj ?? []]);

        return $returnMsg;
    }

    public function roleDelete(array $ids): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $objs = Role::whereIn('id', $ids)->get();
            if ($objs->isEmpty()) {
                throw new Exception('권한을 찾을 수 없습니다.');
            }

            foreach ($objs as $obj) {
                $obj->delete();
            }

            $returnMsg = helpersSuccessMessage();
        } catch (\Throwable $th) {
            $returnMsg = helpersFailMessage($th->getMessage());
        }

        return $returnMsg;
    }

    public function roleStore(AdminRoleStoreRequestDto $dto): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $exists = Role::where('display_name', $dto->displayName)->exists();
            if( $exists ){
                throw new Exception('이미 사용중인 권한명이 있습니다.');
            }

            $exists = Role::where('name', $dto->name)->exists();
            if( $exists ){
                throw new Exception('이미 사용중인 권한값이 있습니다.');
            }

            $obj               = new Role();
            $obj->display_name = $dto->displayName;
            $obj->name         = $dto->name;
            $obj->save();

            $returnMsg = helpersSuccessMessage();
        } catch (\Throwable $th) {
            $returnMsg = helpersFailMessage($th->getMessage());
        }

        return $returnMsg;
    }
}
