<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $connection = 'mysql3';
    protected $table = "partner";
}
