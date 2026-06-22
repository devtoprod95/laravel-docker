<?php

namespace App\Http\Controllers;

use App\Dtos\ManageListRequestDto;
use App\Enums\Admin;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\DeniedRoute;
use App\Services\ManageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $isActive     = $this->request->input('isActive') ?: '';
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

        return view('manages', $params);
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


}
