<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->date('date');
			$table->string('name');
			$table->string('slug');
			$table->text('html');
			$table->text('plaintext');
			$table->string('title');
			$table->string('meta_description');
			$table->string('meta_keywords');
			$table->string('og_type');
			$table->string('og_image');
			$table->string('twitter_card');
			$table->string('twitter_image');
			$table->boolean('published')->default(1);
			$table->boolean('published_range')->default(0);
			$table->timestamp('published_start');
			$table->timestamp('published_end');
			$table->timestamps(); // Adds `created_at` and `updated_at` columns

			if (Config::get('core::languages')) {
				$table->integer('language_id')->unsigned()->default(1);
				$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
			}
		});

		if (ToolBelt::mysql_greater('5.6.4')) {
			DB::statement('ALTER TABLE `news` ADD FULLTEXT search(`name`, `plaintext`, `meta_description`, `meta_keywords`)');
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('news');
	}

}
