<?php

namespace App\GraphQL\Queries;

use App\Models\Category;

final class Categories
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $query = Category::query();

        if (isset($args['name'])) {
            $query->where("name", 'LIKE', "%" . $args['name'] . "%");
        }

        return $query;
    }
}
