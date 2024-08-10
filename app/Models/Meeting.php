<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PersonalDetail;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'start',
        'end',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('status')->withTimestamps();
    }

    public function persons()
    {
        return $this->belongsToMany(PersonalDetail::class)->withPivot('status')->withTimestamps();
    }
}
