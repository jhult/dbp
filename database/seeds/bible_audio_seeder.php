<?php

use Illuminate\Database\Seeder;
use App\Models\Bible\BibleEquivalent;
use App\Models\Bible\BibleFileset;
use App\Models\Bible\BibleFile;
use App\Models\Bible\Book;
use App\Models\Bible\BookCode;
use database\seeds\SeederHelper;

class bible_audio_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    ini_set('memory_limit', '3000M');

	    \DB::table('bible_file_timestamps')->delete();
	    \DB::table('bible_files')->delete();

	    $seederhelper = new SeederHelper();
	    $chapters = $seederhelper->csv_to_array(storage_path() . "/data/dbp3/audio.csv");
	    $setsCreated = array();
	    $fcbh_id = \App\Models\Organization\Organization::where('slug','faith-comes-by-hearing')->first()->id;
	    foreach($chapters as $chapter) {
		    if(!isset($chapter['audio_path'])) { continue; }
	    	$dam_id = $chapter['dam_id'];
		    $testament = (substr($dam_id,-4,1) == 'N') ? "New Testament" : "Old Testament";
		    $testament_code = (substr($dam_id,-4,1) == 'N') ? "NT" : "OT";
		    if(substr($dam_id,-2,1) == 'D') {
			    $type = (substr($dam_id,-3,1) == 2) ? "audio_drama" : "audio";
		    } else {
		    	$type = "text";
		    }

	    	if(!in_array($dam_id,$setsCreated)) {
			    $bibleEquivalent = BibleEquivalent::where('equivalent_id',$dam_id)->first();
			    if(!$bibleEquivalent) {$missing[] = $dam_id;continue;}

	    		$audioSet = BibleFileset::create([
				    'id'              => $chapter['dam_id'],
			        'variation_id'    => null,
					'set_type'        => $type,
				    'size_code'       => $testament_code,
				    'size_name'       => $testament,
					'name'            => 'Faith Comes by Hearing',
					'bible_id'        => $bibleEquivalent->bible->id,
					'organization_id' => $fcbh_id,
			    ]);
			    $audioSet->save();
			    $setsCreated[] = $dam_id;
		    }
	    	// Select Bible using FCBH DAM_ID

	    	// Create the Audio Resource

		    $book = Book::where('id', $chapter['book_code'])->first();
		    if(!$book) {echo "Missing USFM:". $chapter['book_code'];continue;}
			if(BibleFile::where('file_name',$chapter['audio_path'])->first()) {continue;}
			$audioSet->files()->create([
				'file_name'     => $chapter['audio_path'],
				'book_id'       => $book->id,
				'chapter_start' => $chapter['number'],
				'chapter_end'   => null,
				'verse_start'   => 1,
				'verse_end'     => null,
			]);
	    }
	    echo "Missing IDs: ".implode(',',array_unique($missing));

    }
}
