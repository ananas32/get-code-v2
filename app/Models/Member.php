<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public function emails()
    {
        return $this->hasMany(MemberEmail::class);
    }

    public function phones()
    {
        return $this->hasMany(MemberPhone::class);
    }

    public function workHistories()
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
