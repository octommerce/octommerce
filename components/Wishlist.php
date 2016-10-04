<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Cms\Classes\Page;
use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Classes\WishlistManager;


class Wishlist extends ComponentBase
{

    public $userWishlist;

    public $wishlistManager;

    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        parent::__construct($cmsObject, $properties);

        $this->wishlistManager = WishlistManager::instance();
    }

    public function componentDetails()
    {
        return [
            'name'        => 'Wishlist Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->page['userWishlist'] = $this->userWishlist = Auth::check() ? $this->wishlistManager->showUserWishlist(Auth::getUser()->id) : []; 
    }

    public function onAddWishlist()
    {
        if(Auth::check()) {
            $wishlist = $this->wishlistManager->add(post('productSKU')); 
            return "You have add ".$wishlist->name." into your wishlist.";
        } else {
            throw new \ApplicationException("Sorry, you should be logged in first if you want to fill your wishlist.");
        }
    } 

    public function onRemoveWishlist()
    {
       if(Auth::check()) {
            $wishlist = $this->wishlistManager->remove(post('wishlistId'));
            return "The wishlist has been removed.";
       }
    }

}