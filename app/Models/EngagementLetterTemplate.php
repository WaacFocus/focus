<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngagementLetterTemplate extends Model
{
    protected $fillable = ['title', 'service_type', 'body', 'sort_order', 'is_active'];
}
