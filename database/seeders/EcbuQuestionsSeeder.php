<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Option;
use App\Models\AnalysisRule;
use App\Models\Analyse;

class EcbuQuestionsSeeder extends Seeder
{
    public function run()
    {
        $analyseId = 6; // Complete Urine Analysis

        // 1. Double ensure the analysis name is 'ecbu' as expected by the user in the UI
        Analyse::where('id', $analyseId)->update(['name' => 'ecbu']);

        // 2. Clean up ID 11 if it exists (redundant entry)
        Analyse::where('id', 11)->delete();

        // 3. Clean up existing questions for ID 6
        $existingQuestionIds = Question::where('analyse_id', $analyseId)->pluck('id');
        AnalysisRule::whereIn('question_id', $existingQuestionIds)->delete();
        Option::whereIn('question_id', $existingQuestionIds)->delete();
        Question::whereIn('id', $existingQuestionIds)->delete();

        // 4. Define the 11 detailed questions
        $questions = [
            [
                'question' => 'في أي وعاء جمعت عينة البول؟', // Container
                'options' => [
                    ['text' => 'قارورة معقمة من المخبر أو الصيدلية', 'value' => 'STERILE', 'action' => 'none'],
                    ['text' => 'قارورة ماء عادية', 'value' => 'NON_STERILE_WATER', 'action' => 'block'],
                    ['text' => 'كوب منزلي', 'value' => 'HOUSEHOLD_CUP', 'action' => 'block'],
                ]
            ],
            [
                'question' => 'هل قمت بتنظيف المنطقة التناسلية قبل جمع البول؟', // Cleaning
                'options' => [
                    ['text' => 'نعم بالماء والصابون', 'value' => 'YES_SOAP', 'action' => 'none'],
                    ['text' => 'نعم بالماء فقط', 'value' => 'YES_WATER', 'action' => 'warning'],
                    ['text' => 'لا', 'value' => 'NO', 'action' => 'warning'],
                ]
            ],
            [
                'question' => 'كيف جمعت البول؟', // Midstream
                'options' => [
                    ['text' => 'من منتصف التبول', 'value' => 'MIDSTREAM', 'action' => 'none'],
                    ['text' => 'من بداية التبول', 'value' => 'FIRST_STREAM', 'action' => 'warning'],
                    ['text' => 'من نهاية التبول', 'value' => 'END_STREAM', 'action' => 'warning'],
                ]
            ],
            [
                'question' => 'متى جمعت العينة؟', // Time
                'options' => [
                    ['text' => 'منذ أقل من ساعتين', 'value' => 'LESS_2H', 'action' => 'none'],
                    ['text' => 'منذ 3–4 ساعات', 'value' => '3_4H', 'action' => 'warning'],
                    ['text' => 'منذ أكثر من 6 ساعات', 'value' => 'MORE_6H', 'action' => 'block'],
                ]
            ],
            [
                'question' => 'كيف حفظت العينة قبل إحضارها؟', // Storage
                'options' => [
                    ['text' => 'في الثلاجة', 'value' => 'REFRIGERATOR', 'action' => 'none'],
                    ['text' => 'في درجة حرارة الغرفة', 'value' => 'ROOM_TEMP', 'action' => 'warning'],
                    ['text' => 'في السيارة', 'value' => 'CAR', 'action' => 'block'],
                ]
            ],
            [
                'question' => 'هل تتناول مضادات حيوية حاليًا؟', // Antibiotics
                'options' => [
                    ['text' => 'لا', 'value' => 'NO', 'action' => 'none'],
                    ['text' => 'نعم', 'value' => 'YES', 'action' => 'warning'],
                    ['text' => 'لا أعلم', 'value' => 'UNKNOWN', 'action' => 'warning'],
                ]
            ],
            [
                'question' => 'هل أنتِ في فترة الحيض؟', // Menstruation
                'gender_condition' => 'female',
                'options' => [
                    ['text' => 'لا', 'value' => 'NO', 'action' => 'none'],
                    ['text' => 'نعم', 'value' => 'YES', 'action' => 'block'],
                ]
            ],
            [
                'question' => 'هل عينة البول هذه هي أول بول صباحي؟', // First morning
                'options' => [
                    ['text' => 'نعم', 'value' => 'YES', 'action' => 'none'],
                    ['text' => 'لا', 'value' => 'NO', 'action' => 'warning'],
                ]
            ],
            [
                'question' => 'هل شربت كميات كبيرة من الماء قبل جمع العينة؟', // Water intake
                'options' => [
                    ['text' => 'لا (طبيعي)', 'value' => 'NO', 'action' => 'none'],
                    ['text' => 'نعم (بكثرة)', 'value' => 'YES', 'action' => 'warning'],
                ]
            ],
            [
                'question' => 'هل تعاني من حرقان أو ألم أثناء التبول؟', // Symptoms
                'options' => [
                    ['text' => 'نعم', 'value' => 'YES', 'action' => 'none'],
                    ['text' => 'لا', 'value' => 'NO', 'action' => 'none'],
                ]
            ],
            [
                'question' => 'هل تم جمع العينة في المنزل أم في المخبر؟', // Location
                'options' => [
                    ['text' => 'في المنزل', 'value' => 'HOME', 'action' => 'none'],
                    ['text' => 'في المخبر', 'value' => 'LAB', 'action' => 'none'],
                ]
            ],
        ];

        // 5. Insert questions, options, and rules
        foreach ($questions as $index => $qData) {
            $question = Question::create([
                'analyse_id' => $analyseId,
                'question' => $qData['question'],
                'type' => 'radio',
                'gender_condition' => $qData['gender_condition'] ?? null,
                'order' => $index + 1
            ]);

            foreach ($qData['options'] as $oData) {
                $option = Option::create([
                    'question_id' => $question->id,
                    'text' => $oData['text'],
                    'value' => $oData['value'],
                ]);

                if ($oData['action'] !== 'none') {
                    AnalysisRule::create([
                        'analysis_id' => $analyseId,
                        'question_id' => $question->id,
                        'disallowed_option_id' => $option->id,
                        'action' => $oData['action'],
                    ]);
                }
            }
        }
    }
}
