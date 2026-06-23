<?php

namespace App\Http\Controllers;

use App\Dtos\ManageListRequestDto;
use App\Dtos\ManageStoreRequestDto;
use App\Enums\Admin;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\StoreRequest;
use App\Models\Admin as ModelsAdmin;
use App\Models\DeniedRoute;
use App\Models\Role as ModelsRole;
use App\Services\ManageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ManageController extends Controller
{
    protected Request $request;
    protected ManageService $manageService;

    public function __construct(
        Request $request,
        ManageService $manageService
    )
    {
        $this->request       = $request;
        $this->manageService = $manageService;
    }

    public function index(): View
    {
        $role         = $this->request->input('role') ?: '';
        $isActive     = $this->request->input('isActive');
        $deninedRoute = $this->request->input('deninedRoute') ?: [];
        $startDate    = $this->request->input('startDate') ?: '';
        $endDate      = $this->request->input('endDate') ?: '';
        $searchType   = $this->request->input('searchType') ?: Admin::ListSearchUsername;
        $searchText   = $this->request->input('searchText') ?: '';
        $page         = $this->request->input('page') ?: 1;
        $size         = $this->request->input('size') ?: 30;
        $sort         = $this->request->input('sort')[0] ?? ['field' => 'id', 'dir' => 'desc'];
        $params       = [
            'role'              => $role,
            'isActive'          => $isActive,
            'deninedRoute'      => $deninedRoute,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'searchType'        => $searchType,
            'searchText'        => $searchText,
            'page'              => $page,
            'size'              => $size,
            'sort'              => $sort,
            'roles'             => Role::cases(),
            'actives'           => Admin::isActives(),
            'searchTypes'       => Admin::listSearchTypes(),
            'deninedRoutesObjs' => DeniedRoute::get()
        ];

        return view('manage.list', $params);
    }

    public function list(): JsonResponse
    {
        $role         = $this->request->input('role') ?: '';
        $isActive     = $this->request->input('isActive');
        $deninedRoute = $this->request->input('deninedRoute') ?: [];
        $startDate    = $this->request->input('startDate') ?: '';
        $endDate      = $this->request->input('endDate') ?: '';
        $searchType   = $this->request->input('searchType') ?: Admin::ListSearchUsername->value;
        $searchText   = $this->request->input('searchText') ?: '';
        $page         = $this->request->input('page') ?: 1;
        $size         = $this->request->input('size') ?: 30;
        $sort         = $this->request->input('sort')[0] ?? ['field' => 'id', 'dir' => 'desc'];
        $dtoBind      = [
            'role'         => $role,
            'isActive'     => $isActive,
            'deninedRoute' => $deninedRoute,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'searchType'   => $searchType,
            'searchText'   => $searchText,
            'page'         => $page,
            'size'         => $size,
            'sort'         => $sort,
        ];
        $dto = new ManageListRequestDto();
        $dto->bind($dtoBind);
        $result = $this->manageService->list($dto);

        return response()->json($result);
    }

    public function view(int|null $id = null): View
    {
        $adminObj = null;
        if( $id ){
            $adminObj = ModelsAdmin::with('roles')->find($id);
        }

        $params = [
            'adminObj'          => $adminObj,
            'roles'             => ModelsRole::get(),
            'actives'           => Admin::isActives(),
            'deninedRoutesObjs' => DeniedRoute::get()
        ];

        return view('manage.view', $params);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $validated = (object) $request->validated();
        $dto       = new ManageStoreRequestDto($validated);
        $result    = $this->manageService->store($dto);

        return apiRes(($result['isSuccess'] === true ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR), $result);
    }

    public function delete(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'integer',
        ], [
            'ids.required'  => '항목을 전달해주세요.',
            'ids.array'     => '잘못된 요청 형식입니다.',
            'ids.*.integer' => '유효하지 않은 ID 형식입니다.',
        ]);
        if ($validator->fails()) {
            return apiRes(Response::HTTP_BAD_REQUEST, helpersFailMessage());
        }

        $ids    = $this->request->input('ids');
        $result = $this->manageService->delete($ids);

        return apiRes(($result['isSuccess'] === true ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR), $result);
    }

    public function updateActive(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'ids'       => 'required|array',
            'ids.*'     => 'integer',
            'is_active' => 'required|string',
        ], [
            'ids.required'       => '항목을 전달해주세요.',
            'ids.array'          => '잘못된 요청 형식입니다.',
            'ids.*.integer'      => '유효하지 않은 ID 형식입니다.',
            'is_active.required' => '활성 여부를 전달해주세요.',
            'is_active.string'   => '잘못된 요청 형식입니다.',
        ]);
        if ($validator->fails()) {
            return apiRes(Response::HTTP_BAD_REQUEST, helpersFailMessage());
        }

        $ids      = $this->request->input('ids');
        $isActive = $this->request->input('is_active');
        $result   = $this->manageService->updateActive($ids, $isActive);

        return apiRes(($result['isSuccess'] === true ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR), $result);
    }
}
