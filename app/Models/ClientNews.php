<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClientNews extends Model {

    protected $fillable = [
                            'type',
                            'hash',
                            'from',
                            'user_id',
                            'readed',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'client_news';
}