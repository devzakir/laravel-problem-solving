<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Permission extends Model {

    protected $fillable = [
                            'recipienter_id',
                            'show',
                            'control',
                            'moduleNum'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'permissions';
}