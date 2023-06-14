<?php

namespace App\GraphQL\Queries;

use App\Models\Quiz;

final class Quizzes
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = auth()->user();

        $query = Quiz::query();

        if (isset($args['name'])) {
            $query->where("name", 'LIKE', "%" . $args['name'] . "%");
        }

        if ($args['category_id']) {
            $query->where('category_id', $args['category_id']);
        }

        if (!$user->is_admin) {
            $query->whereDoesntHave('results', function ($resultQuery) use ($user) {
                $resultQuery->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
