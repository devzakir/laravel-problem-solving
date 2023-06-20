<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Shipping extends Model {

    protected $fillable = [
                            'status',
                            'hash',
                            'ordered_at',
                            'order_form_id',
                            'user_id',
                            'deadline',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'shippings';
}