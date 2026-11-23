<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'logo_url',
        'tax_code',
        'address',
        'email',
        'phone',
        'website',
        'industry',
        'company_size',
        'description',
        'verification_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    public function activeJobs()
    {
        return $this->hasMany(Job::class, 'company_id')
                    ->where('status', 'approved')
                    ->whereDate('deadline', '>=', now()->toDateString());
    }
}
