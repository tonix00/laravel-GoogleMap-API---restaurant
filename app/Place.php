<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public function getPlaces()
    {
        // retrieve data and format data
        $places = $this->all();

        //format cache
        $toJson = array();
        foreach($places as $place)
        {
            $arr = array();
            $arr["formatted_address"]= $place->formatted_address;
            $arr["lat"]= $place->lat;
            $arr["lng"]= $place->lng;
            $arr["id"]= $place->id;
            $toJson[] = $arr;
        }

        // return json
        return json_encode($toJson);
    }
}
