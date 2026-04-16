{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc>{{ url('/hospitals') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($hospitals as $hospital)
    @php
        $hasBn = !empty($hospital->getTranslation('name', 'bn', false));
        $tabs = [
            ['tab' => null, 'freq' => 'weekly', 'prio' => '0.9'],
            ['tab' => 'doctors', 'freq' => 'weekly', 'prio' => '0.8'],
            ['tab' => 'services', 'freq' => 'monthly', 'prio' => '0.8'],
        ];
    @endphp
    @foreach($tabs as $t)
    @php
        $enUrl = $t['tab'] ? route('hospitals.show', ['slug' => $hospital->slug, 'tab' => $t['tab']]) : route('hospitals.show', $hospital->slug);
        $bnUrl = $t['tab'] ? route('hospitals.show', ['locale' => 'bn', 'slug' => $hospital->slug, 'tab' => $t['tab']]) : route('hospitals.show', ['locale' => 'bn', 'slug' => $hospital->slug]);
    @endphp
    <!-- English URL for {{ $t['tab'] ?? 'overview' }} -->
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        @if($hasBn)
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        @endif
        <lastmod>{{ $hospital->updated_at->toAtomString() }}</lastmod>
        <changefreq>{{ $t['freq'] }}</changefreq>
        <priority>{{ $t['prio'] }}</priority>
    </url>

    <!-- Bengali URL for {{ $t['tab'] ?? 'overview' }} -->
    @if($hasBn)
    <url>
        <loc>{{ $bnUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <lastmod>{{ $hospital->updated_at->toAtomString() }}</lastmod>
        <changefreq>{{ $t['freq'] }}</changefreq>
        <priority>{{ $t['prio'] }}</priority>
    </url>
    @endif
    @endforeach
    @endforeach
</urlset>
