<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $loc)
    <url>
        <loc>{{ $loc }}</loc>
        <changefreq>@if ($loop->first)weekly@else yearly @endif</changefreq>
        <priority>@if ($loop->first)1.0@else0.5@endif</priority>
    </url>
@endforeach
</urlset>
