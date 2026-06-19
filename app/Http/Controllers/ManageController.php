<?php

namespace App\Http\Controllers;

use App\Enums\Admin;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\DeniedRoute;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageController extends Controller
{
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function list(): View
    {
        $role         = $this->request->input('role') ?: '';
        $isActive     = $this->request->input('isActive') ?: '';
        $deninedRoute = $this->request->input('deninedRoute') ?: [];
        $searchType   = $this->request->input('searchType') ?: Admin::ListSearchUsername;
        $searchText   = $this->request->input('searchText') ?: '';
        $page         = $this->request->input('page') ?: 1;
        $pageSize     = $this->request->input('pageSize') ?: 30;
        $params       = [
            'role'              => $role,
            'isActive'          => $isActive,
            'deninedRoute'      => $deninedRoute,
            'searchType'        => $searchType,
            'searchText'        => $searchText,
            'sort'              => 'desc',
            'page'              => $page,
            'pageSize'          => $pageSize,
            'roles'             => Role::cases(),
            'actives'           => Admin::isActives(),
            'searchTypes'       => Admin::listSearchTypes(),
            'deninedRoutesObjs' => DeniedRoute::get()
        ];

        return view('manages', $params);
    }


}
