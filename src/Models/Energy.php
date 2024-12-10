<?php

namespace Eighteen73\Radioactivity\Models;

use Illuminate\Database\Eloquent\Model;

class Energy extends Model
{
    protected $fillable = ['amount'];

    public function subject()
    {
        return $this->morphTo();
    }
}
