{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc>{{ url('/doctors') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($doctors as $doctor)
    @php
        $enUrl = route('doctors.show', $doctor->slug);
        $bnUrl = route('doctors.show', ['locale' => 'bn', 'slug' => $doctor->slug]);
        // Doctor model uses Spatie Translatable. Fallback is true by default, so we pass false to check if exists.
        $hasBn = !empty($doctor->getTranslation('name', 'bn', false));
    @endphp
    <!-- English URL -->
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        @if($hasBn)
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        @endif
        <lastmod>{{ $doctor->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Bengali URL -->
    @if($hasBn)
    <url>
        <loc>{{ $bnUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <lastmod>{{ $doctor->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @endif
    @endforeach
</urlset>
