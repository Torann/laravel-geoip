<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoiprecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('geoip_records', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ip', 15)->unique();
			$table->string('isoCode', 15);
			$table->string('country', 30);
			$table->string('city', 30);
			$table->string('state', 30);
			$table->string('postal_code', 15);
			$table->double('lat');
			$table->double('lon');
			$table->string('timezone', 30);
			$table->string('continent', 30);
			$table->boolean('default');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('geoip_records');
	}

}
