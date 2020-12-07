<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function inputs () {
        return $this->hasMany(Input::class);
    }

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

    protected $fillable = ['title','survey_id', 'type', 'position'];
}
