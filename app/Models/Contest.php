<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
final class Contest extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'contest_address',
        'range',
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
