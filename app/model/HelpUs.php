<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class HelpUs extends Model
{
	use Sluggable;
    protected $table = 'help_us';
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
