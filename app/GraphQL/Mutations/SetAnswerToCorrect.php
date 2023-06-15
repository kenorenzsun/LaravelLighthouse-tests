<?php

namespace App\GraphQL\Mutations;

use App\Models\Answer;
use App\Models\Question;

final class SetAnswerToCorrect
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $answer = Answer::find($args['id']);
        $answer->update(['is_correct' => 1]);

        Question::find($args['question_id'])->answers()->where('answers.id', '!=', $args['id'])->update(['is_correct' => 0]);

        return $answer;
    }
}
