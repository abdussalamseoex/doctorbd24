{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <!-- Diseases Landing Page (EN & BN) -->
    <url>
        <loc>{{ route('diseases.index') }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ route('diseases.index') }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ route('bn.diseases.index') }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('diseases.index') }}" />
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('bn.diseases.index') }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ route('diseases.index') }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ route('bn.diseases.index') }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('diseases.index') }}" />
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Symptom Checker (EN & BN) -->
    <url>
        <loc>{{ route('symptom-checker.index') }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ route('symptom-checker.index') }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ route('bn.symptom-checker.index') }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('symptom-checker.index') }}" />
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    <url>
        <loc>{{ route('bn.symptom-checker.index') }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ route('symptom-checker.index') }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ route('bn.symptom-checker.index') }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('symptom-checker.index') }}" />
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>

    <!-- Individual Disease Guides -->
    @foreach($diseases as $disease)
    @php
        $enUrl = route('diseases.show', $disease->slug);
        $bnUrl = route('bn.diseases.show', ['slug' => $disease->slug]);
    @endphp
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <lastmod>{{ $disease->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ $bnUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <lastmod>{{ $disease->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
