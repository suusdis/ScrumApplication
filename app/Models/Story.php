<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'backlog_items';

    public function sprint()
    {
      return $this->belongsTo(Sprint::class);
    }

    public function project()
    {
      return $this->belongsTo(Project::class);
    }

    public function state()
    {
      return $this->belongsTo(State::class);
    }


}
