<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactMaster extends Model
{
    protected $connection = 'mysql3';
    protected $table = "contact_master";
    public $timestamps = false;
}
