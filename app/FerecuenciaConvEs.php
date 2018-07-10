<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class FerecuenciaConvEs extends Model implements AuditableContract
{
    //
    use Auditable;
}
