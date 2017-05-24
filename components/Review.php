<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Input;
use Flash;
use Redirect;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Review as ReviewModel;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\ReviewType;
use Octommerce\Octommerce\Models\ReviewItem;


class Review extends ComponentBase
{

    public $review;

    public function componentDetails()
    {
        return [
            'name'        => 'review Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (! Auth::check()) {
            return $this->controller->run('404');
        }

        $this->review = $review = $this->getReview();

        if (! $review) {
            return $this->controller->run('404');
        }

        $this->prepareVars();
    }

    public function prepareVars()
    {

        $this->page['reviewTypes'] = ReviewType::get();
    }

    protected function getReview()
    {
        $user = Auth::getUser();

        return ReviewModel::all();
    }

    public function onSave()
    {

        Flash::success('Review successfully added');

        $productIds = Input::get('product_ids');
        $ratings    = Input::get('ratings');
        $contents   = Input::get('contents');
        $reviewTypeIds = Input::get('review_type_ids');
            foreach ($productIds as $productId) {
            
                $review = new ReviewModel();
                
                $review->product_id =  $productId;
                $review->user_id =  Input::get('user_id');
                $review->order_id =  Input::get('order_id');
                $review->content =  $contents[$productId];
                $review->is_masked =  Input::get('is_masked');
                $review->save();


                foreach ($reviewTypeIds[$productId] as $reviewTypeId) {
             
                    $item = new ReviewItem();
                    $item->review_id = $review->id;
                    $item->rating = $ratings[$reviewTypeId][$productId];
                    $item->review_type_id = $reviewTypeId;
                    $item->save();
                     }
            }
        

    }
}
