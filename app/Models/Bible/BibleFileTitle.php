<?php

namespace App\Models\Bible;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Bible\BibleFileTitle
 *
 * @mixin \Eloquent
 *
 * @OAS\Schema (
 *     type="object",
 *     description="The Bible File Title model communicates information about generalized fileset sizes",
 *     title="BibleFileTitle",
 *     @OAS\Xml(name="BibleFileTitle")
 * )
 *
 */
class BibleFileTitle extends Model
{
    public $table = "bible_file_titles";

	 /**
	  *
	  * @OAS\Property(
	  *   title="file_id",
	  *   type="integer",
	  *   description="The incrementing id of the file timestamp"
	  * )
	  *
	  * @method static BibleFileTitle whereFileId($value)
	  * @property int $file_id
	  *
	  */
	 protected $file_id;
	 /**
	  *
	  * @OAS\Property(
	  *   title="iso",
	  *   type="string",
	  *   description="The translation language of the title"
	  * )
	  *
	  * @method static BibleFileTitle whereIso($value)
	  * @property string $iso
	  *
	  */
	 protected $iso;
	 /**
	  *
	  * @OAS\Property(
	  *   title="title",
	  *   type="string",
	  *   description="The title of the file"
	  * )
	  *
	  * @method static BibleFileTitle whereTitle($value)
	  * @property string $title
	  *
	  */
	 protected $title;
	 /**
	  *
	  * @OAS\Property(
	  *   title="description",
	  *   type="string",
	  *   description="The description of the file title"
	  * )
	  *
	  * @method static BibleFileTitle whereDescription($value)
	  * @property string|null $description
	  *
	  */
	 protected $description;
	 /**
	  *
	  * @OAS\Property(
	  *   title="key_words",
	  *   type="string",
	  *   description="The words"
	  * )
	  *
	  * @method static BibleFileTitle whereKeyWords($value)
	  * @property string|null $key_words
	  *
	  */
	 protected $key_words;

	 /*
	  * @property-read \App\Models\Bible\BibleFile $file
	  */
    public function file()
    {
    	return $this->BelongsTo(BibleFile::class);
    }

}
