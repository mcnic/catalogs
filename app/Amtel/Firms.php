<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;
use DB;

class Firms extends Model
{
    protected $table = 'firms';
    protected $fillable = [
        'id', 'title'
    ];
    public $timestamps = false;
}
