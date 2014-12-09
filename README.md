Angel News
==============
This is a news articles module for the [Angel CMS](https://github.com/CharlesAV/angel).

Installation
------------
Add the following requirements to your `composer.json` file:
```javascript
"require": {
	"angel/news": "dev-master"
},
```

Issue a `composer update` to install the package.

Add the following service provider to your `providers` array in `app/config/app.php`:
```php
'Angel\News\NewsServiceProvider'
```

Issue the following command:
```bash
php artisan migrate --package="angel/news"  # Run the migrations
```

Open up your `app/config/packages/angel/core/config.php` and add the news routes to the `menu` array:
```php
'menu' => array(
	'Pages' => 'pages',
	'Menus' => 'menus',
	'News' => 'news',  // <--- Add this line
	'Users' => 'users',
	'Settings' => 'settings'
),
```
