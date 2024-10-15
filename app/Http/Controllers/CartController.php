<?php

namespace App\Http\Controllers;

use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    //加入購物車
    public function addToCart(Request $request)
    {
        $user_status = $request->user();
        if ($user_status) {
            $this->addToDBCart($request);
        } else {
            // return response()->json(['test'=>'test']);
            $this->addToCookieCart($request);
        }
    }

    //登入後存資料庫
    public function addToDBCart(Request $request)
    {
        //來自app > Models > User.php裡面
        // $cart = $request->user()->getPurchaseCartOrCreate();

        // foreach ($request->input() as $key => $value) {
        //     //如果驗證 $key和product_option有關，加進購物車
        //     if (preg_match('/^product_option_[0-9]+$/', $key)) {
        //         $quantity = intval($value); //integer
        //         $productOptionId = intval(str_replace('product_option_', '', $key));
        //         if ($quantity && $productOptionId) {
        //             //驗證商品的狀態是什麼? 草稿? enabled?
        //             $product_option = ProductOption::findIfEnabled($productOptionId);
        //             if ($product_option) {
        //                 //eloquent模型方法
        //                 $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();
        //                 if ($cartItem) {
        //                     //存在的話，save資料庫
        //                     $cartItem->quantity += $quantity;
        //                     $cartItem->save();
        //                 } else {
        //                     //不存在的話新建一筆進資料庫
        //                     $cart->cartItems()->save(
        //                         new CartItem([
        //                             'product_option_id' => $productOptionId,
        //                             'quantity' => $quantity
        //                         ])
        //                     );
        //                 }
        //             }
        //         }
        //     }
        // }
    }

    //登入前存cookie
    public function addToCookieCart(Request $request)
    {
        $cookieCart = $this->getCartFromCookie();
        $productOptions = $request->input('data.productOptions');

        foreach ($productOptions as $key => $value) {
            if (preg_match('/^product_option_[0-9]+$/', $key)) {
                $quantity = intval($value);
                $productOptionId = intval(str_replace("product_option_", '', $key));
                if ($quantity && $productOptionId) {
                    $product_option = ProductOption::findIfEnable($productOptionId);
                    // Log::info($product_option);
                    if ($product_option) {
                        if (isset($cookieCart[$productOptionId])) {
                            $cookieCart[$productOptionId] += $quantity;
                        } else {
                            $cookieCart[$productOptionId] = $quantity;
                        }
                    }
                }
            }
        }
        $this->saveCookieCart($cookieCart);
    }

    //拿到cookie
    public function getCartFromCookie()
    {
        $jsonCart = Cookie::get('cart');
        return (!is_null($jsonCart)) ? json_decode($jsonCart, true) : [];
    }

    //寫入cookie
    private function saveCookieCart($cookieCart)
    {
        $cartToJson = empty($cookieCart) ? "{}" : json_encode($cookieCart);
        Cookie::queue(
            Cookie::make('cart', $cartToJson, 60 * 24 * 7, null, null, false, false)
        );

        Log::info(json_encode($cookieCart));
    }

    //刪除購物車資料
    public function deleteCartItem(Request $request)
    {
        $user_status = $request->user();
        if ($user_status) {
            if ($this->deleteFromDBCart($request)) {
                return response('success');
            } else {
                return response('failed');
            }
        } else {
            if ($this->deleteFromCookieCart($request)) {
                return response('success');
            } else {
                return response('failed');
            }
        }
    }

    //刪除cookie
    private function deleteFromCookieCart(Request $request)
    {
        // Log::info($request->input());
        if ($request->has('product_option_id')) {
            $productOptionId = intval($request->input('product_option_id'));
            $cookieCart = $this->getCartFromCookie();

            if (isset($cookieCart[$productOptionId])) {
                unset($cookieCart[$productOptionId]);
                $this->saveCookieCart($cookieCart);
                return true;
            }
        }
        return false;
    }

    //刪除資料庫
    private function deleteFromDBCart(Request $request)
    {
        if ($request->has('product_option_id')) {
            $productOptionId = intval($request->input('product_option_id'));
            $cart = $request->user()->getPurchaseCartOrCreate();
            $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();

            if ($cartItem) {
                $cartItem->delete();
                return true;
            }
        }
        return false;
    }
}
