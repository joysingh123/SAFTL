<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallBase extends Model
{
    protected $connection = 'mysql3';
    protected $table = "install_base";
}
