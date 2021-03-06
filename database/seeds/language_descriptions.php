<?php

use Illuminate\Database\Seeder;
use App\Models\Language\LanguageTranslation;
use App\Models\Language\Language;

class language_descriptions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $descriptions = collect(glob(storage_path("data/languages/descriptions/*/*.json")));
		foreach ($descriptions as $description_url) {
			$description = json_decode(file_get_contents($description_url));

			// Check for full JSON structure from wiki
			if(isset($description->query)) {
				$description = collect($description->query->pages)->first();
				if(!isset($description->extract)) { continue; }
				$description = $description->extract;
			}
			if(is_null($description)) {continue;}

			// Parse URL for ISOs
			$description_iso = substr($description_url,51,3);
			$translation_iso = substr($description_url,47,3);

			// Fetch Languages
			$description_language = Language::where('iso',$description_iso)->first()->id;
			$translation_language = Language::where('iso',$translation_iso)->first()->id;

			// Update Language Translations Table
			$translation = LanguageTranslation::where('language_source',$description_language)->where('language_translation',$translation_language)->first();
			if(!$translation) { continue; }
			$translation->description = $description;
			$translation->save();
		}



    }
}
