<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function answer_questions(){
        return $this->belongsToMany(AnswerQuestion::class);
    }

    protected $fillable = ['value', 'text', 'question_id', 'position'];
}
