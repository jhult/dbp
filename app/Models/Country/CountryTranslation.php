<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Country\CountryTranslation
 *
 * @property string $country_id
 * @property int $language_id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Country\Country $language
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country\CountryTranslation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country\CountryTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country\CountryTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country\CountryTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country\CountryTranslation whereUpdatedAt($value)
 */
class CountryTranslation extends Model
{
    protected $table = 'country_translations';
    protected $hidden = ["country_id","vernacular"];
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = "string";
    public $primaryKey = "country_id";

    public function language()
    {
        return $this->belongsTo('App\Models\Country\Country');
    }

}
