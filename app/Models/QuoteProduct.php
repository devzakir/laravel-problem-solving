<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuoteProduct extends Model {

    protected $fillable = [
                            'quote_id',
                            'name',
                            'price',
                            'amount',
                            'total_price',
                            'remark',
                            'tax'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'quote_products';
}