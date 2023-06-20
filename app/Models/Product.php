<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model {

    protected $fillable = [
                            'is_public',
                            'mark',
                            'recipienter_id',
                            'tax',
                            'tax_type'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'products';

    public function productValues() {
        return $this->hasMany('\App\Models\ProductValue', 'product_id')->with('productUserColumn');
    }
}