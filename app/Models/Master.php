<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Master extends Model {

    protected $fillable = [
                            'platform_fee',
                            'min_price',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'masters';
}