<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyMaster extends Model
{
    protected $connection = 'mysql2';
    protected $table = "company_master";
    public $timestamps = false;
}
