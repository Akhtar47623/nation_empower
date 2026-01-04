<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;


class Service extends Model
{
use Sluggable;
    protected $table = 'services';
    protected $guarded = [];
     public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
