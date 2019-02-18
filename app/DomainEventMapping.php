<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainEventMapping extends Model
{
    protected $connection = 'mysql2';
    protected $table = "domain_event_mapping";
}
