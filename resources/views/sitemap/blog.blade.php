<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/blog') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', ['id' => $post->id, 'slug' => $post->slug]) }}</loc>
        <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
