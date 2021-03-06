<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\AccessGroupFunction
 * @mixin \Eloquent
 *
 * @OAS\Schema (
 *     type="object",
 *     description="The Access Group Key",
 *     title="AccessGroupKey",
 *     @OAS\Xml(name="AccessGroupKey")
 * )
 *
 */
class AccessGroupKey extends Model
{
	public $table = 'access_group_keys';
	public $fillable = ['access_group_id','key_id'];

	/**
	 *
	 * @OAS\Property(
	 *   title="name",
	 *   type="string",
	 *   description="The name for each access group"
	 * )
	 *
	 * @method static AccessGroupKey whereName($value)
	 * @property string $access_group_id
	 */
	protected $access_group_id;

	/**
	 *
	 * @OAS\Property(
	 *   title="name",
	 *   type="string",
	 *   description="The name for each access group"
	 * )
	 *
	 * @method static AccessGroupKey whereName($value)
	 * @property string $key_id
	 */
	protected $key_id;

	public function access()
	{
		return $this->belongsTo(AccessGroup::class,'access_group_id','id');
	}

	public function type()
	{
		return $this->hasManyThrough(AccessType::class, AccessGroupType::class,'id','id','key_id','access_type_id');
	}

}
