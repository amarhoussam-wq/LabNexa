<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Question;
use App\Models\Option;
use App\Models\AnalysisRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EligibilityController extends Controller
{
    /**
     * Show the management page for eligibility.
     */
    public function manage($analysis_id)
    {
        $analysis = Analyse::with(['questions.options', 'analysisRules'])->findOrFail($analysis_id);
        
        // Map rules to options for easier display
        $rules = $analysis->analysisRules->groupBy('disallowed_option_id');

        return view('Adminstration.manage-eligibility', compact('analysis', 'rules'));
    }

    /**
     * Store a new question for an analysis.
     */
    public function storeQuestion(Request $request, $analysis_id)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        Question::create([
            'question' => $request->question,
            'analyse_id' => $analysis_id,
            'type' => 'radio', // Default to radio for simple eligibility
            'order' => Question::where('analyse_id', $analysis_id)->count() + 1,
        ]);

        return redirect()->back()->with('success', __('messages.save_success') ?? 'Saved successfully');
    }

    /**
     * Remove a question and its associated options/rules.
     */
    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);
        
        DB::transaction(function () use ($question) {
            $question->delete();
        });

        return redirect()->back()->with('success', __('messages.delete_success') ?? 'Deleted successfully');
    }

    /**
     * Update an existing question.
     */
    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $question = Question::findOrFail($id);
        $question->update([
            'question' => $request->question,
        ]);

        return redirect()->back()->with('success', __('messages.update_success') ?? 'Updated successfully');
    }

    /**
     * Store a new option and potentially an associated rule.
     */
    public function storeOption(Request $request, $question_id)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'action' => 'required|in:none,warning,block,approval',
        ]);

        $question = Question::findOrFail($question_id);

        DB::transaction(function () use ($request, $question) {
            $option = Option::create([
                'question_id' => $question->id,
                'text' => $request->text,
                'value' => strtolower(str_replace(' ', '_', $request->text)),
            ]);

            if ($request->action !== 'none') {
                AnalysisRule::create([
                    'analysis_id' => $question->analyse_id,
                    'question_id' => $question->id,
                    'disallowed_option_id' => $option->id,
                    'action' => $request->action,
                ]);
            }
        });

        return redirect()->back()->with('success', __('messages.save_success') ?? 'Saved successfully');
    }

    /**
     * Update an existing option and its associated rule.
     */
    public function updateOption(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'action' => 'required|in:none,warning,block,approval',
        ]);

        $option = Option::findOrFail($id);

        DB::transaction(function () use ($request, $option) {
            $option->update([
                'text' => $request->text,
                'value' => strtolower(str_replace(' ', '_', $request->text)),
            ]);

            // Update or create rule
            if ($request->action === 'none') {
                AnalysisRule::where('disallowed_option_id', $option->id)->delete();
            } else {
                // Defensive check: ensure the option belongs to a question
                if (!$option->question) {
                    throw new \Exception("Option does not have an associated question.");
                }

                AnalysisRule::updateOrCreate(
                    ['disallowed_option_id' => $option->id],
                    [
                        'analysis_id' => $option->question->analyse_id,
                        'question_id' => $option->question_id,
                        'action' => $request->action,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', __('messages.update_success') ?? 'Updated successfully');
    }

    /**
     * Remove an option and its associated rule.
     */
    public function destroyOption($id)
    {
        $option = Option::findOrFail($id);
        
        DB::transaction(function () use ($option) {
            AnalysisRule::where('disallowed_option_id', $option->id)->delete();
            $option->delete();
        });

        return redirect()->back()->with('success', __('messages.delete_success') ?? 'Deleted successfully');
    }
}
