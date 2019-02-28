<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyGroup extends Model
{
    protected $connection = 'mysql2';
    protected $table = "company_group";
}
