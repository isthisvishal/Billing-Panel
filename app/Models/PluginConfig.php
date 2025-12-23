<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PluginConfig extends Model
{
    use HasFactory;

    protected $fillable = ['plugin_id', 'key', 'value', 'encrypted'];
}
