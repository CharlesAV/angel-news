<h2>Archive</h2>
@if($array = $News->archive())
	<ul>
	@foreach($array as $v)
		<li><a href="{{ $v['url'] }}">{{ $v['month'] }} {{ $v['year'] }} ({{ $v['count'] }})</a></li>
	@endforeach
	</ul>
@else 
	No News Articles Found
@endif