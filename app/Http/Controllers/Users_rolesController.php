<?php

namespace App\Http\Controllers;

use App\Users_role;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;

class Users_rolesController extends BaseController{

    public function index(){

        $user = Users_role::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($user);

        return $OXOResponse->jsonSerialize();
    }

    public function getUserRole(Request $request, $userID){
        $userRole = Users_role::where(['userID' => $userID])->first();


        if(!$userRole)
        {
            $OXOResponse = new \Oxoresponse\OXOResponse("User Role Does Not Exist. Kindly sign up");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($userRole);

            return $OXOResponse->jsonSerialize();
        }
        else{
            
                $OXOResponse = new \Oxoresponse\OXOResponse("User Role Exists");
                $OXOResponse->setErrorCode(8000);
                $OXOResponse->setObject($userRole);

                return $OXOResponse->jsonSerialize();
        }

    }      

}