<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    public function getHotel($place_id){
        return $this->where('place_id', $place_id)->first();
    }

    public function setHotel($data){
        $exist = $this->isExist($data['place_id']);
        if($exist == false){
            $this->formatted_phone_number = '(none)';
            if(isset($data['formatted_phone_number']))
                $this->formatted_phone_number = $data['formatted_phone_number'];
            
            $this->rating = 0; 
            if(isset($data['rating']))
                $this->rating = $data['rating'];
            
            $this->place_id = $data['place_id'];
            $this->save();
            return $this->id;
        }else{
            return $exist->id;
        }
    }

    public function isExist($place_id){
        return $this->where('place_id', $place_id)->first();    
    }
}
