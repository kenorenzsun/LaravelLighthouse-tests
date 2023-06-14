<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = Question::all();

        foreach ($questions as $question) {
            $question->answers()->createMany([
                ["answer" => "Choice 1"],
                ["answer" => "Choice 2"],
                ["answer" => "Choice 3", 'is_correct' => true],
                ["answer" => "Choice 4"],
            ]);
        }
    }
}
