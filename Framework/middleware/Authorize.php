<?php 

namespace Framework\Middleware; 

use Framework\Session;

class Authorize
{
/**
 * check if user authenticated
 * 
 * @return bool
 * 
 */
public function isAuthenticated(){
    return Session::has('user');
}


   /**
    * Handle the user request.
    *@param  string  $role
    *@return bool
    */

    public function handle($role = null){
        if($role === 'guest' && $this->isAuthenticated()){
            return redirect(url(''));
    }elseif ($role === 'auth' && !$this->isAuthenticated()){
        return redirect(url('auth/login'));
    }
}   
}