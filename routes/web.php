<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return Inertia::render('Home',[
        'username' => 'Chagy'
    ]);
});

Route::get('/users',function(){
    return Inertia::render('Users/Index',[
        'users' => User::query()
            ->when(Request::input('search'),function ($query,$search){
                $query->where('name','like',"%{$search}%");
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name
            ]),
        'filters' => Request::only(['search']),
        'can' => [
            'createUser' => Auth::user()->can('create',User::class)
        ]
    ]);
});

Route::get('/users/create',function() {
    return Inertia::render('Users/Create');
})->middleware('can:create,App\Models\User');

Route::post('/users/create',function() {

    $attributes = Request::validate([
        'name' => 'required',
        'email' => ['required','email'],
        'password' => 'required',
    ]);

    User::create($attributes);

    return redirect('/users');
});

Route::get('/settings',function(){
    return Inertia::render('Settings');
});

Route::post('/logout', function(){
    dd(request('foo'));
});
