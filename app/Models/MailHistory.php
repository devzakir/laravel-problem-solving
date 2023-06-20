<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MailHistory extends Model {

    protected $fillable = [
                            'title',
                            'user_id',
                            'recipienter_id',
                            'mail_type',
                            'message',
                            'deadline',
                            'address',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'mail_histories';

    public function user() {
      return $this->belongsTo('App\User', 'user_id');
    }
  
    public function recipienter() {
      return $this->belongsTo('App\Models\Recipienter', 'recipienter_id');
    }
}