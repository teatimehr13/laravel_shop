<?php

// use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
// use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/products', function () {
//     return view('/back/product');
// });

// Route::get('/brands', function () {
//     return view('/back/brand');
// });

// Route::get('/cart', function () {
//     return view('/front/cart');
// });

// Route::get('/login-test', function () {
//     Auth::loginUsingId(1); // 使用 ID 為 1 的使用者登入
//     return 'Logged in!';
// });

// Route::get('/login', function () {
//     return view('/front/login');
// })->name('front.login');

// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect('/login');
// });




// Route::post('/logout', function (Request $request) {
//     Auth::logout(); // 登出使用者

//     $request->session()->invalidate(); // 使當前的 session 無效
//     $request->session()->regenerateToken(); // 重新生成 CSRF token
//     return response()->json(['message' => 'Logged out!'], 200);
// });


// Route::get('/register', function(Request $request){
//     return view('/front/register');
// });


Route::get('/register', function () {
    // Log::info('Register route hit');
    return Inertia::render('Auth/Register'); // Make sure the path matches your component
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/cart', function () {
    return Inertia::render('Front/Cart');
})->name('cart');

Route::get('/products', function () {
    return Inertia::render('Back/Product');
})->name('products');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');;