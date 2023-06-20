<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderMessage extends Model {

    protected $fillable = [
                            'recipienter_id',
                            'client_id',
                            'order_id',
                            'title',
                            'message',
                            'deadline',
                            'address'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'order_messages';

    public function user() {
      return $this->belongsTo('App\User', 'client_id');
    }

    public function recipienter() {
      return $this->belongsTo('App\Models\Recipienter', 'recipienter_id');
    }

    public function order() {
      return $this->belongsTo('App\Models\OrderFormMailTemplate', 'order_id');
    }
}