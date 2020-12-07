<?php

namespace App\Http\Controllers;

use App\Models\Input;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $surveys = Survey::all();
        return response()->json([
            'message' => 'success',
            'surveys' => $surveys
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
        $validatedData = Validator::make($request->json()->all(), [
            'title' => 'required|max:255',
            'begin_date' => 'required|date',
            'end_date' => 'required|date',
            'auth' => 'required|numeric',
            'count' => 'required|numeric',
            'state' => 'required|numeric',
            'user_id' => 'required|numeric',
            'questions' => 'required|array'
        ]);
        if ($validatedData->fails()) {
            error_log(json_encode($validatedData->failed()));
            return response()->json([
                'message' => 'error data not validated'
            ]);
        }
        try {
            DB::beginTransaction();
            $survey = Survey::create([
                'title' => $request->title,
                'begin_date' => $request->begin_date,
                'end_date' => $request->end_date,
                'auth' => $request->auth,
                'count' => $request->count,
                'state' => $request->state,
                'user_id' => $request->user_id
            ]);
            foreach ($request->questions as $question) {
                $ques = Question::create([
                    'title' => $question['title'],
                    'type' => $question['type'],
                    'survey_id' => $survey['id'],
                    'position' => $question['position']
                ]);
                foreach ($question['inputs'] as $input) {
                    Input::create([
                        'value' => $input['value'],
                        'text' => $input['text'],
                        'position' => $input['position'],
                        'question_id' => $ques->id
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            error_log($th);
            return response()->json([
                'message' => 'error',
            ]);
        }


        return response()->json([
            'message' => 'success',
            'survey' => $survey
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $survey = Survey::with(['questions' => function ($q) {
            $q->orderBy('position', 'ASC');
        }, 'questions.inputs' => function ($q) {
            $q->orderBy('position', 'ASC');
        }])->where('id', $id)->first();
        if (!isset($survey)) {
            return response()->json([
                'message' => 'error survey not found',
            ]);
        }

        return response()->json([
            'message' => 'success',
            'survey' => $survey
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = Validator::make($request->json()->all(), [
            'title' => 'required|max:255',
            'begin_date' => 'required|date',
            'end_date' => 'required|date',
            'auth' => 'required|numeric',
            'count' => 'required|numeric',
            'state' => 'required|numeric',
            'user_id' => 'required|numeric',
            'questions' => 'required|array'
        ]);
        if ($validatedData->fails()) {
            error_log(json_encode($validatedData->failed()));
            return response()->json([
                'message' => 'error data not validated'
            ]);
        }
        try {
            DB::beginTransaction();
            $survey = Survey::find($id);
            $survey->update([
                'title' => $request->title,
                'begin_date' => $request->begin_date,
                'end_date' => $request->end_date,
                'auth' => $request->auth,
                'count' => $request->count,
                'state' => $request->state,
                'user_id' => $request->user_id
            ]);
            foreach ($request->questions as $question) {
                if (isset($question['id'])) {
                    $ques = Question::find($question['id']);
                    $ques->update([
                        'title' => $question['title'],
                        'type' => $question['type'],
                        'position' => $question['position']
                    ]);
                } else {
                    $ques = Question::create([
                        'title' => $question['title'],
                        'type' => $question['type'],
                        'survey_id' => $survey['id'],
                        'position' => $question['position']
                    ]);
                }
                if (isset($question['inputs'])) {
                    foreach ($question['inputs'] as $input) {
                        if (isset($input['id'])) {
                            $inp = Input::find($input['id']);
                            $inp->update([
                                'value' => $input['value'],
                                'text' => $input['text'],
                                'position' => $input['position'],
                            ]);
                        } else {
                            Input::create([
                                'value' => $input['value'],
                                'text' => $input['text'],
                                'position' => $input['position'],
                                'question_id' => $ques->id
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            error_log($th);
            return response()->json([
                'message' => 'error',
            ]);
        }


        return response()->json([
            'message' => 'success',
            'survey' => $survey
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $survey = Survey::find($id);
        if (!isset($survey)) {
            return response()->json([
                'message' => 'survey not found to be deleted'
            ]);
        }
        foreach ($survey->questions as $question) {
            Input::where('question_id', $question->id)->delete();
        }
        Question::where('survey_id', $survey->id)->delete();
        $survey->delete();
        return response()->json([
            'message' => 'success'
        ]);
    }

    public function stats($id)
    {
        
        try {
            $result = DB::table('answers as an')
            ->join('answer_questions as aq', DB::raw('an.id'), DB::raw('aq.answer_id'))
            ->join('answer_question_input as ai', DB::raw('ai.answer_question_id'), DB::raw('aq.id'))
            ->where('an.survey_id', $id)
            ->groupBy(Db::raw('ai.input_id, aq.question_id'))
            ->select(DB::raw('aq.question_id, ai.input_id , COUNT(ai.input_id) as input_count'))
            ->get();
        } catch (\Throwable $th) {
            error_log($th);
            return response()->json([
                'message' => 'error'
            ]);
        }

        return response()->json([
            'message' => 'success',
            'result' => $result
        ]);
    }

    public function statsQuestion ($id) {
        // SELECT aq.question_id , COUNT(ai.input_id) 
        // from answers an 
        // join answer_questions aq 
        // on an.id = aq.answer_id
        // join answer_question_input ai
        // on ai.answer_question_id = aq.id 
        // where an.survey_id = 26
        // group by aq.question_id 
        try {
            $result = DB::table('answers as an')
            ->join('answer_questions as aq', DB::raw('an.id'), DB::raw('aq.answer_id'))
            ->join('answer_question_input as ai', DB::raw('ai.answer_question_id'), DB::raw('aq.id'))
            ->where('an.survey_id', $id)
            ->groupBy(Db::raw('aq.question_id'))
            ->select(DB::raw('aq.question_id , COUNT(ai.input_id) as input_count_total'))
            ->get();
        } catch (\Throwable $th) {
            error_log($th);
            return response()->json([
                'message' => "error"
            ]);
        }
        return response()->json([
            'message' => 'success',
            'result' => $result
        ]);
    }
}
