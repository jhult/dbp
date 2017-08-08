<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('countries', function (Blueprint $table) {
            $table->char('id', 2)->unique();
            $table->char('iso_a3', 3)->unique();
            $table->char('fips', 2);
            $table->char('continent', 2);
            $table->string('name');
        });

        Schema::create('languages', function (Blueprint $table) {
        	$table->increments('id');
            $table->char('glotto_id',8)->nullable()->unique();
            $table->char('iso',3)->nullable()->unique();
            $table->string('name');
            $table->string('level')->nullable();
            $table->string('maps')->nullable();
            $table->text('development')->nullable();
            $table->text('use')->nullable();
            $table->text('location')->nullable();
            $table->text('area')->nullable();
            $table->integer('population')->unsigned()->nullable();
            $table->text('population_notes')->nullable();
            $table->text('notes')->nullable();
            $table->text('typology')->nullable();
            $table->text('writing')->nullable();
            $table->text('description')->nullable();
            $table->integer('family_pk')->unsigned()->default(0)->nullable();
            $table->integer('father_pk')->unsigned()->default(0)->nullable();
            $table->integer('child_dialect_count')->unsigned()->default(0)->nullable();
            $table->integer('child_family_count')->unsigned()->default(0)->nullable();
            $table->integer('child_language_count')->unsigned()->default(0)->nullable();
            $table->float('latitude',11,7)->nullable();
            $table->float('longitude',11,7)->nullable();
            $table->integer('pk')->unsigned()->default(0);
            $table->text('status')->nullable();
            $table->char('country_id',2)->nullable();
            $table->string('scope')->nullable();
        });

        Schema::create('languages_translations', function (Blueprint $table) {
            $table->integer('language_source')->unsigned();
            $table->foreign('language_source')->references('id')->on('languages')->onUpdate('cascade');
            $table->integer('language_translation')->unsigned();
            $table->foreign('language_translation')->references('id')->on('languages')->onUpdate('cascade');
            $table->string('name');
        });

        Schema::create('languages_altNames', function (Blueprint $table) {
        	$table->increments('id');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->string('name');
        });

        Schema::create('languages_dialects', function (Blueprint $table) {
	        $table->increments('id');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->char('dialect_id', 8)->index()->nullable()->default(NULL);
            $table->text('name');
        });

        Schema::create('languages_classifications', function (Blueprint $table) {
	        $table->increments('id');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->char('classification_id', 8);
            $table->tinyInteger('order')->unsigned();
            $table->string('name');
        });

        Schema::create('languages_codes', function (Blueprint $table) {
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->string('source');
            $table->string('code');
        });

        Schema::create('alphabets', function (Blueprint $table) {
            $table->char('script', 4)->unique()->index(); // ScriptSource/Iso ID
            $table->string('name');
	        $table->string('unicode_pdf')->nullable();
            $table->string('family')->nullable();
            $table->string('type')->nullable();
	        $table->string('white_space')->nullable();
	        $table->string('complex_positioning')->nullable();
	        $table->string('open_type_tag')->nullable();
	        $table->boolean('requires_font')->default(0);
	        $table->boolean('unicode')->default(1);
	        $table->boolean('diacritics')->nullable();
	        $table->boolean('contextual_forms')->nullable();
	        $table->boolean('reordering')->nullable();
	        $table->boolean('case')->nullable();
	        $table->boolean('split_graphs')->nullable();
	        $table->string('status')->nullable();
	        $table->string('baseline')->nullable();
	        $table->string('ligatures')->nullable();
            $table->char('direction', 3)->nullable(); // rtl, ltr, ttb
            $table->text('direction_notes')->nullable();
            $table->text('sample')->nullable();
	        $table->string('sample_img')->nullable();
	        $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

	    Schema::create('alphabet_numbers', function (Blueprint $table) {
	    	$table->increments('id');
		    $table->char('script_id',4);
		    $table->foreign('script_id')->references('script')->on('alphabets')->onUpdate('cascade');
		    $table->char('script_varient_iso',3);
		    $table->integer('numeral')->unsigned();
			$table->string('numeral_vernacular',12);
		    $table->string('numeral_written',24);
	    });

        Schema::create('alphabet_language', function (Blueprint $table) {
            $table->char('script', 4)->index();
            $table->foreign('script')->references('script')->on('alphabets')->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
        });

        Schema::create('alphabet_fonts', function (Blueprint $table) {
	        $table->increments('id');
            $table->char('script_id', 4);
            $table->foreign('script_id')->references('script')->on('alphabets')->onUpdate('cascade');
            $table->string('fontName');
            $table->string('fontFileName');
            $table->integer('fontWeight')->unsigned()->nullable()->default(null);
	        $table->string('copyright')->nullable()->default(null);
	        $table->string('url')->nullable()->default(null);
	        $table->text('notes')->nullable()->default(null);
            $table->boolean('italic')->default(0);
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->char('country_id', 2);
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->string('name');
        });

        Schema::create('country_regions', function (Blueprint $table) {
            $table->char('country_id', 2);
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
            $table->string('name');
        });

        Schema::create('country_language', function (Blueprint $table) {
            $table->char('country_id', 2);
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alphabet_language');
	    Schema::dropIfExists('alphabet_numbers');
        Schema::dropIfExists('alphabet_fonts');
        Schema::dropIfExists('alphabets');
        Schema::dropIfExists('languages_translations');
        Schema::dropIfExists('languages_codes');
        Schema::dropIfExists('languages_classifications');
        Schema::dropIfExists('languages_altNames');
        Schema::dropIfExists('languages_dialects');
        Schema::dropIfExists('country_regions');
        Schema::dropIfExists('country_translations');
        Schema::dropIfExists('country_language');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('countries');
    }
}
