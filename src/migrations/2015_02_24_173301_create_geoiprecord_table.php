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
			$table->string('isoCode', 15)->nullable();
			$table->string('country', 30)->nullable();
			$table->string('city', 30)->nullable();
			$table->string('state', 30)->nullable();
			$table->string('postal_code', 15)->nullable();
			$table->double('lat')->nullable();
			$table->double('lon')->nullable();
			$table->string('timezone', 30)->nullable();
			$table->string('continent', 30)->nullable();
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
