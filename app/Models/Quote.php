<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Quote extends Model {

    protected $fillable = [
                            'subject_title',
                            'publish_date',
                            'hash',
                            'email',
                            'com_name',
                            'department_name',
                            'name',
                            'recipienter_id',
                            'status',
                            'user_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'quotes';

    public function products() {
        return $this->hasMany('\App\Models\QuoteProduct', 'quote_id');
    }

    public function recipienter() {
        return $this->belongsTo('\App\Models\Recipienter', 'recipienter_id');
    }
}