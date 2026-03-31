<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt_type' => 'required|string', 
            // 'doctor_bio', 'hospital_bio', 'ambulance_bio', 'seo_title', 'seo_desc', 'blog_post'
            'context' => 'required|array', 
        ]);

        $prompt = $this->buildPrompt($request->prompt_type, $request->context);

        if (!$prompt) {
            return response()->json(['success' => false, 'message' => 'Invalid prompt type.'], 400);
        }

        $provider = Setting::get('ai_provider', 'openai');
        $modelOverride = $request->input('model_override');

        try {
            if ($modelOverride === 'gemini') {
                $content = $this->callGemini($prompt);
            } elseif ($modelOverride === 'custom') {
                $content = $this->callOpenAI($prompt, 'custom_openai');
            } elseif ($modelOverride && str_starts_with($modelOverride, 'gpt')) {
                $content = $this->callOpenAI($prompt, 'openai', $modelOverride);
            } else {
                // Fallback to default configured
                if ($provider === 'openai' || $provider === 'custom_openai') {
                    $content = $this->callOpenAI($prompt, $provider);
                } else {
                    $content = $this->callGemini($prompt);
                }
            }

            // Cleanup potential markdown formatting from AI output
            if ($request->prompt_type === 'seo_faq_schema' || $request->prompt_type === 'generate_all_fields') {
                $content = preg_replace('/^```(?:json|html)?\s*([\s\S]*?)\s*```$/im', '$1', $content);
                $content = trim($content);
            } elseif ($request->prompt_type !== 'seo_title' && $request->prompt_type !== 'seo_desc' && $request->prompt_type !== 'generic_field') {
                $content = preg_replace('/^```(?:html|json)?\s*([\s\S]*?)\s*```$/im', '$1', $content);
                $content = trim($content);
            }

            $isJson = ($request->prompt_type === 'generate_all_fields');
            $finalContent = $content;
            
            if ($isJson) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $finalContent = $decoded;
                }
            }

            return response()->json([
                'success' => true,
                'is_json' => $isJson,
                'content' => $finalContent
            ]);

        } catch (\Exception $e) {
            Log::error('AI Generator Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }

    public function savePrompts(Request $request)
    {
        $request->validate([
            'prompts' => 'required|array'
        ]);

        foreach ($request->prompts as $key => $value) {
            Setting::set('ai_prompt_' . $key, $value);
        }

        return response()->json(['success' => true, 'message' => 'Prompt templates saved successfully.']);
    }

    private function buildPrompt($type, $context)
    {
        $lang = $context['language'] ?? 'English';
        $tone = $context['tone'] ?? 'Professional';
        $style = $context['writing_style'] ?? 'Informative';
        $country = $context['country'] ?? 'Bangladesh';
        $length = $context['length'] ?? 'Medium';
        $readability = $context['readability'] ?? 'Intermediate';

        $incIntro = filter_var($context['include_intro'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incFaq = filter_var($context['include_faq'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incConcl = filter_var($context['include_conclusion'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incTakeaways = filter_var($context['include_takeaways'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incFeatImg = filter_var($context['include_feature_img'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incYt = filter_var($context['include_yt_video'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incGoogle = filter_var($context['include_google_images'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incIntLinks = filter_var($context['include_internal_links'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $incExtLinks = filter_var($context['include_external_links'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Define default base prompts
        $defaults = [
            'doctor_bio' => "Write an SEO-friendly, engaging biography for {name}. They are a {specialties} specialist. Their qualifications are: {qualifications}.",
            'hospital_bio' => "Write an SEO-friendly, engaging description for {name} located at {address}. Highlight that it provides top medical services and compassionate care.",
            'ambulance_bio' => "Write an SEO-friendly, engaging description for {name}, an {ambulanceType} service located at {address}. Highlight 24/7 availability and rapid response.",
            'blog_post' => "Write an in-depth, highly informative, and SEO-optimized blog post about: '{topic}'. Include an introduction, structured body paragraphs with H2 and H3 headings, and a conclusion.",
            'page_builder' => "Write highly engaging and informative website content for a custom page titled '{title}'. Structure it with compelling headings, informative paragraphs, and appropriate calls to action.",
            'seo_title' => "Generate a highly-clickable SEO Meta Title (maximum 60 characters) based on the following text/topic: '{content}'. Return ONLY the title text, no quotes or additional text. Just the raw text.",
            'seo_desc' => "Generate a compelling SEO Meta Description (between 120 and 160 characters) based on the following text: '{content}'. Incorporate relevant keywords naturally to boost Google rankings. Return ONLY the description text, no quotes, no HTML tags. Just the raw text.",
            'seo_landing_page' => "Write an engaging, highly SEO-optimized content block specifically for the '{section}' portion of a landing page targeting the keyword: '{keyword}'. The page title is '{title}'. Ensure it seamlessly matches the required context of that section.",
            'seo_faq_schema' => "Generate a strict JSON-LD FAQ Schema for the keyword/topic: '{keyword}'. Provide 3-5 relevant and highly-searched questions and answers. Return ONLY valid JSON array containing objects with 'question' and 'answer' keys, e.g. [{\"question\":\"...\",\"answer\":\"...\"}]. DO NOT return any markdown formatting, no ```json, strictly parsable JSON array.",
            'generic_field' => "Write highly SEO-optimized content for a form field labeled '{field}' strictly for a '{pageContext}'. Context & Keywords: '{keywords}'."
        ];

        if ($type === 'generate_all_fields') {
            $pageContext = $context['page_context'] ?? 'General Data';
            $allFields = $context['all_fields'] ?? [];
            
            $fieldsListStr = "";
            foreach($allFields as $f) {
                $fId = $f['id'] ?? '';
                $label = $f['label'] ?? '';
                $fieldsListStr .= "- Field ID: `{$fId}` | Field Label: `{$label}`\n";
            }

            $promptText = "You are an automated medical copywriter and SEO assistant. You must generate contextually perfect content for multiple fields of a '{$pageContext}'. ";
            if (!empty($context['keywords'])) {
                $promptText .= "Context & Keywords: '{$context['keywords']}'. ";
            }
            
            // Advanced Settings summary
            $promptText .= "Settings: Language: {$lang}, Tone: {$tone}, Target Country: {$country}, Target Length (For Body Fields): {$length}. Readability: {$readability}. ";
            
            $promptText .= "You are provided with a list of target form fields (Field ID and Field Label). You must generate the appropriate text/HTML for EACH field based on its Label.\n\n";
            $promptText .= "Fields to fill:\n" . $fieldsListStr . "\n\n";
            
            $promptText .= "Rules:\n";
            $promptText .= "1. For large text fields ('Body', 'Description', 'Details'), generate highly rich, lengthy HTML content utilizing proper tags (<h2>, <p>). Also insert Medical Internal Links (`<a href=\"/search?q=specialty\">Text</a>`), External Authority Links (`<a target=\"_blank\" href=\"https://www.who.int/\">WHO</a>`), and if requested, Media embeds.\n";
            $promptText .= "2. For 'Title' or 'Name', generate a short plain-text string without HTML.\n";
            $promptText .= "3. For 'Slug', generate a valid url-friendly-slug.\n";
            $promptText .= "4. For 'SEO' fields, adhere to standard SEO character limits.\n\n";
            
            $promptText .= "ABSOLUTE REQUIREMENT: Your final response MUST be a STRICT JSON OBJECT. The JSON keys MUST be exactly the 'Field ID' provided above, and the values should be the generated content string. Example: { \"field_0\": \"Doc Name\", \"field_1\": \"<p>description...</p>\" }.\n";
            $promptText .= "DO NOT wrap the response in markdown ```json blocks. Return ONLY valid parsed JSON.";

            return $promptText;
        }

        // Override $type if generic_field is requested but the context maps specifically to a built-in base template.
        if ($type === 'generic_field') {
            $pageContext = $context['page_context'] ?? '';
            $label = strtolower($context['field_label'] ?? '');
            
            if ($pageContext === 'Doctor Profile' && (str_contains($label, 'bio') || str_contains($label, 'body') || str_contains($label, 'description') || str_contains($label, 'education'))) {
                $type = 'doctor_bio';
            } elseif ($pageContext === 'Hospital Profile' && (str_contains($label, 'about') || str_contains($label, 'body') || str_contains($label, 'description'))) {
                $type = 'hospital_bio';
            } elseif ($pageContext === 'Ambulance Service' && (str_contains($label, 'about') || str_contains($label, 'body') || str_contains($label, 'description'))) {
                $type = 'ambulance_bio';
            } elseif ($pageContext === 'Blog Post' && (str_contains($label, 'content') || str_contains($label, 'body'))) {
                $type = 'blog_post';
            } elseif ($pageContext === 'SEO Landing Page') {
                $type = 'seo_landing_page';
            } elseif ($pageContext === 'Page Builder' && (str_contains($label, 'content') || str_contains($label, 'body'))) {
                $type = 'page_builder';
            }
        }

        // Fetch user-defined DB template or fallback to default
        $baseText = Setting::get('ai_prompt_' . $type, $defaults[$type] ?? '');
        if (!$baseText) {
            return null; // Invalid prompt type
        }

        // Determine Section for Single SEO prompt
        $sectionDesc = 'General Content';
        $label = strtolower($context['field_label'] ?? '');
        if (str_contains($label, 'top') || str_contains($label, 'intro')) {
            $sectionDesc = 'Top Introduction Content (Hook the reader, introduce the topic)';
        } elseif (str_contains($label, 'bottom') || str_contains($label, 'conclusion') || str_contains($label, 'faq')) {
            $sectionDesc = 'Bottom Conclusion & FAQ Information (Summarize, answer questions)';
        }
        
        // Ensure inline generateAiContent calls correctly map back to the unified type
        if ($type === 'seo_page_content_top' || $type === 'seo_page_content_bottom') {
            $type = 'seo_landing_page';
            // update base text
            $baseText = Setting::get('ai_prompt_' . $type, $defaults[$type] ?? '');
        }

        // Replace Variables securely
        $replaceMap = [
            '{name}' => $context['name'] ?? '',
            '{specialties}' => $context['specialties'] ?? '',
            '{qualifications}' => $context['qualifications'] ?? '',
            '{experience_years}' => $context['experience_years'] ?? '',
            '{designation}' => $context['designation'] ?? '',
            '{services}' => $context['services'] ?? '',
            '{chambers}' => $context['chambers'] ?? '',
            '{districts}' => $context['districts'] ?? '',
            '{address}' => $context['address'] ?? '',
            '{ambulanceType}' => $context['ambulanceType'] ?? '',
            '{topic}' => $context['topic'] ?? '',
            '{keyword}' => $context['keyword'] ?? '',
            '{title}' => $context['title'] ?? '',
            '{content}' => $context['content'] ?? ($context['name'] ?? ''),
            '{field}' => $context['field_label'] ?? 'General',
            '{pageContext}' => $context['page_context'] ?? 'Page',
            '{keywords}' => $context['keywords'] ?? '',
            '{section}' => $sectionDesc
        ];

        foreach ($replaceMap as $key => $val) {
            $baseText = str_ireplace($key, $val, $baseText);
        }

        $promptText = $baseText . "\n\n";

                // Apply advanced settings strictly
                $promptText .= "Settings to STRICTLY follow: ";
                $promptText .= "- Language: {$lang}. ";
                $promptText .= "- Target Audience Country: {$country}. ";
                $promptText .= "- Tone: {$tone}. ";
                $promptText .= "- Writing Style: {$style}. ";
                $promptText .= "- Target Word Count: {$length}. You MUST write EXACTLY or very close to this requested length. If a large word count is requested, expand with rich information, details, and examples to meet the limit. DO NOT write a short response. ";
                $promptText .= "- Readability Level: {$readability}. ";

                // Content Structure
                $sections = [];
                if ($incIntro) $sections[] = "Introduction";
                if ($incFaq) $sections[] = "FAQ Section";
                if ($incConcl) $sections[] = "Conclusion";
                if ($incTakeaways) $sections[] = "Key Takeaways";
                if (!empty($sections)) {
                    $promptText .= "- Must Include Sections: " . implode(", ", $sections) . ". ";
                }

                // Research & Links
                if ($incIntLinks) {
                    $promptText .= "- Internal Links: Include 2-3 internal hyperlink anchor texts pointing to relevant theoretical local pages (e.g., `<a href=\"/search?q=specialty\">Related Specialty</a>` or `<a href=\"/doctors\">Our Doctors</a>`). Use proper anchor text seamlessly in the content. ";
                }
                if ($incExtLinks) {
                    $promptText .= "- External Links: Reference highly trusted external Health Authority sources using anchor tags (e.g., `<a href=\"https://www.who.int\" target=\"_blank\">World Health Organization</a>`). Do NOT paste raw URLs as text, always use clean anchor text. ";
                }

                // Media & Visuals
                if ($incFeatImg || $incYt || $incGoogle) {
                    $promptText .= "- Rich Media Embeds (Place these beautifully inside the HTML content exactly as provided): ";
                    if ($incFeatImg || $incGoogle) {
                        $promptText .= "For highly relevant images, insert this exact block but replace the brackets with an accurate keyword: `<figure class=\"my-4\"><img src=\"https://image.pollinations.ai/prompt/[highly-detailed-keyword]?width=800&height=400&nologo=true\" alt=\"[keyword]\" style=\"width:100%; border-radius:10px;\"><figcaption style=\"text-align:center; font-size:12px; color:#666; margin-top:5px;\">Image Source: Google / AI</figcaption></figure>`. ";
                    }
                    if ($incYt) {
                        $promptText .= "For a relevant YouTube video, insert this exact iframe block but replace the bracketed keyword (use + for spaces): `<div style=\"margin: 20px 0;\"><iframe width=\"100%\" height=\"315\" src=\"https://www.youtube.com/embed?listType=search&list=[Optimized+Search+Keyword]\" frameborder=\"0\" allowfullscreen style=\"border-radius:10px;\"></iframe></div>`. ";
                    }
                }

                // Special overrides for structural responses
                if ($type === 'seo_title' || $type === 'seo_desc' || $type === 'seo_faq_schema') {
                    return $promptText; // these don't need formatting rules
                }

                $promptText .= "Formatting Rules: Format the entire output using proper rich HTML tags (<h2>, <h3>, <p>, <strong>, <ul>, <li>). DO NOT wrap the response inside ```html markdown blocks. Return ONLY the raw HTML output intended for a WYSIWYG editor. Avoid conversational bot introductions.";
                
                return $promptText;
    }

    private function callOpenAI($prompt, $provider = 'openai', $forceModel = null)
    {
        $apiKey = Setting::get('openai_api_key');
        if (!$apiKey) {
            throw new \Exception("OpenAI API key is missing. Please configure it in the AI Settings.");
        }

        $baseUrl = 'https://api.openai.com/v1';
        $modelName = $forceModel ? $forceModel : 'gpt-4o-mini';

        if ($provider === 'custom_openai') {
            $customUrl = Setting::get('openai_base_url');
            if (!empty($customUrl)) {
                $baseUrl = rtrim(preg_replace('#/chat/completions/?$#i', '', trim($customUrl)), '/');
            }
            
            if (!$forceModel) {
                $customModel = Setting::get('openai_model');
                if (!empty($customModel)) {
                    $modelName = $customModel;
                }
            }
        }

        $request = Http::withToken($apiKey)->timeout(60);
        if (app()->isLocal()) {
            $request = $request->withoutVerifying();
        }

        $response = $request->post($baseUrl . '/chat/completions', [
                'model' => $modelName,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert medical copywriter and SEO specialist.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            throw new \Exception("OpenAI API error: " . $response->body());
        }

        $data = $response->json();
        return trim($data['choices'][0]['message']['content'] ?? '');
    }

    private function callGemini($prompt)
    {
        $apiKey = Setting::get('gemini_api_key');
        if (!$apiKey) {
            throw new \Exception("Google Cloud Gemini API key is missing. Please configure it in the AI Settings.");
        }

        $request = Http::timeout(60);
        if (app()->isLocal()) {
            $request = $request->withoutVerifying();
        }

        $response = $request->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "System: You are an expert medical copywriter and SEO specialist.\n\nUser: " . $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception("Gemini API error: " . $response->body());
        }

        $data = $response->json();
        return trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
    }
}
