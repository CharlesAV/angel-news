@extends('core::template')

@section('title','News')

@section('content')
	<div class="row">
		<div class="col-md-9">
			@foreach($articles as $article) 
			<div class="news-item">
				<h1 class="news-name"><a href="{{ $article->link() }}">{{ $article->name }}</a></h1>
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