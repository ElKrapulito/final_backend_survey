<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerQuestion extends Model
{
    use HasFactory;

    public function values(){
        return $this->hasMany(Value::class);
    }

    public function inputs(){
        return $this->belongsToMany(Input::class);
    }

    protected $fillable = ['answer_id', 'question_id'];
}
