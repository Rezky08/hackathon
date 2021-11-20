<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait ColumnListing{
    static public function getTableColumns()
    {
        /** @var Model $oClass */
        $oClass = new static();
        return \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing($oClass->getTable());
    }
}
