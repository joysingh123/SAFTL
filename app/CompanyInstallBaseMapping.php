<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyInstallBaseMapping extends Model
{
    protected $connection = 'mysql2';
    protected $table = "company_install_base_mapping";
}
