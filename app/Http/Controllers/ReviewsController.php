<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Hotel;
use App\Review;
use App\CacheDetail;


class ReviewsController extends Controller
{
    public function getReview($place_id){
        
        $review = new Review();
        $data = $review->getReview($place_id);
        return json_encode($data);
    }
}
