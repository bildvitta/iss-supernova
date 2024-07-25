<?php

namespace Bildvitta\IssSupernova\Models;

use Bildvitta\IssSupernova\Traits\UsesSupernovaDB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

abstract class ModelSync extends Model
{
    use UsesSupernovaDB;
    use SoftDeletes;

    protected $connection = 'iss-supernova';

    protected $table = 'model_syncs';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    //
}
