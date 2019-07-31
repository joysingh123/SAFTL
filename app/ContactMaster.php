<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactMaster extends Model
{
    protected $connection = 'mysql2';
    protected $table = "contact_master";
    public $timestamps = false;
}
