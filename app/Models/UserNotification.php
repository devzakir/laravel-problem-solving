<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserNotification extends Model {

    protected $fillable = [
                            'category',
                            'hash',
                            'target_name',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'user_notifications';
}