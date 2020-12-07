<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Survey;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function mySurveys ($id) {
        $surveys = Survey::where('user_id', $id)->get();
        if(isset($surveys)){
            return response()->json([
                "message" => 'success',
                "surveys" => $surveys
            ]);
        }

        return response()->json([
            'message' => 'error surveys not found'
        ]);

    }

    public function myAnswers ($id) {
        $answers =  Answer::with('survey')->where('user_id', $id)->get();
        if(isset($answers)){
            return response()->json([
                "message" => 'success',
                "answers" => $answers
            ]);
        }

        return response()->json([
            'message' => 'error answers not found'
        ]);

    }
}
