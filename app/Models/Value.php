<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasFactory;

    public function answer_question(){
        return $this->belongsTo(AnswerQuestion::class);
    }

    protected $fillable = ['answer_question_id', 'value'];
}
