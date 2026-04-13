<div id="ai-assistant-wrapper" class="relative z-[9999]">

    {{-- Floating Action Button (FAB) --}}
    <button onclick="aiSidebarToggle()" type="button" title="Open AI Assistant" id="ai-fab-btn"
            style="position: fixed; bottom: 2rem; right: 2rem; z-index: 99999;"
            class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-full shadow-[0_10px_40px_-10px_rgba(99,102,241,0.8)] hover:scale-105 hover:shadow-[0_10px_40px_-5px_rgba(99,102,241,1)] transition-all focus:outline-none focus:ring-4 focus:ring-indigo-300 group ring-4 ring-white dark:ring-gray-900 border-2 border-indigo-400">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
    </button>

    {{-- Overlay --}}
    <div id="ai-sidebar-overlay" onclick="aiSidebarClose()" style="display: none; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[9990]"></div>

    {{-- Slide-over Panel --}}
    <div id="ai-sidebar-panel" style="display: none; transform: translateX(100%); transition: transform 0.3s ease-in-out;"
         class="fixed inset-y-0 right-0 max-w-sm w-full bg-white dark:bg-gray-900 shadow-2xl z-[9995] border-l border-gray-200 dark:border-gray-800 flex-col">
        
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gradient-to-r from-sky-50 dark:from-sky-900/20 to-indigo-50 dark:to-indigo-900/20">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                AI Assistant
            </h2>
            <button onclick="aiSidebarClose()" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            {{-- Context --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">0. Targeted Section / Page Context</label>
                <select id="ai-opt-page-context" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="Doctor Profile">👨‍⚕️ Doctor Profile</option>
                    <option value="Hospital Profile">🏥 Hospital Profile</option>
                    <option value="Ambulance Service">🚑 Ambulance Service</option>
                    <option value="Blog Post">📝 Blog Post</option>
                    <option value="SEO Landing Page">🚀 SEO Landing Page</option>
                    <option value="Page Builder">📄 Page Builder</option>
                    <option value="General Data / Other">⚙️ General Data / Other</option>
                </select>
            </div>

            {{-- Target Field --}}
            <div class="mt-4">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex justify-between">
                    <span>1. Target Field</span>
                    <span id="ai-field-count" class="text-[10px] bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded cursor-pointer hover:bg-indigo-200 transition-colors" onclick="aiSidebarScanFields()">↻ Rescan</span>
                </label>
                <div id="ai-target-fields-container" class="max-h-52 overflow-y-auto w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:outline-none space-y-1">
                    <div class="text-xs text-gray-500 text-center py-2">Scraping Page Fields...</div>
                </div>
            </div>

            {{-- Keywords / Instructions --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">2. Keywords / Instructions</label>
                <textarea id="ai-keywords" rows="3" placeholder="e.g. Heart Specialist, 10 years experience... Also mention compassionate care." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none resize-none"></textarea>
            </div>

            {{-- Model --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">3. Select Model</label>
                <select id="ai-model" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="gpt-4o-mini">GPT-4o Mini (Fast/Cheap)</option>
                    <option value="gpt-4o">GPT-4o (Smart)</option>
                    <option value="gemini">Gemini</option>
                    <option value="custom">Custom AI Settings Model</option>
                </select>
            </div>

            {{-- Advanced Settings Accordion --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden mt-2">
                <button type="button" onclick="toggleAIAccordion('settings-accordion')" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700/80 transition-colors">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">⚙️ Advanced Content Settings</span>
                    <svg id="settings-accordion-icon" class="w-4 h-4 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="settings-accordion" class="hidden p-4 bg-white dark:bg-gray-900 space-y-5 border-t border-gray-200 dark:border-gray-700">
                    
                    {{-- 1. Language & Tone --}}
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 border-b border-indigo-100 dark:border-indigo-900/50 pb-1">Language & Tone</label>
                        <div class="space-y-2">
                            <select id="ai-opt-lang" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-xs rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                <option value="English">Language: English</option>
                                <option value="Bangla">Language: Bangla</option>
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="ai-opt-tone" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-xs rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    <option value="Professional">Tone: Professional</option>
                                    <option value="Friendly">Tone: Friendly</option>
                                    <option value="Conversational">Tone: Conversational</option>
                                    <option value="Persuasive">Tone: Persuasive</option>
                                </select>
                                <select id="ai-opt-style" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-xs rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    <option value="Informative">Style: Informative</option>
                                    <option value="Simple">Style: Simple</option>
                                    <option value="Engaging">Style: Engaging</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Format & Readability --}}
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 border-b border-indigo-100 dark:border-indigo-900/50 pb-1">Format & Target</label>
                        <div class="space-y-2">
                            <select id="ai-opt-country" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-xs rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                <option value="Bangladesh">Target: Bangladesh</option>
                                <option value="Global">Target: Global</option>
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="ai-opt-length" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-[11px] rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    <option value="Short (~150 words)">Short (~150 words)</option>
                                    <option value="Medium (~300 words)" selected>Medium (~300 words)</option>
                                    <option value="Long (~600 words)">Long (~600 words)</option>
                                    <option value="In-Depth (~1000 words)">In-Depth (~1000 words)</option>
                                    <option value="Comprehensive (~2000 words)">Comprehensive (~2000 words)</option>
                                </select>
                                <select id="ai-opt-readability" class="w-full px-3 py-2 border-none ring-1 ring-gray-200 dark:ring-gray-700 text-xs rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                    <option value="Intermediate">Readability: Intermediate</option>
                                    <option value="Basic">Readability: Basic</option>
                                    <option value="Advanced">Readability: Advanced</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Content Structure --}}
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 border-b border-indigo-100 dark:border-indigo-900/50 pb-1">Content Structure</label>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-intro" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Intro Section</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-faq" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> FAQ Section</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-conclusion" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Conclusion</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-takeaways" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Key Takeaways</label>
                        </div>
                    </div>

                    {{-- 4. Media & Visuals --}}
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 border-b border-indigo-100 dark:border-indigo-900/50 pb-1">Media Suggestions</label>
                        <div class="space-y-1.5 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-feature-img" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Featured Image Prompt</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-yt" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> YouTube Video Embeds</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-google-img" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Google Images Search Terms</label>
                        </div>
                    </div>

                    {{-- 5. Research & Links --}}
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 border-b border-indigo-100 dark:border-indigo-900/50 pb-1">Research & Links</label>
                        <div class="space-y-1.5 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-internal-link" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> Internal Links (Docs/Hospitals/Spec)</label>
                            <label class="flex items-center gap-2 cursor-pointer text-[11px] font-medium text-gray-600 dark:text-gray-300"><input type="checkbox" id="ai-opt-external-link" class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-500 w-3 h-3"> External Authority References</label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Custom Prompts Settings Accordion --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden mt-2">
                <button type="button" onclick="toggleAIAccordion('prompts-accordion')" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 text-left flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-700/80 transition-colors">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">📝 Setup Prompt Templates</span>
                    <svg id="prompts-accordion-icon" class="w-4 h-4 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="prompts-accordion" class="hidden p-4 bg-white dark:bg-gray-900 space-y-4 border-t border-gray-200 dark:border-gray-700">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Doctor Profile Base</label>
                        <textarea id="ai-tmpl-doctor" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_doctor_bio', "Write an SEO-friendly, engaging biography for {name}. They are a {specialties} specialist. Their qualifications are: {qualifications}.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {name}, {specialties}, {qualifications}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Hospital Profile Base</label>
                        <textarea id="ai-tmpl-hospital" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_hospital_bio', "Write an SEO-friendly, engaging description for {name} located at {address}. Highlight that it provides top medical services and compassionate care.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {name}, {address}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Ambulance Base</label>
                        <textarea id="ai-tmpl-ambulance" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_ambulance_bio', "Write an SEO-friendly, engaging description for {name}, an {ambulanceType} service located at {address}. Highlight 24/7 availability and rapid response.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {name}, {ambulanceType}, {address}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Blog Post Base</label>
                        <textarea id="ai-tmpl-blog" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_blog_post', "Write an in-depth, highly informative, and SEO-optimized blog post about: '{topic}'. Include an introduction, structured body paragraphs with H2 and H3 headings, and a conclusion.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {topic}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Page Builder Base</label>
                        <textarea id="ai-tmpl-pagebuilder" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_page_builder', "Write highly engaging and informative website content for a custom page titled '{title}'. Structure it with compelling headings, informative paragraphs, and appropriate calls to action.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {title}, {keywords}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">SEO Landing Page Base</label>
                        <textarea id="ai-tmpl-seoland" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_seo_landing_page', "Write an engaging, highly SEO-optimized content block specifically for the '{section}' portion of a landing page targeting the keyword: '{keyword}'. The page title is '{title}'. Ensure it seamlessly matches the required context of that section.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {section}, {keyword}, {title}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-1">Generic / Auto-Fill Base</label>
                        <textarea id="ai-tmpl-generic" rows="2" class="w-full text-[11px] px-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-300 focus:outline-none resize-none">{{ \App\Models\Setting::get('ai_prompt_generic_field', "Write highly SEO-optimized content for a form field labeled '{field}' strictly for a '{pageContext}'. Context & Keywords: '{keywords}'.") }}</textarea>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-1">Vars: {field}, {pageContext}, {keywords}</p>
                    </div>

                    <button type="button" onclick="window.saveAIPrompts()" id="ai-save-prompts-btn" class="w-full py-2 bg-indigo-500 text-white rounded-lg text-xs font-bold hover:bg-indigo-600 transition-colors shadow-sm">
                        Save Prompt Templates
                    </button>
                    <div id="ai-prompts-msg" style="display:none;" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-medium border border-emerald-200 text-center"></div>

                </div>
            </div>

            {{-- Generate Button --}}
            <button onclick="aiSidebarGenerate()" id="ai-generate-btn" type="button" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="ai-loading-spinner" style="display:none;" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="ai-btn-text">Generate Content</span>
            </button>

            {{-- Error/Success Message --}}
            <div id="ai-error-msg" style="display:none;" class="p-3 bg-red-50 text-red-600 rounded-lg text-xs font-medium border border-red-200"></div>
            <div id="ai-success-msg" style="display:none;" class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-medium border border-emerald-200"></div>

            {{-- Result --}}
            <div id="ai-result-container" style="display:none;" class="pt-4 border-t border-gray-100 dark:border-gray-800">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">Generated Output:</label>
                <div class="mb-3">
                    <textarea id="ai-result" rows="12" class="w-full px-3 py-2 text-[13px] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-300 focus:outline-none resize-y"></textarea>
                </div>
                
                <div id="ai-normal-actions" class="flex gap-2 mt-3">
                    <button onclick="aiSidebarCopy()" type="button" class="flex-1 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                        Copy HTML
                    </button>
                    <button onclick="aiSidebarInsert()" id="ai-insert-btn" type="button" style="display:none;" class="flex-1 py-2 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition-colors shadow-sm">
                        Insert to Field
                    </button>
                </div>
                
                {{-- Mass Insert Actions (Dynamic) --}}
                <div id="ai-mass-actions" style="display:none;" class="mt-3">
                    <div class="text-[10px] font-bold text-gray-500 uppercase mb-2">Insert individual fields:</div>
                    <div id="ai-mass-buttons" class="flex flex-wrap gap-1.5 mb-2"></div>
                    <button onclick="aiSidebarInsertAll()" type="button" class="w-full py-2 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition-colors shadow-sm mt-1">
                        🚀 Insert ALL Generated Fields Automatically
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.toggleAIAccordion = function(id) {
    const el = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        el.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
};

window.aiSidebarState = {
    isOpen: false,
    pageFields: []
};

window.aiSidebarToggle = function() {
    if (window.aiSidebarState.isOpen) {
        window.aiSidebarClose();
    } else {
        window.aiSidebarOpen();
    }
};

window.aiSidebarOpen = function() {
    window.aiSidebarState.isOpen = true;
    let overlay = document.getElementById('ai-sidebar-overlay');
    let panel = document.getElementById('ai-sidebar-panel');
    
    overlay.style.display = 'block';
    panel.style.display = 'flex';
    
    void overlay.offsetWidth;
    
    overlay.style.opacity = '1';
    overlay.style.pointerEvents = 'auto';
    panel.style.transform = 'translateX(0)';

    try {
        window.aiSidebarScanFields();
        window.aiSidebarInitEditor();
    } catch (e) {
        console.error('aiSidebarScanFields Error: ', e);
    }
};

window.aiSidebarInitEditor = function() {
    if (typeof tinymce !== 'undefined' && !tinymce.get('ai-result')) {
        tinymce.init({
            selector: '#ai-result',
            height: 400,
            menubar: false,
            plugins: 'lists link image media preview code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code',
            statusbar: false,
            skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
            content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        });
    }
};

window.aiSidebarClose = function() {
    window.aiSidebarState.isOpen = false;
    let overlay = document.getElementById('ai-sidebar-overlay');
    let panel = document.getElementById('ai-sidebar-panel');
    
    overlay.style.opacity = '0';
    overlay.style.pointerEvents = 'none';
    panel.style.transform = 'translateX(100%)';

    setTimeout(function() {
        if (!window.aiSidebarState.isOpen) {
            overlay.style.display = 'none';
            panel.style.display = 'none';
        }
    }, 300);
};

document.addEventListener('open-ai-assistant', function() {
    window.aiSidebarOpen();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && window.aiSidebarState.isOpen) {
        window.aiSidebarClose();
    }
});

window.aiSidebarScanFields = function() {
    let fields = [];
    
    let inputs = document.querySelectorAll('textarea, input[type="text"]');
    
    inputs.forEach(el => {
        if(!el.id && !el.name) return;
        
        let idVal = el.id || '';
        let nameVal = el.name || '';
        
        // Skip irrelevant elements
        if(idVal === 'ai-result' || idVal.includes('search') || idVal.includes('ai-') || idVal.includes('og_image') || idVal.includes('banner') || idVal.includes('logo') || idVal.includes('image')) return;
        
        if (nameVal && !nameVal.startsWith('_') && nameVal.indexOf('[]') === -1) {
            let isTinyMce = typeof tinymce !== 'undefined' && idVal ? tinymce.get(idVal) : null;
            let selector = isTinyMce ? 'tinymce:' + idVal : (idVal ? '#' + CSS.escape(idVal) : '[name="' + CSS.escape(nameVal) + '"]');
            let labelText = nameVal;
            let labelEl = null;
            try { if (idVal) labelEl = document.querySelector('label[for="' + CSS.escape(idVal) + '"]'); } catch (e) {}
            if (!labelEl) { let div = el.closest('div'); if (div) labelEl = div.querySelector('label'); }
            if (labelEl && labelEl.innerText) labelText = labelEl.innerText.replace(/[*📞📅🚫🗺️]/g, '').trim() || nameVal;
            if(!fields.find(f => f.selector === selector)) fields.push({ label: labelText, selector: selector });
        }
    });

    window.aiSidebarState.pageFields = fields;
    let targetContainer = document.getElementById('ai-target-fields-container');
    if (!targetContainer) return;

    if (fields.length === 0) {
        targetContainer.innerHTML = '<div class="text-xs text-gray-500 text-center py-2">No AI-supported fields found on this page.</div>';
        return;
    }

    let html = `<label class="flex items-center gap-2 p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded cursor-pointer transition-colors border-b border-gray-200 dark:border-gray-700 pb-2 mb-2">
            <input type="checkbox" id="ai-select-all-fields" checked class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" onchange="document.querySelectorAll('.ai-field-checkbox').forEach(cb => cb.checked = this.checked)">
            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400">🚀 Select ALL Fields (${fields.length})</span>
        </label>`;
    fields.forEach(f => {
        let safeSelector = f.selector.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        let safeLabel = f.label.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        
        html += `<label class="flex items-center gap-2 p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded cursor-pointer transition-colors">
            <input type="checkbox" value="${safeSelector}" checked data-label="${safeLabel}" class="ai-field-checkbox w-3.5 h-3.5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">✍️ ${f.label}</span>
        </label>`;
    });
    targetContainer.innerHTML = html;
};

window.aiSidebarGenerate = async function() {
    const btn = document.getElementById('ai-generate-btn');
    const spinner = document.getElementById('ai-loading-spinner');
    const btnText = document.getElementById('ai-btn-text');
    const errorMsg = document.getElementById('ai-error-msg');
    const successMsg = document.getElementById('ai-success-msg');
    const resultBox = document.getElementById('ai-result');
    const resultContainer = document.getElementById('ai-result-container');
    const keywordsEl = document.getElementById('ai-keywords');
    const modelEl = document.getElementById('ai-model');

    const optLang = document.getElementById('ai-opt-lang')?.value || 'English';
    const optTone = document.getElementById('ai-opt-tone')?.value || 'Professional';
    const optStyle = document.getElementById('ai-opt-style')?.value || 'Informative';
    const optCountry = document.getElementById('ai-opt-country')?.value || 'Bangladesh';
    const optLength = document.getElementById('ai-opt-length')?.value || 'Medium (~300 words)';
    const optReadability = document.getElementById('ai-opt-readability')?.value || 'Intermediate';
    const optPageContext = document.getElementById('ai-opt-page-context')?.value || 'General SEO Setup';

    const checkedBoxes = Array.from(document.querySelectorAll('.ai-field-checkbox:checked'));
    const selectedSelectors = checkedBoxes.map(cb => cb.value);
    const isManualOutput = selectedSelectors.length === 0;
    const keywords = keywordsEl?.value || '';
    const modelName = modelEl?.value || '';
    const insertBtn = document.getElementById('ai-insert-btn');

    btn.disabled = true;
    spinner.style.display = 'inline-block';
    btnText.innerText = 'Generating...';
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';
    resultContainer.style.display = 'none';

    try {
        let csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const response = await fetch('/admin/ai/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfMeta ? csrfMeta.getAttribute('content') : ''
            },
            body: JSON.stringify({
                prompt_type: selectedSelectors.length > 1 ? 'generate_all_fields' : 'generic_field',
                context: {
                    page_context: optPageContext,
                    all_fields: checkedBoxes.map((cb, idx) => ({ id: 'field_' + idx, selector: cb.value, label: cb.getAttribute('data-label') })),
                    keywords: keywords,
                    language: optLang,
                    tone: optTone,
                    writing_style: optStyle,
                    country: optCountry,
                    length: optLength,
                    readability: optReadability
                },
                model_override: modelName
            })
        });

        if (!response.ok) {
            try { let errJson = await response.json(); throw new Error(errJson.message || 'Server error'); }
            catch(ee) { throw new Error('HTTP Error: ' + response.status); }
        }

        const data = await response.json();
        if (data.success) {
            document.getElementById('ai-normal-actions').style.display = 'none';
            document.getElementById('ai-mass-actions').style.display = 'none';

            if (selectedSelectors.length > 1 && typeof data.content === 'object') {
                window.aiSidebarState.lastGenData = {};
                let previewHtml = '';
                let buttonsHtml = '';
                
                let fieldMapping = checkedBoxes.map((cb, idx) => ({ id: 'field_' + idx, selector: cb.value, label: cb.getAttribute('data-label') }));
                
                for (let fMap of fieldMapping) {
                    if (data.content[fMap.id] !== undefined) {
                        let sel = fMap.selector;
                        let htmlContent = data.content[fMap.id];
                        let fieldLabel = fMap.label || 'Field';
                        
                        window.aiSidebarState.lastGenData[fMap.id] = {
                            selector: sel,
                            content: htmlContent,
                            isTiny: sel.startsWith('tinymce:')
                        };
                        
                        previewHtml += `<div style="margin-bottom:15px; border-bottom:1px solid #e5e7eb; padding-bottom:10px;">
                            <div style="font-weight:bold; font-size:12px; color:#4f46e5; margin-bottom:5px;">📋 ${fieldLabel}</div>
                            <div style="font-size:13px;">${htmlContent}</div>
                        </div>`;

                        buttonsHtml += `<button onclick="window.aiSidebarInsertSingle('${fMap.id}')" type="button" class="px-3 py-1.5 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200 text-left truncate flex-1 min-w-[120px]">
                            <span class="block font-bold">Insert</span>
                            <span class="block text-[9px] truncate text-indigo-500">${fieldLabel}</span>
                        </button>`;
                    }
                }
                
                if(typeof tinymce !== 'undefined' && tinymce.get('ai-result')) {
                    tinymce.get('ai-result').setContent(previewHtml);
                } else {
                    resultBox.value = previewHtml;
                }
                
                document.getElementById('ai-mass-buttons').innerHTML = buttonsHtml;
                document.getElementById('ai-mass-actions').style.display = 'block';
                resultContainer.style.display = 'block';

                btnText.innerText = 'Regenerated Successfully';
                window.aiSidebarShowSuccess('Preview ready for insertion!');
            } else {
                // Normal insert or AI failed to output valid JSON for Mass Generate
                let rawContent = (typeof data.content === 'object') ? JSON.stringify(data.content) : data.content;
                if(typeof tinymce !== 'undefined' && tinymce.get('ai-result')) {
                    tinymce.get('ai-result').setContent(rawContent);
                } else {
                    resultBox.value = rawContent;
                }
                resultContainer.style.display = 'block';
                document.getElementById('ai-normal-actions').style.display = 'flex';
                
                if (!isManualOutput && insertBtn) {
                    insertBtn.style.display = 'block';
                } else if (insertBtn) {
                    insertBtn.style.display = 'none';
                }
                
                // If it was supposed to be mass generation but didn't return json
                if (selectedSelectors.length > 1) {
                    window.aiSidebarShowError('AI generated text but it was not in strict format. You can copy it manually.');
                }
            }
        } else {
            errorMsg.innerText = data.message || 'Error generating content.';
            errorMsg.style.display = 'block';
        }
    } catch(e) {
        console.error(e);
        errorMsg.innerText = e.message && e.message !== 'Failed to fetch' ? e.message : 'Network error. Please try again.';
        errorMsg.style.display = 'block';
    } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
        btnText.innerText = 'Generate Content';
    }
};

