@php
    $isBangla = app()->getLocale() === 'bn';
    $hasTranslation = !empty($seoPage->getTranslation('content_top', 'bn', false)) || !empty($seoPage->getTranslation('content_bottom', 'bn', false));
    $showTranslationWarning = $isBangla && !$hasTranslation;
@endphp

@extends('layouts.app', ['noindex_page' => $showTranslationWarning, 'has_bn_translation' => $hasTranslation])

@section('title', $seoPage->meta_title ?? $seoPage->title)
@section('meta_description', $seoPage->meta_description)

@section('content')
<div class="bg-violet-50 dark:bg-gray-800/50 py-10 border-b border-violet-100 dark:border-gray-800 min-h-screen relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($showTranslationWarning)
        <div class="mb-6 p-4 rounded-xl border border-sky-100 dark:border-sky-800/50 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-800/50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-1">অসম্পূর্ণ অনুবাদ (Incomplete Translation)</h4>
                <p class="text-xs opacity-90 leading-relaxed">এই পেজটির বাংলা অনুবাদ এখনো আপডেট করা হয়নি। আপনার সুবিধার্থে আপাতত ইংরেজি ভার্সনটি দেখানো হচ্ছে। খুব শীঘ্রই বাংলা কন্টেন্ট যুক্ত করা হবে।</p>
            </div>
        </div>
        @endif

        <!-- Interactive Directory List -->
        <div class="my-6">
            @if($seoPage->type === 'doctor')
                <livewire:doctor-list 
                    :specialty="$seoPage->specialty->slug ?? ''" 
                    :division="$seoPage->division->slug ?? ''" 
                    :district="$seoPage->district->slug ?? ''" 
                    :area="$seoPage->area->slug ?? ''" 
                    :hideFilters="true"
                    :seoTitle="$seoPage->title"
                    :seoTopContent="$seoPage->content_top"
                    :seoBottomContent="$seoPage->content_bottom"
                />
            @elseif($seoPage->type === 'hospital')
                <livewire:hospital-list 
                    :division="$seoPage->division->slug ?? ''" 
                    :district="$seoPage->district->slug ?? ''" 
                    :area="$seoPage->area->slug ?? ''" 
                    :hideFilters="true"
                    :seoTitle="$seoPage->title"
                    :seoTopContent="$seoPage->content_top"
                    :seoBottomContent="$seoPage->content_bottom"
                />
            @elseif($seoPage->type === 'ambulance')
                <livewire:ambulance-list 
                    :division="$seoPage->division->slug ?? ''" 
                    :district="$seoPage->district->slug ?? ''" 
                    :area="$seoPage->area->slug ?? ''" 
                    :hideFilters="true"
                    :seoTitle="$seoPage->title"
                    :seoTopContent="$seoPage->content_top"
                    :seoBottomContent="$seoPage->content_bottom"
                />
            @endif
        </div>

    </div>
</div>

<!-- Inject JSON-LD Schema if Present -->
@if(!empty($seoPage->faq_schema))
    @push('scripts')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            @foreach($seoPage->faq_schema as $index => $faq)
            {
                "@type": "Question",
                "name": "{!! addslashes($faq['question'] ?? '') !!}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{!! addslashes($faq['answer'] ?? '') !!}"
                }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }
    </script>
    @endpush
@endif
@endsection
