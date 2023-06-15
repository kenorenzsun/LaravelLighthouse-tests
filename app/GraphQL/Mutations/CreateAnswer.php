<?php

namespace App\GraphQL\Mutations;

use App\Models\Answer;
use App\Models\Question;

final class CreateAnswer
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $answerIsFirstToQuestion = Question::find($args['question_id'])->answers->count() === 0;

        return Answer::create([
            "answer" => $args['answer'],
            "is_correct" => $answerIsFirstToQuestion,
            "question_id" => $args['question_id'],
        ]);
    }
}
