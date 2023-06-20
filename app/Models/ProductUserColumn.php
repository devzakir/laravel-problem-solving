<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductUserColumn extends Model {
	protected $fillable = [
							'recipienter_id',
							'product_column_id',
							'order',
							'picked',
							'nickname',
							'tax',
							'tax_type'
						];
	protected $primaryKey = 'id';
	protected $table = 'product_user_columns';

  public function productColumn()
  {
    return $this->belongsTo('App\Models\ProductColumn', 'product_column_id');
  }
}