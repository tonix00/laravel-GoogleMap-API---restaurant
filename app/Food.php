<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    public function setFood($food,$place_id)
    {
        $this->food = $food;
        $this->place_id = $place_id;
        $this->save();
    }

    public function getFoods($place_id)
    {
        return $this->where('place_id',$place_id)->get();
    }
}
