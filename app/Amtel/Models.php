<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $fillable = [
        'id', 'firm', 'title', 'group', 'img', 'img_local'
    ];
}
