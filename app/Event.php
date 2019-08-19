<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'mysql3';
    protected $table = "event";
}
