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
        $bnUrl = route('bn.doctors.show', ['slug' => $doctor->slug]);
    @endphp
    <!-- English URL -->
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <lastmod>{{ $doctor->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Bengali URL -->
    <url>
        <loc>{{ $bnUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <lastmod>{{ $doctor->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach
</urlset>
