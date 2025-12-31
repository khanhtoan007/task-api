<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $project_id
 * @property string $user_id
 * @property string $role
 * @property Carbon $joined_at
 * @property Carbon $invited_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class ProjectMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'joined_at',
        'invited_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
