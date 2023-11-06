<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

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
    return view('welcome');
});
Route::get('/test',function(){
    return Inertia::render('Test');
});
Route::get('login',function(){
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('create-role',function(){
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'society_admin']);
    Role::create(['name' => 'society_cashier']);
    Role::create(['name' => 'gatekeeper']);
    echo "success";
});
