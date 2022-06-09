<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itemajax extends Model
{
    //
    use SoftDeletes;

    protected $table = 'itemajaxes';

    protected $fillable = ['id', 'item_name', 'descriptions', 'manufacture_date', 'images'];
}
