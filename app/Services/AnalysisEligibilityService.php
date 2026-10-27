<?php

namespace App\Services;

use App\Models\AnalysisRule;
use App\Models\PatientAnswer;

class AnalysisEligibilityService
{
    /**
     * Check if a patient is eligible for a specific analysis.
     *
     * @param int $patient_id
     * @param int $analysis_id
     * @return array{status:string,notes:array,reason:string|null}
     */
    public function checkEligibility($patient_id, $analysis_id)
    {
        $rules = AnalysisRule::with(['question', 'option'])
            ->where('analysis_id', $analysis_id)
            ->get();

        $patientAnswers = PatientAnswer::where('patient_id', $patient_id)
            ->get()
            ->groupBy('question_id');

        $status = 'eligible';
        $notes = [];

        /** @var AnalysisRule $rule */
        foreach ($rules as $rule) {
            $answers = $patientAnswers->get($rule->question_id);
            
            if ($answers) {
                $hasViolated = false;
                foreach ($answers as $answer) {
                    if ($answer->option_id == $rule->disallowed_option_id) {
                        $hasViolated = true;
                        break;
                    }
                }

                if ($hasViolated) {
                    // Update main status (block overrides anything, approval overrides warning/eligible)
                    if ($rule->action === 'block') {
                        $status = 'block';
                    } elseif ($status !== 'block' && $rule->action === 'approval') {
                        $status = 'approval';
                    } elseif ($status !== 'block' && $status !== 'approval' && $rule->action === 'warning') {
                        $status = 'warning';
                    }

                    // Add note if provided in rule or generate from question
                    $questionText = $rule->question?->question ?? "Question #{$rule->question_id}";
                    $optionText = $rule->option?->text ?? "Option #{$rule->disallowed_option_id}";
                    $notes[] = "$questionText: $optionText";
                }
            }
        }

        $reason = null;
        if (!empty($notes)) {
            // Join all notes into a single human‑readable reason string
            $reason = implode(' | ', $notes);
        }

        return [
            'status' => $status,
            'notes' => $notes,
            'reason' => $reason,
        ];
    }
}
