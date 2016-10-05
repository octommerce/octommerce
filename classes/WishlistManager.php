<?php namespace Octommerce\Octommerce\Classes;

use Auth;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\WishList;

/**
* Wishlist manager
*/
class WishlistManager
{
	use \October\Rain\Support\Traits\Singleton;

	protected function __construct()
	{
		$this->init();	
	}

	private function init()
	{

	}

	/**
	 * Show all user wishlist
	 * @param  int $userId
	 * @return Collection
	 */
	public function showUserWishlist($userId)
	{
		$userWishlist = WishList::whereUserId($userId)->with('product', 'user')->get();

		return $userWishlist;
	}

	public function add($productSKU)
	{
		$product = Product::where('sku', $productSKU)->first();

		$wishlist = new WishList;
		$wishlist->product_id = $product->id;
		$wishlist->user_id    = Auth::getUser()->id;
		$wishlist->save();

		return $product;
	}

	public function remove($wishlistId)
	{
		$wishlist = WishList::find($wishlistId);

		$wishlist->delete();	

		return $wishlist;
	}

}