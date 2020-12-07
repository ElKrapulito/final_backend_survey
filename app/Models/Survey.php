<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }

    protected $fillable = ['title', 'begin_date', 'end_date', 'auth', 'count', 'state', 'user_id'];
}
