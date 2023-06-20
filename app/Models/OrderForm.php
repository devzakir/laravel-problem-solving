<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderForm extends Model {

    protected $fillable = [
                            'name',
                            'condition',
                            'has_deadline',
                            'payment_method',
                            'memo',
                            'is_public',
                            'recipienter_id',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'order_forms';

    public function orderedProducts() {
        return $this->hasMany('\App\Models\OrderedProduct', 'order_form_id')->with('product');
    }

    public function orders() {
        return $this->hasMany('\App\Models\OrderFormMailTemplate', 'order_form_id')->orderByDesc('ordered_at');
    }
}