<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeniedRoute extends Model
{
    protected $table    = 'denied_routes';
    protected $fillable = [];
    protected $hidden   = [];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_denied_routes', // 중간 테이블명
            'route_id',              // 현재 모델의 FK
            'role_id'                // 상대 모델의 FK
        );
    }
}
