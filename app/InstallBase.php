<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallBase extends Model
{
    protected $connection = 'mysql2';
    protected $table = "install_base";
}
