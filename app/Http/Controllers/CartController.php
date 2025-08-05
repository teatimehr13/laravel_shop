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
use Inertia\Inertia;


class CartController extends Controller
{
    public function index(Request $request)
    {
        // Log::info($request->user());
        $selected = session('cart.selected', []);
        Log::info($selected);

        $cartItems = $this->getCartItems($request);
        $endPrice = $this->getEndPrice($request);
        return Inertia::render('Front/Cart', [
            'cartItems' => $cartItems,
            'endPrice' => $endPrice,
            'selectedItem' => $selected,
        ]);
    }


    //加入購物車
    public function addToCart(Request $request)
    {
        $user_status = $request->user();
        if ($user_status) {
            return $this->addToDBCart($request);
        } else {
            return $this->addToCookieCart($request);
        }
    }

    public function addToDBCart(Request $request)
    {
        //來自app > Models > User.php裡面
        $cart = $request->user()->getPurchaseCartOrCreate();
        // Log::info($cart);
        $productOptions =  $request->validate([
            'quantity' => 'required|integer|min:1',
            'id' => 'required|integer|'
        ]);
        // Log::info($productOptions);

        //如果驗證 $key和product_option有關，加進購物車
        $quantity = intval($productOptions['quantity']); //integer
        $productOptionId = intval($productOptions['id']);

        session()->flash('cart.selected', [$productOptionId]);

        if ($quantity <= 0 || $productOptionId <= 0) {
            return response()->json(['msg' => '商品規格不符'], 400);
        }

        $product_option = ProductOption::findIfEnable($productOptionId);

        if (!$product_option) {
            return response()->json(['msg' => '商品不存在或未啟用'], 404);
        }

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
        return response()->json(['msg' => '加入購物車成功']);
    }

    public function addToCookieCart(Request $request)
    {
        $cookieCart = $this->getCartFromCookie();
        $productOptions =  $request->validate([
            'quantity' => 'required|integer|min:1',
            'id' => 'required|integer|'
        ]);


        $quantity = intval($productOptions['quantity']); //integer
        $productOptionId = intval($productOptions['id']);

        session()->flash('cart.selected', [$productOptionId]);

        if ($quantity <= 0 || $productOptionId <= 0) {
            return response()->json(['msg' => '商品規格不符'], 400);
        }

        $product_option = ProductOption::findIfEnable($productOptionId);

        if (!$product_option) {
            return response()->json(['msg' => '商品不存在或未啟用'], 404);
        }

        $product_option = ProductOption::findIfEnable($productOptionId);

        if ($product_option) {
            if (isset($cookieCart[$productOptionId])) {
                $cookieCart[$productOptionId] += $quantity;
            } else {
                $cookieCart[$productOptionId] = $quantity;
            }
        }

        $this->saveCookieCart($cookieCart);
        return response()->json(['msg' => '加入購物車成功']);
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

        // Log::info(Cookie::get('cart'));
    }

    //刪除購物車資料
    public function deleteCartItem(Request $request)
    {
        $user_status = $request->user();
        // Log::info($user_status);
        if ($user_status) {
            if ($this->deleteFromDBCart($request)) {
                return response()->json(['msg' => '刪除成功', 'status' => 'success']);
            } else {
                return response()->json(['msg' => '刪除失敗', 'status' => 'error']);
            }
        } else {
            if ($this->deleteFromCookieCart($request)) {
                return response()->json(['msg' => '刪除成功', 'status' => 'success']);
            } else {
                return response()->json(['msg' => '刪除失敗', 'status' => 'error']);
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
        Log::info($request->input('product_option_id'));
        if ($request->has('product_option_id')) {
            $productOptionId = intval($request->input('product_option_id'));
            Log::info($productOptionId);
            $cart = $request->user()->getPurchaseCartOrCreate();
            $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();

            if ($cartItem) {
                $cartItem->delete();
                return true;
            }
        }
    }

    //購物車結帳頁面(登入前)
    private function updateToCookieCart(Request $request)
    {
        // Log::info($request->input('data.productOptions'));

        Log::info($request->input('product_option_id'));
        Log::info($request->input('quantity'));

        if ($request->has('product_option_id') && $request->has('quantity')) {
            $quantity = intval($request->input('quantity'));
            $productOptionId = $request->input('product_option_id');

            if ($quantity > 0) {
                $cookieCart = $this->getCartFromCookie();
                $productOption = ProductOption::findIfEnable($productOptionId);
                if ($productOption) {
                    $cookieCart[$productOptionId] = $quantity;
                }

                $this->saveCookieCart($cookieCart);
            }
        }

    }

    //更新購物車(登入後)
    private function updateToDBCart(Request $request)
    {
        if ($request->has('product_option_id') && $request->has('quantity')) {
            // Log::info($request->input('product_option_id'));
            // Log::info($request->input('quantity'));
            $cart = $request->user()->getPurchaseCartOrCreate();
            $quantity = intval($request->input('quantity'));
            $productOptionId = $request->input('product_option_id');
            $product_option = ProductOption::findIfEnable($productOptionId);
        }

        if ($product_option) {
            $cartItem = $cart->cartItems()->where('product_option_id', $productOptionId)->first();
            if ($cartItem && $quantity > 0) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
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
            // Log::info($cartItemsArray);
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
                return $currentValue + intval($quantity) * $productOption->price ?? 0;
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
}
