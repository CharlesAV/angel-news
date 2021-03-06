<?php namespace Angel\News;

use Angel\Core\LinkableModel;
use App, Config, Input;

class News extends LinkableModel {

	public static function columns()
	{
		$columns = array(
			'date',
			'name',
			'slug',
			'html',
			'title',
			'meta_description',
			'meta_keywords',
			'og_type',
			'og_image',
			'twitter_card',
			'twitter_image',
			'published',
			'published_range',
			'published_start',
			'published_end'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}
	public function validate_rules()
	{
		return array(
			'date' => 'required',
			'name' => 'required',
			'slug' => 'required|alpha_dash|unique:news,slug,' . $this->id
		);
	}
	public function validate_custom()
	{
		$errors = array();
		
		$published_start = Input::get('published_start');
		$published_end   = Input::get('published_end');
		if (Input::get('published_range') && $published_end && strtotime($published_start) >= strtotime($published_end)) {
			$errors[] = 'The publication end time must come after the start time.';
		}

		return $errors;
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($article) {
			$article->plaintext = strip_tags($article->html);
			if (!$article->published_range) {
				$article->published_start = $article->published_end = null;
			}
			$article->title = $article->title ? $article->title : $article->name;
		});
	}
	
	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'News')
				   	       ->where('fid', $this->id)
				   	       ->with('user')
				   	       ->orderBy('created_at', 'DESC')
				   	       ->get();
	}

	// Handling relationships in controller CRUD methods
	public function pre_delete()
	{
		parent::pre_delete();
		$Change = App::make('Change');
		$Change::where('fmodel', 'News')
			        ->where('fid', $this->id)
			        ->delete();
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		return url($language_segment . 'news/' . $this->slug);
	}
	public function link_edit()
	{
		return admin_url('news/edit/' . $this->id);
	}
	public function search($terms)
	{
		return static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name',             'like', $term);
				$query->orWhere('plaintext',        'like', $term);
				$query->orWhere('meta_description', 'like', $term);
				$query->orWhere('meta_keywords',    'like', $term);
			}
		})->get();
	}
                                   
	///////////////////////////////////////////////
	//                View-Related               //
	///////////////////////////////////////////////
	public function meta_html()
	{
		$html = '';
		if ($this->title) {
			$html .= '<meta name="og:title" content="' . $this->title . '" />' . "\n";
			$html .= '<meta name="twitter:title" content="' . $this->title . '" />' . "\n";
		}
		if ($this->meta_description) {
			$html .= '<meta name="description" content="' . $this->meta_description . '" />' . "\n";
			$html .= '<meta name="og:description" content="' . $this->meta_description . '" />' . "\n";
			$html .= '<meta name="twitter:description" content="' . $this->meta_description . '" />' . "\n";
		}
		if ($this->meta_keywords) {
			$html .= '<meta name="keywords" content="' . $this->meta_keywords . '" />' . "\n";
		}
		if ($this->url) {
			$html .= '<meta name="og:url" content="' . $this->link() . '" />' . "\n";
			$html .= '<meta name="twitter:url" content="' . $this->link() . '" />' . "\n";
		}
		if ($this->og_type) {
			$html .= '<meta name="og:type" content="' . $this->og_type . '" />' . "\n";
		}
		if ($this->og_image) {
			$html .= '<meta name="og:image" content="' . $this->og_image . '" />' . "\n";
		}
		if ($this->twitter_card) {
			$html .= '<meta name="twitter:card" content="' . $this->twitter_card . '" />' . "\n";
		}
		if ($this->twitter_image) {
			$html .= '<meta name="twitter:image" content="' . $this->twitter_image . '" />' . "\n";
		}
		return $html;
	}

	public function is_published()
	{
		if ((
				$this->published_range &&
				(strtotime($this->published_start) > time() || strtotime($this->published_end) < time())
			) || (
				!$this->published_range &&
				!$this->published
			)) return false;
		return true;
	}
	
	public function recent($count = 5)
	{
		$articles = $this->orderBy('date','desc')->limit($count)->get();
		
		return $articles;
	}
	
	public function archive()
	{
		$array = array();
		$articles = $this->orderBy('id','desc')->get();
		foreach($articles as $article) {
			$time = strtotime($article->date);
			$year = date('Y',$time);
			$month = date('m',$time);
			$key = $year.$month;
			if(!isset($array[$key])) {
				$array[$key] = array(
					'year' => $year,
					'month' => date('F',$time),
					'time' => strtotime($month."/1/".$year),
					'count' => 1,
					'url' => url('news/archive/'.$year.'/'.$month)
				);
			}
			else $array[$key]['count'] += 1;
		}
		
		// Return
		return $array;
	}
}