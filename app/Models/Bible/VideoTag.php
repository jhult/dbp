<?php

namespace App\Models\Bible;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Bible\VideoTags
 *
 * @mixin \Eloquent
 * @method static VideoTag whereBookId($value)
 *
 * @OAS\Schema (
 *     type="object",
 *     description="The VideoTags model holds miscellaneous information about the video model.",
 *     title="VideoTags",
 *     @OAS\Xml(name="VideoTags")
 * )
 *
 */
class VideoTag extends Model
{

	/**
	 *
	 * @OAS\Property(
	 *   title="id",
	 *   type="integer",
	 *   description="The incrementing id of the video tag"
	 * )
	 *
	 * @method static VideoTag whereId($value)
	 * @property int $id
	 */
	protected $id;

	/**
	 *
	 * @OAS\Property(
	 *   title="video_id",
	 *   type="integer",
	 *   description="The video id"
	 * )
	 *
	 * @method static VideoTag whereVideoId($value)
	 * @property int|null $video_id
	 */
	protected $video_id;

	/**
	 *
	 * @OAS\Property(
	 *   title="category",
	 *   type="integer",
	 *   description="The category"
	 * )
	 *
	 * @method static VideoTag whereCategory($value)
	 * @property string $category
	 */
	protected $category;

	/**
	 *
	 * @OAS\Property(
	 *   title="category",
	 *   type="integer",
	 *   description="The category"
	 * )
	 *
	 * @method static VideoTag whereTagType($value)
	 * @property string $tag_type
	 */
	protected $tag_type;

	/**
	 *
	 * @OAS\Property(
	 *   title="tag",
	 *   type="string",
	 *   description="The tag"
	 * )
	 *
	 * @method static VideoTag whereTag($value)
	 * @property string $tag
	 */
	protected $tag;

	/**
	 *
	 * @OAS\Property(
	 *   title="tag",
	 *   type="integer",
	 *   description="The language_id",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereLanguageId($value)
	 * @property int|null $language_id
	 */
	protected $language_id;

	/**
	 *
	 * @OAS\Property(
	 *   title="organization_id",
	 *   type="integer",
	 *   description="The language_id",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereOrganizationId($value)
	 * @property int|null $organization_id
	 */
	protected $organization_id;

	/**
     *
	 * @OAS\Property(
	 *   title="book_id",
	 *   type="string",
	 *   description="The book id",
	 *   nullable=true
	 * )
	 *
     * @method static VideoTag whereBookId($value)
     * @property string|null $book_id
    */
	protected $book_id;

	/**
	 *
	 * @OAS\Property(
	 *   title="chapter_start",
	 *   type="integer",
	 *   description="The starting chapter",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereChapterStart($value)
	 * @property int|null $chapter_start
	 */
	protected $chapter_start;

	/**
	 *
	 * @OAS\Property(
	 *   title="chapter_end",
	 *   type="integer",
	 *   description="The ending chapter",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereChapterEnd($value)
	 * @property int|null $chapter_end
	 */
	protected $chapter_end;

	/**
	 *
	 * @OAS\Property(
	 *   title="verse_start",
	 *   type="integer",
	 *   description="The verse_start",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereVerseStart($value)
	 * @property int|null $verse_start
	 */
	protected $verse_start;

	/**
	 *
	 * @OAS\Property(
	 *   title="verse_end",
	 *   type="integer",
	 *   description="The verse_end",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereVerseEnd($value)
	 * @property int|null $verse_end
	 */
	protected $verse_end;

	/**
	 *
	 * @OAS\Property(
	 *   title="time_begin",
	 *   type="integer",
	 *   description="The time_begin",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereTimeBegin($value)
	 * @property float|null $time_begin
	 */
	protected $time_begin;

	/**
	 *
	 * @OAS\Property(
	 *   title="time_end",
	 *   type="integer",
	 *   description="The time_end",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereTimeEnd($value)
	 * @property float|null $time_end
	 */
	protected $time_end;

	/**
	 *
	 * @OAS\Property(
	 *   title="created_at",
	 *   type="string",
	 *   description="The created_at",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereCreatedAt($value)
	 * @property \Carbon\Carbon|null $created_at
	 */
	protected $created_at;

	/**
	 *
	 * @OAS\Property(
	 *   title="updated_at",
	 *   type="string",
	 *   description="The updated_at",
	 *   nullable=true
	 * )
	 *
	 * @method static VideoTag whereUpdatedAt($value)
	 * @property \Carbon\Carbon|null $updated_at
	 */
	protected $updated_at;


}
