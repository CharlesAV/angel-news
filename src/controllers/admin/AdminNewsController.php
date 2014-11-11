<?php namespace Angel\News;

use Angel\Core\AdminCrudController;
use App, Input, View, Config;

class AdminNewsController extends AdminCrudController {

	protected $Model	= 'News';
	protected $uri		= 'news';
	protected $plural	= 'articles';
	protected $singular	= 'article';
	protected $package	= 'news';

	public function index()
	{
		$News   = App::make('News');
		$articles = $News->withTrashed();

		if (Config::get('core::languages') && in_array(Config::get('language_models'), $this->Model)) {
			$articles = $articles->where('language_id', $this->data['active_language']->id);
		}

		// If a search term has been entered...
		$this->data['search'] = $search = (Input::get('search')) ? urldecode(Input::get('search')) : null;
		if ($search) {
			$terms = explode(' ', $search);
			foreach ($terms as &$term) {
				$term = '%' . $term . '%';
			}

			// Call the search method on the Model
			$resultIDs = array();
			$News->search($terms)->each(function($article) use (&$resultIDs) {
				$resultIDs[] = $article->id;
			});
			// Limit the $articles query based on the results, make sure that no objects
			// are returned if there are no results. (where id = 0, it's cheap but it works!)
			$articles = (count($resultIDs)) ? $articles->whereIn('id', $resultIDs) : $articles->where('id', 0);
		}

		$articles->orderBy('date','desc');
		$paginator = $articles->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make($this->view('index'), $this->data);
	}

	public function edit($id)
	{
		$News = App::make($this->Model);

		$article = $News::withTrashed()->find($id);
		$this->data['article'] = $article;
		$this->data['changes'] = $article->changes();
		$this->data['action'] = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
	}
}