<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



class PinController extends Controller
{
    public function show(Request $request)
    {
        return view('auth.pin', ['intended_url' => $request->query('url')]);
    }

    public function verify(Request $request)
{
    $request->validate([
        'pin' => 'required|numeric|digits:4',
        'intended_url' => 'required'
    ]);

    $user = auth()->user();

    // Comparamos el PIN ingresado con el Hash de la base de datos
    if (Hash::check($request->pin, $user->pin)) {
        
        session(['pin_confirmed' => true]);

        return redirect($request->intended_url);
    }

    return back()->withErrors(['pin' => 'El PIN es incorrecto.']);
}
}