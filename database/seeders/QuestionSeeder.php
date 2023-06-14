<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            $quiz->questions()->create([
                "question" => "Question #1",
            ]);
        }
    }
}
