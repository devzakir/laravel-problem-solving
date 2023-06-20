<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderFormMailTemplateProduct extends Model {

    protected $fillable = [
                            'template_id',
                            'product_id',
                            'price',
                            'amount',
                            'tax_type',
                            'tax',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'order_form_mail_template_products';

    public function template() {
        return $this->belongsTo('App\Models\OrderFormMailTemplate', 'template_id');
    }

    public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id')->with('productValues');
    }
}