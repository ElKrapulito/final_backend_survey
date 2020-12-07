<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerQuestion;
use App\Models\Input;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $answers = Answer::with('survey')->get();
        return response()->json([
            'message' => 'success',
            'answers' => $answers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $answer = Answer::create([
                'survey_id' => $request->survey_id,
                'user_id' => isset($request->user_id) ? $request->user_id : null
            ]);

            foreach ($request->answer_questions as $ans) {
                $ansquestion = AnswerQuestion::create([
                    'question_id' => $ans['question_id'],
                    'answer_id' => $answer->id
                ]);
                if (isset($ans['values'])) {
                    foreach ($ans['values'] as $val) {
                        Value::create([
                            'answer_question_id' => $ansquestion->id,
                            'value' => $val['value']
                        ]);
                    }
                }
                if(isset($ans['inputs'])){
                    $inputs = [];
                    foreach ($ans['inputs'] as $inp) {
                        array_push($inputs, Input::find($inp['id']));
                    }

                    $ansquestion->inputs()->saveMany($inputs);
                }
            }
            DB::commit();
            return response()->json([
                "message" => "success",
                "answer" => $answer
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            error_log($th);
            error_log($request);
            return response()->json([
                'message' => 'error no answer saved'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $answer = Answer::with('answer_questions.values', 'answer_questions.inputs')->where('id', $id)->first();
        if(!isset($answer)){
        return response()->json([
                'message' => 'error no answer found'
            ]);
        }
        
        return response()->json([
            'message' => 'success',
            'answer' => $answer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function edit(Answer $answer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $answer = Answer::find($id);

            foreach ($request->answer_questions as $ans) {
                if(isset($ans['id'] )) {
                    $ansquestion = AnswerQuestion::find($ans['id']);
                } else {
                    $ansquestion = AnswerQuestion::create([
                        'question_id' => $ans['question_id'],
                        'answer_id' => $answer->id
                    ]);
                }
                if (isset($ans['values'])) {
                    foreach ($ans['values'] as $val) {
                        if($val['id']) {
                            $newVal = Value::find($val['id']);
                            $newVal->update([
                                'value' => $val['value']
                            ]);
                        } else {
                            Value::create([
                                'answer_question_id' => $ansquestion->id,
                                'value' => $val['value']
                            ]);
                        }
                        
                    }
                }
                if(isset($ans['inputs'])){
                    $inputs = [];
                    $ansquestion->inputs()->detach();
                    foreach ($ans['inputs'] as $inp) {
                        array_push($inputs, Input::find($inp['id']));
                    }
                    $ansquestion->inputs()->saveMany($inputs);
                }
            }
            DB::commit();
            return response()->json([
                "message" => "success",
                "answer" => $answer
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            error_log($th);
            error_log($request);
            return response()->json([
                'message' => 'error no answer saved'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $answer = Answer::with('answer_questions.values', 'answer_questions.inputs')->where('id', $id)->first();
        if(isset($answer)){
            foreach ($answer->answer_questions as $ans) {
                if(isset($ans->inputs)){
                    $ans->inputs()->detach();
                }

                if(isset($ans->values)){
                    foreach ($ans->values as $value) {
                        $value->delete();
                    }
                }
                $ans->delete();
            }
            $answer->delete();
            return response()->json([
                'message' => 'success'
            ]);
        }

        return response()->json([
            'message' => 'error no answer found to delete'
        ]);
        
    }
}
