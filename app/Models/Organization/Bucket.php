<?php

namespace App\Models\Organization;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Organization\Bucket
 *
 * @OAS\Schema (
 *     type="object",
 *     description="Bucket",
 *     title="Bucket",
 *     @OAS\Xml(name="Bucket")
 * )
 *
 * @property-read \App\Models\Organization\Organization $organization
 * @mixin \Eloquent
 * @property int $hidden
 */
class Bucket extends Model
{
	public $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
    public $hidden = ['created_at','updated_at'];

	/**
	 *
	 * @OAS\Property(
	 *   title="id",
	 *   type="string",
	 *   description="The id of the Bucket",
	 *   maxLength=64,
	 *   minLength=24
	 * )
	 *
	 * @method static Bucket whereId($value)
	 * @property string $id
	 *
	 */
	protected $id;

	/**
	 *
	 * @OAS\Property(ref="#/components/schemas/Organization/properties/id")
	 *
	 * @method static Bucket whereOrganizationId($value)
	 * @property int $organization_id
	 *
	 */
	protected $organization_id;

	/**
	 *
	 * @OAS\Property(
	 *   title="created_at",
	 *   type="string",
	 *   description="The timestamp at which the bucket was created",
	 * )
	 *
	 * @method static Bucket whereCreatedAt($value)
	 * @property Carbon $created_at
	 *
	 */
	protected $created_at;
	/**
	 *
	 * @OAS\Property(
	 *   title="updated_at",
	 *   type="string",
	 *   description="The timestamp at which the bucket was last updated",
	 * )
	 *
	 * @method static Bucket whereUpdatedAt($value)
	 * @property Carbon $updated_at
	 *
	 */
	protected $updated_at;

    public function organization()
    {
    	return $this->belongsTo(Organization::class);
    }

}
