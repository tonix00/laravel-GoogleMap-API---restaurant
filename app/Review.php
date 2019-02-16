<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use \GuzzleHttp\Client;
use App\Hotel;
use App\CacheDetail;

class Review extends Model
{
    public function getReview($place_id){

        $returnArray = array();
        $returnArray['place_id'] = $place_id;

        // get cache detail
        $cacheDetail = new cacheDetail();
        $cache = $cacheDetail->getCacheDetail($place_id);

        if($cache){
            $returnArray['name'] = $cache['name'];
            $returnArray['formatted_address']  = $cache['formatted_address'];
        }
        
        // get hotel more info
        $hotel = new Hotel();
        $isHotelExist = $hotel->isExist($place_id);

        if( $isHotelExist == null){

            $apiUrl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=";
            $apiUrl = $apiUrl . $place_id . "&fields=name,rating,formatted_phone_number,reviews&key=";
            $apiUrl = $apiUrl .  config('googleapikey.GoogleAPIKey');

            // get data from google
            $client = new Client(['verify' => false ]);
            $res = $client->get($apiUrl);
            $content = (string) $res->getBody();
            $infos = json_decode($content,true);

            $data = $infos['result'];
            $data['place_id'] = $place_id;
            $hotel_id = $hotel->setHotel($data);

            // set return data
            $returnArray['formatted_phone_number'] = "(none)";
            if(isset($data['formatted_phone_number']))
                $returnArray['formatted_phone_number'] = $data['formatted_phone_number'];

            $returnArray['rating'] = 0;
            if(isset($data['rating']))
                $returnArray['rating'] = $data['rating'];

            $returnArray['reviews'] = array();

            foreach($data['reviews'] as $review){        
                $data = $review;
                $data['hotel_id'] = $hotel_id;
                $returnArray['reviews'][] = $data;
                $this->setReview($data);
            }

        }else{

            $data = $hotel->getHotel($place_id);
            $returnArray['formatted_phone_number'] = $data['formatted_phone_number'];
            $returnArray['rating'] = $data['rating'];

            //get reviews
            $returnArray['reviews']  = $this->getUserReviews($isHotelExist->id)->toArray();  
        }  
        
        return $returnArray;
    }

    public function setReview($data){
        $review = App::make(Review::class);
        $review->author_name = $data['author_name'];
        $review->profile_photo_url = $data['profile_photo_url'];
        $review->rating = $data['rating'];
        $review->text = $data['text'];
        $review->hotel_id = $data['hotel_id'];
        $review->save();
    }

    public function getUserReviews($hotel_id){
        return $this->where('hotel_id', $hotel_id)->get();
    }

}
