<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorStatistic extends Model
{
    public function setVisitorStatistic($place_id,$visited_at)
    {  
        $this->place_id = $place_id;
        $this->visited_at = $visited_at;
        $this->save();
    }

    public function getVisitorStatistics($place_id)
    {
        $currentMonth = date('m');
        return $this->where('place_id',$place_id)
        ->whereRaw('MONTH(visited_at) = ?',[$currentMonth])->get();
    }
}
