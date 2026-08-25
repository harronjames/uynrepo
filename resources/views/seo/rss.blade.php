{!! '<' . '?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ config('seo.organization.description') }}</description>
        <language>de-AT</language>
        <atom:link href="{{ route('seo.rss') }}" rel="self" type="application/rss+xml"/>
        @foreach($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('post.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('post.show', $post) }}</guid>
                <pubDate>{{ $post->displayDate()->toRfc2822String() }}</pubDate>
                <description><![CDATA[{!! $post->seoDescription() !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
