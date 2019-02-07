<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Food;

class FoodsController extends Controller
{
    private $data = array();

    public function __construct()
    {
        $this->setParam(); 
    }
    
    private function setParam()
    {
        foreach($_REQUEST as $key => $value)
        {
            $this->data[$key] = $value;
        }
    }

    public function foods($lat,$lng)
    {
        $restaurantid = $this->createID($lat,$lng);
        $foods = $this->getFoods($restaurantid);

        $toJson = array();
        foreach($foods as $i => $food)
        {
            $toJson[]= $food->food;
        }
        echo json_encode($toJson);
    }

    public function saveFood($lat,$lng)
    {
        $cusfood = "";
        if(isset($this->data['f']) && !empty($this->data['f'])){
            $cusfood = $this->data['f'];
        }  
        $restaurantid = $this->createID($lat,$lng);

        // check food exist
        $food = Food::where('restaurantid', $restaurantid)
            ->where('Food', $cusfood)
            ->first();

        // if not - save
        if($food==null){
            $this->setFood($restaurantid,$cusfood);
        }
        
    }

    private function createID($lat,$lng)
    {
        $name = "";
        if(isset($this->data['n']) && !empty($this->data['n'])){
            $name = $this->data['n'];
        }
        return md5($lat.$lng.$name);
    }

    private function setFood($restaurantid,$cusfood)
    {
        $food = new Food();
        $food->restaurantid = $restaurantid;
        $food->food = $cusfood;
        $food->save();
    }

    private function getFoods($restaurantid)
    {
        return Food::where('restaurantid',$restaurantid)->get();
    }

    public function __set( $key, $value ){
		if($key && $value){
			$this->data[$key] = $value;
		}
	}

	public function __get( $key ){
		if(isset($this->data[$key]) && $this->data[$key]){
			return $this->data[$key];
		}else{
			return "";
		}
	}
}
