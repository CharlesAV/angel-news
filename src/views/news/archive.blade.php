@extends('core::template')

@section('title')
{{ $month_string }} {{ $year }} | News Archive
@stop

@section('meta')
	{{ $News->meta_html() }}
@stop

@section('content')
	<div class="row">
		<div class="col-md-9">
			<h1 class="news-archive-heading">{{ $month_string }} {{ $year }}</h1>
			@foreach($articles as $article) 
			<div class="news-archive-article">
				<h2 class="news-name"><a href="{{ $article->link() }}">{{ $article->name }}</a></h2>
				<div class="news-date">{{ date('F j, Y',strtotime($article->date)) }}</div>
				<div class="news-html">
					{{ $article->html }}
				</div>
				<hr />
			</div>
			@endforeach
			
			<div class="row text-center">
				{{ $links }}
			</div>
		</div>
		<div class="col-md-3">
			@include('news::news.sidebar')
		</div>
	</div>
@stop