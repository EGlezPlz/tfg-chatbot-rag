<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/dashboard'));

Route::get('/login', fn() => view('login'));

Route::post('/login', function() {
    if (request('password') === 'admin') {
        return redirect('/dashboard')->withCookie(
            cookie()->forever('venancia_auth', 'true')
        );
    }
    return back()->with('error', 'Credenciales incorrectas');
});

Route::get('/dashboard', function() {
    if (request()->cookie('venancia_auth') !== 'true') return redirect('/login');
    return view('dashboard');
});

Route::get('/corpus', function() {
    if (request()->cookie('venancia_auth') !== 'true') return redirect('/login');
    return view('corpus');
});

Route::post('/logout', function() {
    return redirect('/login')->withoutCookie('venancia_auth');
});
