<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model {

    protected $fillable = [
                            'status',
                            'hash',
                            'ordered_at',
                            'user_id',
                            'order_form_id',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'orders';
}