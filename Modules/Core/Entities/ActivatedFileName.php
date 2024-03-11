<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivatedFileName extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $table = "psx_activated_file_names";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\ActivatedFileNameFactory::new();
    }
}
