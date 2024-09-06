<?php

namespace App\Http\Controllers\Parent;

use App\Models\Parents;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\StudentsParent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }
        $parent = Parents::where('user_id',$this->authService->user()->id)->first();
        $totalchildrens = StudentsParent::where('parent_id',$parent->id)->count();

        return view('parent.dashboard',[
        'totalchildrens'=>$totalchildrens
        ]);
    }

    public function profile(){
        $parent = Parents::with('user')->where('user_id',$this->authService->user()->id)->first();
        
        return view('parent.profile.profile',[
            'parent'=>$parent
        ]);
    }
   
}
