@extends('core::template')

@section('title', $article->title)

@section('meta')
	{{ $article->meta_html() }}
@stop

@section('content')
	<div class="row">
		<div class="col-md-9">
			<h1 class="news-name">{{ $article->name }}</h1>
			<div class="news-date">{{ date('F j, Y',strtotime($article->date)) }}</div>
			<div class="news-html">
				{{ $article->html }}
			</div>
		</div>
		<div class="col-md-3">
			@include('news::news.sidebar')
		</div>
	</div>
@stop