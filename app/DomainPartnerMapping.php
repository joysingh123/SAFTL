<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainPartnerMapping extends Model
{
    protected $connection = 'mysql2';
    protected $table = "domain_partner_mapping";
}
