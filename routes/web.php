<?php
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Setting;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\SettingCollection;
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
Route::get('/', 'BlogController@index');

Route::group(['middleware' => 'guest'], function () {
	Route::get('register', function () {
		return view('Auth.register');
	});
	Route::get('login', function () {
		return view('Auth.login');
	})->name('login');
});

Route::group(['middleware' => 'auth'], function () {
	Route::get('logout', function (Request $request) {
		if (!$request->token) {
			return redirect('logout/?token='.session('token'));
		}
		Auth::logout();
		Auth::guard('web')->logout();
		return redirect('login');

	});
});

Route::group(['middleware' => 'auths', 'prefix' =>'al-admin'], function () {
	Route::get('/', function () {
		return view('Admin.index');
	});
	Route::get('/settings', function () {
		return view('Admin.settings');
	});
	Route::resource('user', 'UserController');
	Route::resource('post', 'PostController');
});

Route::get('/test', 'API\SettingController@test');

Route::group(['prefix' => 'uploads', 'middleware' => ['web', 'auth']], function () {
	\UniSharp\LaravelFilemanager\Lfm::routes();
});