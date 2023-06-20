<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductColumn extends Model {
	protected $fillable = [
							'name',
							'type',
							'order',
							'slug',
							'included',
							'picked',
							'needed'
						];
	protected $primaryKey = 'id';
	protected $table = 'product_columns';
}