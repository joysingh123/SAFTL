<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesbotContacts extends Model
{
    protected $connection = 'mysql2';
    protected $table = "contacts";
}
