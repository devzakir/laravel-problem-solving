<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model {

    protected $fillable = [
                            'type',
                            'hash',
                            'from',
                            'recipienter_id',
                            'readed',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'news';
}