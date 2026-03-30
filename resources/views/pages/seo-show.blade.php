@extends('layouts.app')
@section('title', $seoPage->meta_title ?? $seoPage->title)
@section('meta_description', $seoPage->meta_description)

@section('content')
<div class="bg-violet-50 dark:bg-gray-800/50 py-10 border-b border-violet-100 dark:border-gray-800 min-h-screen relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
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
