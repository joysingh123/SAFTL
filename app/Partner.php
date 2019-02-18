<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $connection = 'mysql2';
    protected $table = "partner";
}
