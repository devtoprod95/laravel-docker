<?php

namespace App\Services;

use App\Dtos\ManageListRequestDto;
use App\Models\Admin;

class ManageService
{
    protected array $returnMsg = [];

    public function __construct()
    {
        $this->returnMsg = helpersDefaultMessage();
    }

    public function list(ManageListRequestDto $dto)
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
}
