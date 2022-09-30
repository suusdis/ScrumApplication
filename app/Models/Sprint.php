<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    use HasFactory;

    public function stories()
    {
      return $this->hasMany(Story::class);
    }

    public function project()
    {
      return $this->belongsTo(Project::class);
    }

    public function review()
    {
      return $this->hasOne(Review::class);
    }

    public function retrospectives()
    {
      return $this->hasMany(Retrospective::class);
    }
}
