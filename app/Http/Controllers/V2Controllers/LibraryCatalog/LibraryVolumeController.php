<?php

namespace App\Http\Controllers\V2Controllers\LibraryCatalog;

use App\Http\Controllers\APIController;
use App\Transformers\V2\LibraryVolumeTransformer;
use App\Traits\AccessControlAPI;

use App\Models\Bible\BibleFileset;
use App\Models\Bible\Bible;

class LibraryVolumeController extends APIController
{

	use AccessControlAPI;

    /**
     * v2_volume_history
     *
     * @link https://api.dbp.dev/library/volumehistory?key=1234&v=2
     *
     * @OAS\Get(
     *     path="/library/volumehistory",
     *     tags={"Library Catalog"},
     *     summary="Volume History List",
     *     description="This call gets the event history for volume changes to status, expiry, basic info, delivery, and organization association. The event reflects the previous state of the volume. In other words, it reflects the state up to the moment of the time of the event.",
     *     operationId="v2_volume_history",
     *     @OAS\Parameter(name="limit",  in="query", description="The Number of records to return"),
     *     @OAS\Parameter(ref="#/components/parameters/version_number"),
     *     @OAS\Parameter(ref="#/components/parameters/key"),
     *     @OAS\Parameter(ref="#/components/parameters/pretty"),
     *     @OAS\Parameter(ref="#/components/parameters/format"),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(mediaType="application/json", @OAS\Schema(ref="#/components/schemas/v4_bible.one")),
     *         @OAS\MediaType(mediaType="application/xml",  @OAS\Schema(ref="#/components/schemas/v4_bible.one")),
     *         @OAS\MediaType(mediaType="text/x-yaml",      @OAS\Schema(ref="#/components/schemas/v4_bible.one"))
     *     )
     * )
     *
     * A Route to Review The Last 500 Recent Changes to The Bible Resources
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function history()
    {
        if (!$this->api) return view('bibles.history');

        $limit  = checkParam('limit', null, 'optional') ?? 500;
        $filesets = BibleFileset::with('bible.language')->has('bible.language')->take($limit)->get();
        $filesets->map(function($fileset) {
            $fileset->v2_id = strtoupper($fileset->bible->first()->language->iso.substr($fileset->bible->first()->id,3,3));
            return $fileset;
        });

        return $this->reply(fractal($filesets, new LibraryVolumeTransformer())->serializeWith($this->serializer));
    }

	/**
	 *
	 *
	 * Display a listing of the bibles.
	 *
	 * @OAS\Get(
	 *     path="/library/volume",
	 *     tags={"Library Catalog"},
	 *     summary="",
	 *     description="This method retrieves the available volumes in the system according to the filter specified",
	 *     operationId="v2_library_volume",
	 *     @OAS\Parameter(
	 *          name="dam_id",
	 *          in="query",
	 *          description="The Bible Id",
	 *          ref="#/components/schemas/Bible/properties/id"
	 *     ),
	 *     @OAS\Parameter(
	 *          name="fcbh_id",
	 *          in="query",
	 *          description="An alternative query name for the bible id",
	 *          @OAS\Schema(type="string")
	 *     ),
	 *     @OAS\Parameter(
	 *          name="media",
	 *          in="query",
	 *          description="If set, will filter results by the type of media for which filesets are available.",
	 *         @OAS\Schema(
	 *          type="string",
	 *          @OAS\ExternalDocumentation(
	 *              description="For a complete list of media types please see the v4_bible_filesets.types route",
	 *              url="/docs/swagger/v4#/Bibles/v4_bible_filesets_types"
	 *          )
	 *         )
	 *     ),
	 *     @OAS\Parameter(
	 *          name="language",
	 *          in="query",
	 *          description="The language to filter results by",
	 *          @OAS\Schema(ref="#/components/schemas/Language/properties/name")
	 *     ),
	 *     @OAS\Parameter(
	 *          name="full_word",
	 *          in="query",
	 *          description="Consider the language name as being a full word. For instance, when false,
	               'new' will return volumes where the string 'new' is anywhere in the language name,
	               like in `Newari` and `Awa for Papua New Guinea`. When true, it will only return volumes
	               where the language name contains the word 'new', like in `Awa for Papua New Guinea`.",
	 *          @OAS\Schema(ref="#/components/schemas/Language/properties/name")
	 *     ),
	 *     @OAS\Parameter(
	 *          name="language_name",
	 *          in="query",
	 *          description="The language name to filter results by. For a complete list see the `/languages` route",
	 *          @OAS\Schema(ref="#/components/schemas/Language/properties/name")),
	 *     @OAS\Parameter(
	 *          name="language_code",
	 *          in="query",
	 *          description="The iso code to filter results by. This will return results only in the language specified.",
	 *          @OAS\Schema(ref="#/components/schemas/Language/properties/iso"),
	 *          @OAS\ExternalDocumentation(
	 *              description="For a complete list see the `iso` field in the `/languages` route",
	 *              url="/docs/swagger/v2#/Languages"
	 *          )),
	 *     @OAS\Parameter(
	 *          name="language_family_code",
	 *          in="query",
	 *          description="The iso code of the trade language to filter results by. This will also return all dialects of a language. For a complete list see the `iso` field in the `/languages` route",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(
	 *          name="updated",
	 *          in="query",
	 *          description="The last time updated",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(
	 *          name="organization_id",
	 *          in="query",
	 *          description="The owning organization to return bibles for. For a complete list see the `/organizations` route",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(
	 *          name="sort_by",
	 *          in="query",
	 *          description="The any field to within the bible model may be selected as the value for this `sort_by` param.",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(
	 *          name="sort_dir",
	 *          in="query",
	 *          description="The direction to sort by the field specified in `sort_by`. Either `asc` or `desc`",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(
	 *          name="filter_by_fileset",
	 *          in="query",
	 *          description="This field defaults to true but when set to false will return all Bible entries regardless of whether or not the API has content for that biblical text.",
	 *          @OAS\Schema(type="string")),
	 *     @OAS\Parameter(ref="#/components/parameters/version_number"),
	 *     @OAS\Parameter(ref="#/components/parameters/key"),
	 *     @OAS\Parameter(ref="#/components/parameters/pretty"),
	 *     @OAS\Parameter(ref="#/components/parameters/format"),
	 *     @OAS\Response(
	 *         response=200,
	 *         description="successful operation",
	 *         @OAS\MediaType(mediaType="application/json",
	 *          @OAS\Schema(ref="#/components/schemas/v4_bible.one")),
	 *         @OAS\MediaType(mediaType="application/xml",  @OAS\Schema(ref="#/components/schemas/v4_bible.one")),
	 *         @OAS\MediaType(mediaType="text/x-yaml",      @OAS\Schema(ref="#/components/schemas/v4_bible.one"))
	 *     )
	 * )
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function libraryVolume()
	{
		if (env('APP_ENV') == 'local') ini_set('memory_limit', '864M');
		// Return the documentation if it's not an API request
		if (!$this->api) return view('bibles.index');

		$dam_id             = checkParam('dam_id|fcbh_id', null, 'optional');
		$media              = checkParam('media', null, 'optional');
		$language           = checkParam('language', null, 'optional');
		$full_word          = checkParam('full_word', null, 'optional');
		$iso                = checkParam('language_code|language_family_code', null, 'optional');
		$updated            = checkParam('updated', null, 'optional');
		$status             = checkParam('status', null, 'optional');
		$organization       = checkParam('organization_id', null, 'optional');
		$sort_by            = checkParam('sort_by', null, 'optional');
		$sort_dir           = checkParam('sort_dir', null, 'optional') ?? 'asc';
		$include_regionInfo = checkParam('include_region_info', null, 'optional');
		$bucket             = checkParam('bucket|bucket_id', null, 'optional') ?? env('FCBH_AWS_BUCKET');

		$access_control = $this->accessControl($this->key, "api");

		$cache_string = 'library_volume' . $dam_id . '_' . $media . '_' . $language . '_' . $include_regionInfo . $full_word . '_' . $iso . '_' . $updated . '_' . $organization . '_' . $sort_by . '_' . $sort_dir . '_' . '_' . $bucket . $access_control->string;
		//\Cache::forget($cache_string);
		//$bibles = \Cache::remember($cache_string, 1600, function () use ($dam_id, $media, $language, $full_word, $iso, $updated, $organization, $sort_by, $sort_dir, $bucket, $include_regionInfo, $access_control) {
			$output = [];
			$filesets = BibleFileset::with(['bible.translatedTitles', 'bible.language.parent.parentLanguage', 'bible.alphabet',
			'bible.organizations' => function ($q) use($organization) {
				if($organization) $q->where('organization_id', $organization);
			},
			'bible' => function($query) use($iso) {
				$query->with(['language' => function ($q) use($iso) {
					if($iso) $q->where('iso', $iso);
				}]);
			}])->has('bible.language')
			->where('bucket_id', $bucket)->has('bible.translations')

			// Version 2 does not support delivery via s3
			->where('set_type_code','!=','text_format')

			// Check substring for several dam_id variations
            ->when($dam_id, function ($q) use ($dam_id) {
				$q->where('id', $dam_id)->orWhere('id',substr($dam_id,0,-4))->orWhere('id',substr($dam_id,0,-2));
			})

			// Filter by media
			->when($media, function ($q) use ($media) {
				switch ($media) {
					case "video": {$q->has('filesetFilm');break;}
					case "audio": {$q->has('filesetAudio');break;}
					case "text":  {$q->has('filesetText');break;}
				}
			})->when($updated, function ($q) use ($updated) {
				$q->where('updated_at', '>', $updated);
			})->when($sort_by, function ($q) use ($sort_by, $sort_dir) {
				$q->orderBy($sort_by, $sort_dir);
			})->get();

			return $this->reply(fractal($this->generate_v2_style_id($filesets), new LibraryVolumeTransformer())->serializeWith($this->serializer));
		//});
		//return $this->reply($bibles);
	}

	private function generate_v2_style_id($filesets)
	{
		$output = [];
		foreach($filesets as $fileset) {
			if(!$fileset->bible->first()) { continue; }
			$bible_id = substr($fileset->bible->first()->id,0,6);
			switch($fileset->set_type_code) {
				case "audio_drama": { $type_code = "2DA"; break; }
				case "audio":       { $type_code = "1DA"; break; }
				case "text_plain":  { $type_code = "ET"; break; }
			}

			switch ($fileset->set_size_code) {
				case "C":
				case "NTOTP":
				case "OTNTP":
				case "NTPOTP": {
					if($type_code == "ET") {
						$output[$bible_id.'O1'.$type_code] = clone $fileset;
						$output[$bible_id.'N1'.$type_code] = clone $fileset;
						$output[$bible_id.'O2'.$type_code] = clone $fileset;
						$output[$bible_id.'N2'.$type_code] = clone $fileset;
					} else {
						$output[$bible_id.'O'.$type_code] = clone $fileset;
						$output[$bible_id.'N'.$type_code] = clone $fileset;
					}
					break;
				}

				case "NT":
				case "NTP":    {
					if($type_code == "ET") {
						$output[$bible_id.'N1'.$type_code] = clone $fileset;
						$output[$bible_id.'N2'.$type_code] = clone $fileset;
					} else {
						$output[$bible_id.'N'.$type_code] = clone $fileset;
					}
					break;
				}

				case "OT":
				case "OTP":    {
					if($type_code == "ET") {
						$output[$bible_id.'O1'.$type_code] = clone $fileset;
						$output[$bible_id.'O2'.$type_code] = clone $fileset;
					} else {
						$output[$bible_id.'O'.$type_code] = clone $fileset;
					}
					break;
				}
			}
		}

		foreach ($output as $key => $item) {$output[$key]->generated_id = $key;}
		return $output;
	}

}
