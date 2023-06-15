<?php

namespace App\GraphQL\Mutations;

use App\Models\Answer;

final class DeleteAnswer
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $answer = Answer::find($args['id']);
        $question = $answer->question;

        $answer->delete();

        $question->answers()->first()->update(['is_correct' => true]);

        return $answer;
    }
}
