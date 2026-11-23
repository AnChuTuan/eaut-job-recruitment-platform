<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'category',
        'job_level',
        'employment_type',
        'quantity',
        'deadline',
        'salary_min',
        'salary_max',
        'currency',
        'location',
        'description',
        'requirements',
        'benefits',
        'skills',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
                     ->whereDate('deadline', '>=', now()->toDateString());
    }

    public function getFormattedSalaryAttribute(): string
    {
        if (empty($this->salary_min) && empty($this->salary_max)) {
            return 'Thỏa thuận';
        }

        $formatNumber = function($val) {
            if ($val >= 1000000) {
                $mil = $val / 1000000;
                return (rtrim(rtrim(number_format($mil, 1, '.', ''), '0'), '.') . ' triệu');
            }
            return number_format($val, 0, ',', '.') . ' ' . $this->currency;
        };

        if ($this->salary_min && $this->salary_max) {
            return $formatNumber($this->salary_min) . ' - ' . $formatNumber($this->salary_max) . ' ' . $this->currency;
        }

        if ($this->salary_min) {
            return 'Từ ' . $formatNumber($this->salary_min) . ' ' . $this->currency;
        }

        return 'Đến ' . $formatNumber($this->salary_max) . ' ' . $this->currency;
    }
}
