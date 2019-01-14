<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    //
    protected $fillable = [ 'name_of_business', 'category', 'description', 'user_id', 'business_image'];

    //relationship one to one  business has one owner
    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
