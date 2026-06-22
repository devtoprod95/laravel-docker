<?php
namespace App\Dtos;

class ManageListRequestDto extends Dto
{
    public string $role        = '';
    public ?string $isActive   = '';
    public array $deninedRoute = [];
    public string $startDate   = '';
    public string $endDate     = '';
    public string $searchType  = '';
    public string $searchText  = '';
    public array $sort         = ['field' => 'id', 'dir' => 'desc'];
    public int $page           = 1;
    public int $size           = 30;

    public function bind(mixed $data): void
    {
        $this->role         = $data['role'];
        $this->isActive     = $data['isActive'];
        $this->deninedRoute = $data['deninedRoute'];
        $this->startDate    = $data['startDate'];
        $this->endDate      = $data['endDate'];
        $this->searchType   = $data['searchType'];
        $this->searchText   = $data['searchText'];
        $this->sort         = $data['sort'];
        $this->page         = $data['page'];
        $this->size         = $data['size'];
        $this->sort         = $data['sort'];
    }
}
