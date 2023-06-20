<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model {

    protected $fillable = [
                            'is_public',
                            'hash',
                            'price',
                            'until',
                            'user_id',
                            'shipping_id',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'invoices';
}