<?php

namespace App\GraphQL\Mutations;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use Illuminate\Support\Facades\Log;

final class CreateResult
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = auth()->user();

        $score = 0;

        foreach ($args['answers'] as $answer) {
            if (Question::find($answer['question_id'])->answers()->where('id', $answer['answer_id'])->first()->is_correct) {
                $score++;
            }
        }

        $result = Result::create([
            "user_id" => $user->id,
            "quiz_id" => $args['quiz_id'],
            "score" => $score,
        ]);

        return $result;
    }
}
