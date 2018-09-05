<?php

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use LogsActivity;
    use EloquentJoin;
    protected static $logAttributes = '*';
    protected static $logName = 'Dana App Transactions';

    public function getDescriptionForEvent($eventName)
    {
        $name = Auth::guard('api')->user()? Auth::guard('api')->user()->name: "Laravel Seeder";
        return "This model has been {$eventName} by ". $name;
    }
}
