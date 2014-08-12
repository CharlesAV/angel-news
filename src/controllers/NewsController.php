<?php namespace Angel\News;

use App, View;

class NewsController extends \Angel\Core\AngelController {
	
	public function __construct()
	{
		$this->News = $this->data['News'] = App::make('News');

		parent::__construct();
	}
	
	function index()
	{
		// Query
		$objects = $this->News
			->orderBy('date','desc');
			
		// Pagination
		$paginator = $objects->paginate(5);
		$this->data['articles'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
			
		// Return
		return View::make('news::news.index',$this->data);
	}

	public function show($slug)
	{
		// Item
		$article = $this->News
			->where('slug', $slug)
			->first();
		if (!$article || !$article->is_published()) App::abort(404);
		$this->data['article'] = $article;
		
		// Return
		return View::make('news::news.show', $this->data);
	}

	public function show_language($language_uri = 'en', $slug)
	{
		// Language
		$language = $this->languages->filter(function ($language) use ($language_uri) {
			return ($language->uri == $language_uri);
		})->first();
		if (!$language) App::abort(404);

		//  Item
		$article = $this->News
			->where('language_id', $language->id)
			->where('slug', $slug)
			->first();
		if (!$article || !$article->is_published()) App::abort(404);
		$this->data['active_language'] = $language;
		$this->data['article'] = $article;

		// Return
		return View::make('news::news.show', $this->data);
	}
	
	function archive($year,$month)
	{
		// Year / month
		$this->data['year'] = $year;
		$this->data['month'] = $month;
		
		// Start / end
		$carbon = \Carbon\Carbon::create($year,$month,1,0,0,0);
		$start = $carbon->toDateString();
		$this->data['month_string'] = date('F',$carbon->timestamp);
		$carbon = \Carbon\Carbon::create($year,($month + 1),1,0,0,0);
		$end = $carbon->toDateString();
		
		// Query
		$objects = $this->News
			->where('date','>',$start)
			->where('date','<',$end)
			->orderBy('date','desc');
			
		// Pagination
		$paginator = $objects->paginate(5);
		$this->data['articles'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
			
		// Return
		return View::make('news::news.archive',$this->data);
	}
}