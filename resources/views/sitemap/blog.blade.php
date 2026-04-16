{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc>{{ url('/blog') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @foreach($posts as $post)
    @php
        $enUrl = route('blog.show', $post->slug);
        $bnUrl = route('blog.show', ['locale' => 'bn', 'slug' => $post->slug]);
        $hasBn = !empty($post->getTranslation('title', 'bn', false));
    @endphp
    <!-- English URL -->
    <url>
        <loc>{{ $enUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        @if($hasBn)
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        @endif
        <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Bengali URL -->
    @if($hasBn)
    <url>
        <loc>{{ $bnUrl }}</loc>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $enUrl }}" />
        <xhtml:link rel="alternate" hreflang="bn" href="{{ $bnUrl }}" />
        <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endif
    @endforeach
</urlset>
