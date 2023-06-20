<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderFormMailTemplate extends Model {

    protected $fillable = [
                            'recipienter_id',
                            'user_name',
                            'user_id',
                            'title',
                            'content',
                            'address_info',
                            'order_form_id',
                            'ordered',
                            'payed',
                            'payment_method',
                            'message',
                            'deadline',
                            'deadline_time',
                            'hash',
                            'price',
                            'delivery',
                            'invoiced',
                            'invoice_at',
                            'delivery_at',
                            'delivery_hash',
                            'invoice_hash',
                            'delivery_confirm',
                            'ordered_at',
                            'tax',
                            'delivery_card',
                            'delivery_card_at',
                            'tax_type',
                            'pay_expire'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'order_form_mail_templates';

    public function recipienter() {
        return $this->belongsTo('App\Models\Recipienter', 'recipienter_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function orderForm() {
        return $this->belongsTo('App\Models\OrderForm', 'order_form_id');
    }

    public function products() {
        return $this->hasMany('App\Models\OrderFormMailTemplateProduct', 'template_id');
    }
}