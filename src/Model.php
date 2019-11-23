<?php

namespace LGrevelink\CustomQueryBuilder;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use LGrevelink\CustomQueryBuilder\Concerns\HasCustomQueryBuilder;

class Model extends EloquentModel
{
    use HasCustomQueryBuilder;
}
