<?php

namespace App\GraphQL\Queries;

use App\Models\Question;

final class Questions
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $query = Question::query();

        if (isset($args['question'])) {
            $query->where("question", 'LIKE', "%" . $args['question'] . "%");
        }

        if ($args['quiz_id']) {
            $query->where('quiz_id', $args['quiz_id']);
        }

        return $query;
    }
}
