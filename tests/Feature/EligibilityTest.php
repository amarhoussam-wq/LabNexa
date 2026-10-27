<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\Option;
use App\Models\Patient;
use App\Models\PatientAnswer;
use App\Models\Question;
use App\Models\AnalysisRule;
use App\Services\AnalysisEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_is_eligible_when_no_rules_exist()
    {
        $patient = Patient::factory()->create();
        $analyse = Analyse::factory()->create();

        $service = new AnalysisEligibilityService();
        $result = $service->checkEligibility($patient->id, $analyse->id);

        $this->assertEquals('eligible', $result['status']);
        $this->assertEmpty($result['notes']);
    }

    public function test_patient_is_blocked_when_violating_block_rule()
    {
        $patient = Patient::factory()->create();
        $analyse = Analyse::factory()->create();

        $question = Question::create(['analyse_id' => $analyse->id, 'question' => 'Are you fasting?', 'type' => 'radio']);
        $optionYes = Option::create(['question_id' => $question->id, 'text' => 'Yes', 'value' => 'YES']);
        $optionNo = Option::create(['question_id' => $question->id, 'text' => 'No', 'value' => 'NO']);

        // Rule: If option NO is selected, BLOCK
        AnalysisRule::create([
            'analysis_id' => $analyse->id,
            'question_id' => $question->id,
            'disallowed_option_id' => $optionNo->id,
            'action' => 'block'
        ]);

        // Patient answered NO
        PatientAnswer::create([
            'patient_id' => $patient->id,
            'question_id' => $question->id,
            'option_id' => $optionNo->id,
        ]);

        $service = new AnalysisEligibilityService();
        $result = $service->checkEligibility($patient->id, $analyse->id);

        $this->assertEquals('block', $result['status']);
        $this->assertStringContainsString('Are you fasting?', $result['notes'][0]);
        $this->assertStringContainsString('No', $result['notes'][0]);
    }

    public function test_patient_receives_warning_when_violating_warning_rule()
    {
        $patient = Patient::factory()->create();
        $analyse = Analyse::factory()->create();

        $question = Question::create(['analyse_id' => $analyse->id, 'question' => 'Taking medication?', 'type' => 'radio']);
        $optionYes = Option::create(['question_id' => $question->id, 'text' => 'Yes', 'value' => 'YES']);

        // Rule: If option YES is selected, WARNING
        AnalysisRule::create([
            'analysis_id' => $analyse->id,
            'question_id' => $question->id,
            'disallowed_option_id' => $optionYes->id,
            'action' => 'warning'
        ]);

        // Patient answered YES
        PatientAnswer::create([
            'patient_id' => $patient->id,
            'question_id' => $question->id,
            'option_id' => $optionYes->id,
        ]);

        $service = new AnalysisEligibilityService();
        $result = $service->checkEligibility($patient->id, $analyse->id);

        $this->assertEquals('warning', $result['status']);
    }
}
