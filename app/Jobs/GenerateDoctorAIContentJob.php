<?php

namespace App\Jobs;

use App\Models\Doctor;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateDoctorAIContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $doctor;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Doctor $doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $provider = Setting::get('ai_provider', 'openai');
        
        // 1. Generate Bio if empty
        if (empty($this->doctor->bio)) {
            $specialties = $this->doctor->specialties->pluck('name')->join(', ');
            $bioPrompt = "Write an SEO-friendly, engaging English biography (around 150-200 words) for {$this->doctor->name}. They are a {$specialties} specialist. Their qualifications are: {$this->doctor->qualifications}. Tone: Professional and trustworthy. Format it with proper HTML tags (e.g., <p>, <strong>). Output raw HTML. DO NOT wrap in ```html block.";
            
            try {
                $bio = $this->generateContent($provider, $bioPrompt);
                $this->doctor->bio = $bio;
            } catch (\Exception $e) {
                Log::error("Failed to generate Bio for Doctor ID {$this->doctor->id}: " . $e->getMessage());
            }
        }

        // 2. Generate SEO Meta Description if empty
        $seo = $this->doctor->seoMeta;
        if (!$seo) {
            $seo = $this->doctor->seoMeta()->create(['title' => $this->doctor->name]);
        }

        if (empty($seo->description)) {
            $contentSource = strip_tags($this->doctor->bio ?? $this->doctor->name . ' ' . $this->doctor->qualifications);
            $seoPrompt = "Generate a compelling SEO Meta Description (between 120 and 160 characters) based on the following doctor profile: '{$contentSource}'. Incorporate relevant keywords naturally. Return ONLY the description text, no quotes.";
            
            try {
                $desc = $this->generateContent($provider, $seoPrompt);
                $seo->description = $desc;
                $seo->save();
            } catch (\Exception $e) {
                Log::error("Failed to generate SEO for Doctor ID {$this->doctor->id}: " . $e->getMessage());
            }
        }

        // Save if bio was updated
        if ($this->doctor->isDirty('bio')) {
            $this->doctor->save();
        }
    }

    private function generateContent($provider, $prompt)
    {
        if ($provider === 'openai') {
            $apiKey = Setting::get('openai_api_key');
            if (!$apiKey) throw new \Exception("OpenAI API key missing");

            $response = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert medical copywriter and SEO specialist.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
            ]);

            if ($response->failed()) throw new \Exception("OpenAI error: " . $response->body());
            return trim(preg_replace('/^```html\s*([\s\S]*?)\s*```$/m', '$1', $response->json('choices.0.message.content', '')));
        } else {
            $apiKey = Setting::get('gemini_api_key');
            if (!$apiKey) throw new \Exception("Gemini API key missing");

            $response = Http::timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    ['parts' => [['text' => "System: You are an expert medical copywriter and SEO specialist.\n\nUser: " . $prompt]]]
                ],
                'generationConfig' => ['temperature' => 0.7]
            ]);

            if ($response->failed()) throw new \Exception("Gemini error: " . $response->body());
            return trim(preg_replace('/^```html\s*([\s\S]*?)\s*```$/m', '$1', $response->json('candidates.0.content.parts.0.text', '')));
        }
    }
}
