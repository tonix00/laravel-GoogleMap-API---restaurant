<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Food;

class FoodsController extends Controller
{
    
    public function foods($place_id)
    {
        $foodModel = new Food();
        $foods = $foodModel->getFoods($place_id);

        $toJson = array();
	    $toJson[]= 'dummy food';
        foreach($foods as $i => $food)
        {
            $toJson[]= $food->food;
        }
        $x = json_encode($toJson);
        ob_start();
        echo $x;
        flush();
    }

    public function saveFood($place_id)
    {
        $cusfood = "";
        if(isset($_GET['f']) && !empty($_GET['f'])){
            $cusfood = $_GET['f'];
        }  

        // check food exist
        $food = Food::where('place_id', $place_id)
            ->where('Food', $cusfood)
            ->first();

        // if not - save
        if($food==null){
            $foodModel = new Food();
            $foodModel->setFood($cusfood,$place_id);
        }
    }

}