window.aiSidebarCopy = function() {
    let text = (typeof tinymce !== 'undefined' && tinymce.get('ai-result')) ? tinymce.get('ai-result').getContent() : document.getElementById('ai-result').value;
    navigator.clipboard.writeText(text);
    window.aiSidebarShowSuccess('Copied HTML to clipboard!');
};

window.aiSidebarInsert = function() {
    const checkedBoxes = Array.from(document.querySelectorAll('.ai-field-checkbox:checked'));
    if (checkedBoxes.length === 0) {
        window.aiSidebarShowError('Please select at least one target field from the checkboxes.');
        return;
    }
    
    // We only insert manually into the first checked field, if multiple were checked, they should use mass generation.
    const selectedField = checkedBoxes[0].value;
    const result = (typeof tinymce !== 'undefined' && tinymce.get('ai-result')) ? tinymce.get('ai-result').getContent() : document.getElementById('ai-result').value;

    if (!result) return;
    
    if (selectedField.startsWith('tinymce:')) {
        let editorId = selectedField.split(':')[1];
        if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
            tinymce.get(editorId).setContent(result);
            window.aiSidebarShowSuccess('Inserted into text editor!');
        } else {
            document.getElementById('ai-error-msg').innerText = 'TinyMCE editor not found or not initialized yet.';
            document.getElementById('ai-error-msg').style.display = 'block';
        }
    } else {
        let target = document.querySelector(selectedField);
        if (target) {
            target.value = result;
            target.dispatchEvent(new Event('input', { bubbles: true }));
            target.dispatchEvent(new Event('change', { bubbles: true }));
            window.aiSidebarShowSuccess('Inserted to field successfully!');
        } else {
            document.getElementById('ai-error-msg').innerText = 'Target field not found in DOM.';
            document.getElementById('ai-error-msg').style.display = 'block';
        }
    }
};
window.aiSidebarInsertSingle = function(fId) {
    if (!window.aiSidebarState.lastGenData || !window.aiSidebarState.lastGenData[fId]) return;
    let dataObj = window.aiSidebarState.lastGenData[fId];
    let htmlContent = dataObj.content;
    let sel = dataObj.selector;
    let isTiny = dataObj.isTiny;
    let actualSel = isTiny ? sel.split(':')[1] : sel;
    
    if (isTiny) {
        try { tinymce.get(actualSel).setContent(htmlContent); window.aiSidebarShowSuccess('Inserted field successfully!'); }
        catch(e) { window.aiSidebarShowError('TinyMCE not found for this field.'); }
    } else {
        let fieldEl = document.querySelector(actualSel);
        if (fieldEl) { 
            fieldEl.value = htmlContent; 
            fieldEl.dispatchEvent(new Event('input', { bubbles: true }));
            fieldEl.dispatchEvent(new Event('change', { bubbles: true }));
            window.aiSidebarShowSuccess('Inserted into field successfully!'); 
        } else { window.aiSidebarShowError('Field not found in DOM.'); }
    }
};

