<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Login\RememberMeExpiration;
use AuthenticatesUsers;

class LoginController extends Controller{
    use RememberMeExpiration;

    /**
     * Display login page.
     * 
     * @return Renderable
     */
    
    public function show(){
        return view('auth.login');
    }

    public function login(LoginRequest $request){
        $credentials = $request->getCredentials();
        if(!Auth::validate($credentials)):
            return redirect()->to('login')->withErrors(trans('auth.failed'));
        endif;

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        Auth::login($user, $request->get('remember'));
        if($request->get('remember')):
            $this->setRememberMeExpiration($user);
        endif;
        return $this->authenticated($request, $user);
    }

    protected function authenticated() {
        if (Auth::check()) {
            return redirect('/home');
        }else{
            return redirect('/login');
        }
    }

}