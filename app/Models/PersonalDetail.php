<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'title',
        'avatar',
        'linkedin_url',
        'companyName',
        'companyLikedinUrl',
        'companyEmployees',
    ];

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class)->withPivot('status')->withTimestamps();
    }
}
