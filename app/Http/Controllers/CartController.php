<?php

namespace App\Http\Controllers;

use App\Http\Resources\Front\CartItemResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // Log::info($request->user());
        $cartItems = $this->getCartItems($request);
        $endPrice = $this->getEndPrice($request);
        return response()->json(
            [
                'cartItems' => CartItemResource::collection($cartItems),
                'endPrice' => $endPrice
            ]
        );

        // $filteredCartItems = array_map(function ($item) {
        //     return [
        //         'productOption' => [
        //             'id' => $item['productOption']['id'],
        //             'name' => $item['productOption']['name'],
        //             'price' => $item['productOption']['price'],
        //             'image' => $item['productOption']['image'],
        //             'description' => $item['productOption']['description'],
        //         ],
        //         'quantity' => $item['quantity'],
        //     ];
        // }, $cartItems);
        // return response()->json($filteredCartItems);
    }


    //加入購物車
    public function addToCart(Request $request)
    {
        $user_status = $request->user();
        if ($user_status) {
            $this->addToDBCart($request);
        } else {
            $this->addToCookieCart($request);
        }
    }

    //登入後存資料庫
    public function addToDBCart(Request $request)
    {
        //來自app > Models > User.php裡面
        $cart = $request->user()->getPurchaseCartOrCreate();
        $productOptions = $request->input('data.productOptions');

        foreach ($productOptions as $key => $value) {
            //如果驗證 $key和product_option有關，加進購物車
            if (preg_match('/^product_option_[0-9]+$/', $key)) {
                $quantity = intval($value); //integer
                $productOptionId = intval(str_replace('product_option_', '', $key));
                if ($quantity && $productOptionId) {
                    //驗證商品的狀態是什麼? 草稿? enabled?
                    $product_option = ProductOption::findIfEnable($productOptionId);
                    if ($product_option) {
                        //eloquent模型方法
                        $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();
                        if ($cartItem) {
                            //存在的話，save資料庫
                            $cartItem->quantity += $quantity;
                            $cartItem->save();
                        } else {
                            //不存在的話新建一筆進資料庫
                            $cart->cartItems()->save(
                                new CartItem([
                                    'product_option_id' => $productOptionId,
                                    'quantity' => $quantity
                                ])
                            );
                        }
                    }
                }
            }
        }
    }

    //登入前存cookie (加入購物車的頁面)
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
        // Log::info($jsonCart);
        return (!is_null($jsonCart)) ? json_decode($jsonCart, true) : [];
    }

    //寫入cookie
    private function saveCookieCart($cookieCart)
    {
        $cartToJson = empty($cookieCart) ? "{}" : json_encode($cookieCart);
        Cookie::queue(
            Cookie::make('cart', $cartToJson, 60 * 24 * 7, null, null, false, false)
        );
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

    //更新購物車
    public function updateCartItem(Request $request)
    {
        $user_status = $request->user();

        if ($user_status) {
            $this->updateToDBCart($request);
        } else {
            $this->updateToCookieCart($request);
        }
        // if (empty($request->header('referer'))) {
        //     return redirect()->route('cart.index');
        // } else {
        //     return redirect()->back();
        // }
    }

    //結帳
    public function checkout(Request $request)
    {
        return $this->createOrderByCart($request);
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

    //購物車結帳頁面(登入前)
    private function updateToCookieCart(Request $request)
    {
        // Log::info($request->input('data.productOptions'));

        if ($request->input('data.productOptions')) {
            $productOptions = $request->input('data.productOptions');

            if (is_array($productOptions)) {
                $cookieCart = [];
                foreach ($productOptions as $productOptionId => $value) {
                    if (isset($value['quantity'])) {
                        $quantity = intval($value['quantity']);
                        if ($quantity > 0) {
                            $productOption = ProductOption::findIfEnable($productOptionId);

                            if ($productOption) {
                                $cookieCart[$productOptionId] = $quantity;
                            }
                        }
                    }
                }
                // Log::info($cookieCart);
                $this->saveCookieCart($cookieCart);
            }
        }
    }

    //更新購物車(登入後)
    private function updateToDBCart(Request $request)
    {
        if ($request->has('data.productOptions')) {
            $product_options = $request->input('data.productOptions');

            if (is_array($product_options)) {
                $cart = $request->user()->getPurchaseCartOrCreate();
                foreach ($product_options as $productOptionId => $value) {
                    if (isset($value['quantity'])) {
                        $quantity = intval($value['quantity']);
                        $product_option = ProductOption::findIfEnable($productOptionId);
                        if ($product_option) {
                            $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();
                            if ($cartItem) {
                                $cartItem->quantity = $quantity;
                                $cartItem->save();
                            }
                        }
                    }
                }
            }
        }
    }

    //拿到購物車資料
    private function getCartItems(Request $request)
    {
        $user_status = $request->user();
        if ($user_status) {
            $this->syncCookieCartToDBCart($user_status);

            //經由user找到cart再找到cartItem的carItems
            $cartItems = $user_status->getPurchaseCartOrCreate()->cartItems;
            // Log::info($user_status);
            $cartItemsAry = [];
            foreach ($cartItems as $cartItem) {
                $productOption = ProductOption::findIfEnable($cartItem->product_option_id);
                if ($productOption && $cartItem->quantity > 0) {
                    array_push($cartItemsAry, [
                        "productOption" => $productOption,
                        "quantity" => $cartItem->quantity
                    ]);
                } else {
                    $cartItem->delete();
                }
            }
            return $cartItemsAry;
        } else {
            $cookieCart = $this->getCartFromCookie();
            $cartItemsArray = [];
            foreach ($cookieCart as $productOptionId => $quantity) {
                $productOption = ProductOption::findIfEnable($productOptionId);
                if ($productOption) {
                    $cartItem = [
                        "productOption" => $productOption,
                        "quantity" => $quantity
                    ];
                    array_push($cartItemsArray, $cartItem);
                }
            }
            return $cartItemsArray;
        }
    }

    //拿到購物車全部的金額
    private function getEndPrice(Request $request)
    {
        return array_reduce(
            $this->getCartItems($request),
            function ($currentValue, $cartItemObj) {
                $productOption = $cartItemObj["productOption"];
                $quantity = $cartItemObj["quantity"];
                return $currentValue = intval($quantity) * $productOption->price;
            },
            0
        );
    }

    //登入後將cookie清空，將資料移到資料庫 (cart_items)
    private function syncCookieCartToDBCart(User $user)
    {
        if ($user) {
            $cookieCart = $this->getCartFromCookie();
            $cart = $user->getPurchaseCartOrCreate();
            foreach ($cookieCart as $productOptionId => $quantity) {
                $productOption = ProductOption::findIfEnable($productOptionId);
                if ($productOption) {
                    $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();
                    if ($cartItem) {
                        $cartItem->quantity += $quantity;
                        $cartItem->save();
                    } else {
                        $cart->cartItems()->save(
                            new CartItem([
                                'product_option_id' => $productOptionId,
                                'quantity' => $quantity
                            ])
                        );
                    }
                }
            }
            //清空cookieCart
            $this->saveCookieCart([]);
        }
    }

    //生成訂單
    private function createOrderByCart(Request $request){
        $user_status = $request->user();
        // Log::info($user_status);
        $cart = $user_status->getPurchaseCartOrCreate();
        $amount = $this->getEndPrice($request);

        $order = Order::create([
            'amount' => $amount,
            'address' => 'testing...address',
            'user_id' => $user_status->id
        ]);

        //利用order找到order items去存儲資料
        $order->orderItems()->saveMany($cart->cartItems->map(function($cartItem){
            return new OrderItem([
                'name' => $cartItem->productOption->name,
                'price' => $cartItem->productOption->price,
                'quantity' => $cartItem->quantity,
                'product_option_id' => $cartItem->product_option_id
            ]);
        }));

        //購物車變成訂單後，刪除
        // $cart->cartItems()->delete();
    }
}
