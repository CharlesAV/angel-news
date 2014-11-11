<?php namespace Angel\News;

use Angel\Core\AdminCrudController;
use App, Input, View, Config;

class AdminNewsController extends AdminCrudController {

	protected $Model	= 'News';
	protected $uri		= 'news';
	protected $plural	= 'articles';
	protected $singular	= 'article';
	protected $package	= 'news';

	protected $log_changes = true;
	protected $searchable = array(
		'date',
		'name',
		'slug',
		'html'
	);

	// Columns to update on edit/add
	protected static function columns()
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

	public function index()
	{
		$News   = App::make('News');
		$articles = $News->withTrashed();

		if (Config::get('core::languages') && in_array(Config::get('language_models'), $this->Model)) {
			$articles = $articles->where('language_id', $this->data['active_language']->id);
		}

		if (isset($this->searchable) && count($this->searchable)) {
			$search = Input::get('search') ? urldecode(Input::get('search')) : null;
			$this->data['search'] = $search;

			if ($search) {
				$terms = explode(' ', $search);
				$articles = $articles->where(function($query) use ($terms) {
					foreach ($terms as $term) {
						$term = '%'.$term.'%';
						foreach ($this->searchable as $column) {
							$query->orWhere($column, 'like', $term);
						}
					}
				});
			}
		}

		$articles->orderBy('date','desc');
		$paginator = $articles->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make($this->view('index'), $this->data);
	}
	
	public function after_save($article, &$changes = array())
	{
		$article->plaintext = strip_tags($article->html);
		$article->save();
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

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'date' => 'required',
			'name' => 'required',
			'slug' => 'required|alpha_dash|unique:news,slug,' . $id
		);
	}

	/**
	 * @param array &$errors - The array of failed validation errors.
	 * @return array - A key/value associative array of custom values.
	 */
	public function validate_custom($id = null, &$errors)
	{
		$published_start = Input::get('published_start');
		$published_end   = Input::get('published_end');
		if (Input::get('published_range') && $published_end && strtotime($published_start) >= strtotime($published_end)) {
			$errors[] = 'The publication end time must come after the start time.';
		} else if (!Input::get('published_range')) {
			// Reset these so that we won't ever get snagged by an impossible range
			// if the user has collapsed the publication range expander.
			$published_start = $published_end = 0;
		}

		return array(
			'title'           => Input::get('title') ? Input::get('title') : Input::get('name'),
			'published'       => Input::get('published') ? 1 : 0,
			'published_range' => Input::get('published_range') ? 1 : 0,
			'published_start' => $published_start,
			'published_end'   => $published_end
		);
	}
}