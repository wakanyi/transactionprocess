<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification  extends Model 
{
    
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'notifications';

    protected $fillable = [
        'userID', 'notification', 'is_read', 'role',
    ];

   
}
