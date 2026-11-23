<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'is_searchable' => 'boolean',
        'completion_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cvs()
    {
        return $this->hasMany(Cv::class, 'candidate_profile_id')->latest();
    }

    public function defaultCv()
    {
        return $this->hasOne(Cv::class, 'candidate_profile_id')->where('is_default', true);
    }

    /**
     * Calculate and save the profile completion percentage.
     * Total: 100%
     * - Basic info (name, email, phone): 15%
     * - Avatar: 15%
     * - Career summary: 15%
     * - Skills: 15%
     * - Work Experience: 15%
     * - Education: 10%
     * - At least 1 CV uploaded: 15%
     */
    public function recalculateCompletionScore(): int
    {
        $score = 0;
        $user = $this->user;

        if ($user && !empty($user->name) && !empty($user->email) && !empty($user->phone)) {
            $score += 15;
        } elseif ($user && (!empty($user->name) || !empty($user->phone))) {
            $score += 8;
        }

        if (!empty($this->avatar_url)) {
            $score += 15;
        }

        if (!empty(trim($this->career_summary ?? ''))) {
            $score += 15;
        }

        if (!empty(trim($this->skills ?? ''))) {
            $score += 15;
        }

        if (!empty(trim($this->experience ?? ''))) {
            $score += 15;
        }

        if (!empty(trim($this->education ?? ''))) {
            $score += 10;
        }

        if ($this->cvs()->exists()) {
            $score += 15;
        }

        $this->completion_score = min(100, $score);
        $this->save();

        return $this->completion_score;
    }
}
