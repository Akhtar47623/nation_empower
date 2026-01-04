<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote_info extends Model
{
    use SoftDeletes;
    protected $table = 'quote_requests';
    protected $guarded = [];
}
