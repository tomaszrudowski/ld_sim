<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'population_id',
        'type'
    ];

    public function extension() {
        return $this->hasOne(ExtensionDelegationElection::class, 'election_id', 'id');
    }
}
