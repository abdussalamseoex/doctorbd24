{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($seoPages as $page)
    @php
        $enUrl = url('/' . $page->slug);
        $bnUrl = url('/bn/' . $page->slug);
        $hasBn = !empty($page->getTranslation('title', 'bn', false)) || !empty($page->getTranslation('content_top', 'bn', false));
    @endphp
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        @if($hasBn)
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        @endif
        <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach

    @foreach($staticPages as $page)
    @php
        $enUrl = url('/' . $page->slug);
        $bnUrl = url('/bn/' . $page->slug);
        $hasBn = !empty($page->getTranslation('title', 'bn', false));
    @endphp
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        @if($hasBn)
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        @endif
        <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
