<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class Event extends Model
{
	use Sluggable;
    protected $table = 'events';
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

