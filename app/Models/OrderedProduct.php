<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderedProduct extends Model {

    protected $fillable = [
                            'order_form_id',
                            'product_id',
                            'recipienter_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'ordered_products';

    public function product() {
        return $this->belongsTo('\App\Models\Product', 'product_id');
    }
}