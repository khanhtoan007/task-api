<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property uuid $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property uuid $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['title', 'description', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
