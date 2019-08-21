<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyGroup extends Model
{
    protected $connection = 'mysql3';
    protected $table = "company_group";
}