window.aiSidebarInsertAll = function() {
    if (!window.aiSidebarState.lastGenData) return;
    let successCount = 0;
    for (let fId in window.aiSidebarState.lastGenData) {
        let dataObj = window.aiSidebarState.lastGenData[fId];
        let htmlContent = dataObj.content;
        let sel = dataObj.selector;
        let isTiny = dataObj.isTiny;
        let actualSel = isTiny ? sel.split(':')[1] : sel;
        
        if (isTiny) {
            try { tinymce.get(actualSel).setContent(htmlContent); successCount++; } catch(e){}
        } else {
            let fieldEl = document.querySelector(actualSel);
            if (fieldEl) { 
                fieldEl.value = htmlContent; 
                fieldEl.dispatchEvent(new Event('input', { bubbles: true }));
                fieldEl.dispatchEvent(new Event('change', { bubbles: true }));
                successCount++; 
            }
        }
    }
    window.aiSidebarShowSuccess(successCount + ' Fields Auto-filled!');
};

window.aiSidebarShowSuccess = function(msg) {
    const msgBox = document.getElementById('ai-success-msg');
    msgBox.innerText = msg;
    msgBox.style.display = 'block';
    
    setTimeout(() => {
        msgBox.style.display = 'none';
    }, 3000);
};
window.saveAIPrompts = async function() {
    const btn = document.getElementById('ai-save-prompts-btn');
    const msgBox = document.getElementById('ai-prompts-msg');
    
    btn.disabled = true;
    btn.innerText = "Saving...";
    msgBox.style.display = 'none';

    let payloads = {
        'doctor_bio': document.getElementById('ai-tmpl-doctor').value,
        'hospital_bio': document.getElementById('ai-tmpl-hospital').value,
        'ambulance_bio': document.getElementById('ai-tmpl-ambulance').value,
        'blog_post': document.getElementById('ai-tmpl-blog').value,
        'page_builder': document.getElementById('ai-tmpl-pagebuilder').value,
        'seo_landing_page': document.getElementById('ai-tmpl-seoland').value,
        'generic_field': document.getElementById('ai-tmpl-generic').value
    };

    try {
        let csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const response = await fetch('/admin/ai/save-prompts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfMeta ? csrfMeta.getAttribute('content') : ''
            },
            body: JSON.stringify({ prompts: payloads })
        });

        const data = await response.json();
        
        if (data.success) {
            msgBox.innerText = 'Templates saved successfully!';
            msgBox.className = "p-2 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-medium border border-emerald-200 text-center";
            msgBox.style.display = 'block';
        } else {
            msgBox.innerText = data.message || 'Error saving templates.';
            msgBox.className = "p-2 bg-red-50 text-red-600 rounded-lg text-xs font-medium border border-red-200 text-center";
            msgBox.style.display = 'block';
        }
    } catch(e) {
        msgBox.innerText = 'Connection error.';
        msgBox.className = "p-2 bg-red-50 text-red-600 rounded-lg text-xs font-medium border border-red-200 text-center";
        msgBox.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerText = "Save Prompt Templates";
        setTimeout(() => { if(msgBox) msgBox.style.display = 'none'; }, 2000);
    }
};
</script>
