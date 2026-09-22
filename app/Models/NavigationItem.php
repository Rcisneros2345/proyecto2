<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'section',
        'label',
        'route_name',
        'icon',
        'permission_action',
        'sort_order',
        'admin_only',
        'active',
    ];

    protected $casts = [
        'admin_only' => 'boolean',
        'active' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
