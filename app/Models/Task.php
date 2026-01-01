<?php

namespace App\Models;

use App\Http\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $created_by
 * @property string $assigned_to
 * @property string $project_id
 * @property string $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
        'assigned_to',
        'parent_id',
        'project_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TaskStatusEnum::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
