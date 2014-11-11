<h2>Recent News Articles</h2>
@if($articles = $News->recent(5))
	<ul>
	@foreach($articles as $article)
		<li>
			<a href="{{ $article->link() }}">{{ $article->name }}</a><br />
			<em>{{ date('m/d/Y',strtotime($article->date)) }}</em>
		</li>
	@endforeach
	</ul>
@else 
	No Recent News Articles
@endif