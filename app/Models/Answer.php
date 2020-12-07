<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function answer_questions(){
        return $this->hasMany(AnswerQuestion::class);
    }

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

    protected $fillable = ['survey_id', 'user_id'];
}
