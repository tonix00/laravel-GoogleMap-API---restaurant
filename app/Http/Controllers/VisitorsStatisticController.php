<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VisitorStatistic;

class VisitorsStatisticController extends Controller
{
    public function vistors($lat,$lng)
    {
        $restaurantid = $this->createID($lat,$lng);
        $lastday = (int)date('t',strtotime('today'));
        $daysOfTheMonth = array();
        for($day=1;$day<=$lastday;$day++){
            $daysOfTheMonth[$day] = 0;
        }

        $visitors = $this->getVisitorStatistics($restaurantid);
        foreach($visitors as $i => $visitor)
        {
            $day = (int)date('j', strtotime($visitor->visited_at));
            $daysOfTheMonth[$day]++;
        }
        echo json_encode($daysOfTheMonth);
    }

    public function saveStatistics($lat,$lng)
    {
        $visited_at = "";
        if(isset($_GET['v']) && !empty($_GET['v'])){
            $visited_at = $_GET['v'];
        }  
        $restaurantid = $this->createID($lat,$lng);
        $this->setVisitorStatistic($restaurantid,$visited_at);
    }

    private function createID($lat,$lng)
    {
        $name = "";
        if(isset($_GET['n']) && !empty($_GET['n'])){
            $name = $_GET['n'];
        }
        return md5($lat.$lng.$name);
    }

    private function setVisitorStatistic($restaurantid,$visited_at)
    {
        $visitorStatistic = new VisitorStatistic();
        $visitorStatistic->restaurantid = $restaurantid;
        $visitorStatistic->visited_at = $visited_at;
        $visitorStatistic->save();
    }

    private function getVisitorStatistics($restaurantid)
    {
        $currentMonth = date('m');
        return VisitorStatistic::where('restaurantid',$restaurantid)
        ->whereRaw('MONTH(visited_at) = ?',[$currentMonth])->get();
    }

}
