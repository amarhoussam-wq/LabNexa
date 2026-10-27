<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $apiUrlBase = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        // Deduplicate and support multiple delimiters
        $rawKeys = config('services.gemini.key') ?? env('GEMINI_API_KEY') ?? '';
        $this->apiKey = array_unique(array_filter(preg_split('/[,;| ]+/', $rawKeys)));
        
        $censoredKeys = array_map(function($key) {
            return substr($key, 0, 4) . '...' . substr($key, -4);
        }, $this->apiKey);

        Log::debug('AiService initialized', [
            'unique_keys' => count($this->apiKey),
            'detected' => $censoredKeys,
        ]);
    }

    /**
     * Generate clinical insight with automatic rotation and model fallback.
     */
    public function generateInsight(Reservation $reservation)
    {
        if (empty($this->apiKey) || (count($this->apiKey) === 1 && $this->apiKey[0] === 'DEMO')) {
             return "### 🤖 التحليل العيادي الذكي (وضع التجربة - Demo Mode)\n" .
                    "يجب إضافة مفتاح API حقيقي لتوليد نتائج واقعية.";
        }

        $reservation->load(['patient', 'reservationAnalyses.analyse']);
        $patient = $reservation->patient;
        
        // Minimalist Prompt
        $prompt = "Expert Lab Interpreter. CONCISE Bilingual (AR/FR). Markdown.
Patient: {$patient->name}, Age: {$patient->birth_date}, Results:\n";
        foreach ($reservation->reservationAnalyses as $ra) {
            $a = $ra->analyse;
            $prompt .= "- {$a->name}: {$ra->result_value} {$a->unit} ({$a->min_range}-{$a->max_range})\n";
        }

        // Try these models in order
        $models = ['gemini-2.0-flash', 'gemini-1.5-flash'];
        
        foreach ($models as $modelName) {
            foreach ($this->apiKey as $index => $key) {
                try {
                    $url = "{$this->apiUrlBase}{$modelName}:generateContent?key={$key}";
                    
                    $response = Http::timeout(30)->post($url, [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'تفسير غير متوفر.';
                    }

                    $status = $response->status();
                    if ($status == 429) {
                        Log::warning("Quota Exceeded for key {$index} on model {$modelName}. Trying next...");
                        usleep(500000); // 0.5s pause to respect IP limits
                        continue;
                    }

                    if ($status == 401 || $status == 403) continue;

                } catch (\Exception $e) {
                    Log::error("Key {$index} failed: " . $e->getMessage());
                    continue;
                }
            }
        }

        $count = count($this->apiKey);
        return "⚠️ تم استهلاك حصة (Quota) جميع المفاتيح الـ $count. \n\n**الحل:** يرجى الانتظار 60 ثانية حتى تقوم جوجل بإعادة تصفير العداد.";
    }
}
