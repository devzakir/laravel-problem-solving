<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductValue extends Model {
	protected $fillable = [
							'product_id',
							'product_user_column_id',
							'value',
						];
	protected $primaryKey = 'id';
	protected $table = 'product_values';

  public function productUserColumn() {
    return $this->belongsTo('\App\Models\ProductUserColumn', 'product_user_column_id')->with('productColumn');
  }

	  /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
		protected $appends = [
			'value_url',
		];

		  /**
   * Get the profile photo URL attribute.
   *
   * @return string
   */
  public function getValueUrlAttribute()
  {
      if (is_null($this->value)) {
          return null;
      } else {
          return Storage::disk('public')->url($this->value);
      }
  }
}