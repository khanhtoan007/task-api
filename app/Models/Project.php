<?php

namespace App\Models;

use App\Http\Enums\ProjectRoleEnum;
use App\Http\Enums\ProjectStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $created_by
 * @property User $createdBy
 * @property string $status
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<ProjectMember> $owners;
 * @property Collection<ProjectMember> $contributors;
 * @property Collection<ProjectMember> $members;
 */
final class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => ProjectStatusEnum::class,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->whereNull('parent_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(ProjectMember::class)
            ->where('role', ProjectRoleEnum::OWNER->value);
    }

    public function contributors(): HasMany
    {
        return $this->hasMany(ProjectMember::class)
            ->where('role', ProjectRoleEnum::CONTRIBUTOR->value);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function isOwner(User $user): bool
    {
        return $this->owners->contains('user_id', $user->id);
    }
}
