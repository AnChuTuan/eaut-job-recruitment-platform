<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_url',
        'career_summary',
        'skills',
        'experience',
        'education',
        'completion_score',
        'is_searchable',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
