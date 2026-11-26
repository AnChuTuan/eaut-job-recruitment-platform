<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_profile_id',
        'cv_title',
        'cv_url',
        'file_size',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'file_size' => 'integer',
    ];

    public function candidateProfile()
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_profile_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'cv_id');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if ($this->file_size >= 1048576) {
            return round($this->file_size / 1048576, 2) . ' MB';
        }
        if ($this->file_size >= 1024) {
            return round($this->file_size / 1024, 1) . ' KB';
        }
        return $this->file_size . ' B';
    }
}
