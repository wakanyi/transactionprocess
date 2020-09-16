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

    public function addRole(Request $request){
        $userRole = new Users_role();
        $userRole->userID = $request->get('userID');
        $userRole->roleID = $request->get('roleID');

        if($userRole->save()):

            $OXOResponse = new \Oxoresponse\OXOResponse("User role created successfully. Kindly check your email to verify account.");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($userRole);
            return $OXOResponse->jsonSerialize();
        else:
            $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create user role. Kindly try again later");
            $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
            $OXOResponse->setObject($userRole);
            return $OXOResponse->jsonSerialize();
        endif;

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