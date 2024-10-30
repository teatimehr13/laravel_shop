<?php

// use App\Http\Controllers\ProfileController;
// use Illuminate\Foundation\Application;
// use Illuminate\Support\Facades\Route;
// use Inertia\Inertia;

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

Route::get('/', function () {
    return view('index');
});

Route::get('/products', function () {
    return view('/back/product');
});

Route::get('/brands', function () {
    return view('/back/brand');
});

Route::get('/cart', function () {
    return view('/front/cart');
});

// Route::get('/login-test', function () {
//     Auth::loginUsingId(1); // 使用 ID 為 1 的使用者登入
//     return 'Logged in!';
// });

Route::get('/login', function () {
    return view('/front/login');
});

// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect('/login');
// });

Route::post('/login_access', function (Request $request) {
    $credentials = $request->only('email', 'password');
    Log::info($credentials);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // 重新生成 session ID
        return response()->json(['message' => 'Logged in!'], 200);
    }
    return response()->json(['message' => 'Invalid credentials'], 401);
});


Route::post('/logout', function (Request $request) {
    Auth::logout(); // 登出使用者

    $request->session()->invalidate(); // 使當前的 session 無效
    $request->session()->regenerateToken(); // 重新生成 CSRF token
    return response()->json(['message' => 'Logged out!'], 200);
});