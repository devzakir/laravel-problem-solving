<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderFormMessage extends Model {

    protected $fillable = [
                            'recipienter_id',
                            'client_id',
                            'order_form_id',
                            'title',
                            'message',
                            'address',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'order_form_messages';

    public function user() {
      return $this->belongsTo('App\User', 'client_id');
    }
  
    public function recipienter() {
      return $this->belongsTo('App\Models\Recipienter', 'recipienter_id');
    }
  
    public function orderForm() {
      return $this->belongsTo('App\Models\OrderForm', 'order_form_id');
    }
}