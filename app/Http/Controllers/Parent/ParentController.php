<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        return view('parent.dashboard');
    }
}
