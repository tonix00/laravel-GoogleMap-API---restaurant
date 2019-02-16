<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VisitorStatistic;

class VisitorsStatisticController extends Controller
{
    public function vistors($place_id)
    {
        $lastday = (int)date('t',strtotime('today'));
        $daysOfTheMonth = array();
        for($day=1;$day<=$lastday;$day++){
            $daysOfTheMonth[$day] = 0;
        }

        $visitorModel = new VisitorStatistic();
        $visitors = $visitorModel->getVisitorStatistics($place_id);
        foreach($visitors as $i => $visitor)
        {
            $day = (int)date('j', strtotime($visitor->visited_at));
            $daysOfTheMonth[$day]++;
        }
        echo json_encode($daysOfTheMonth);
    }

    public function saveStatistics($place_id)
    {
        $visited_at = "";
        if(isset($_GET['v']) && !empty($_GET['v'])){
            $visited_at = $_GET['v'];
        }  
        $visitorModel = new VisitorStatistic();
        $visitorModel->setVisitorStatistic($place_id,$visited_at);
    }
}
