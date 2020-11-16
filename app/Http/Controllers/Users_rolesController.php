<?php

namespace App\Http\Controllers;

use App\Users_role;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;
use Illuminate\Support\Facades\DB;

class Users_rolesController extends BaseController{

    public function index(){

        $user = Users_role::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($user);

        return $OXOResponse->jsonSerialize();
    }

    public function addRole(Request $request){

        $userID = $request->get('userID'); //gets the user ID from the request

        $user = Users_role::where(['userID' => $userID])->first();

        if($user){
            //update the records that have already been created

            $user->roleID = $request->get('roleID');

                if($user->save()){
                    $OXOResponse = new \Oxoresponse\OXOResponse("User role updated successfully.");
                    $OXOResponse->setErrorCode(8000);
                    $OXOResponse->setObject($user);
                    return $OXOResponse->jsonSerialize();
                }

        } else {
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
    
    
    public function userInformation_userID(Request $request){
        $userdetails = DB::table('users_roles')
                       ->join('users', 'users.id', '=' ,'users_roles.userID' )
                       ->join('roles', 'roles.id', '=', 'users_roles.roleID')
                        //->where('users.id', '=', $userID)
                        ->select('users.*','users_roles.userID as userROLEID','roles.role as rolename')
                        ->get();
//dd($userdetails);
        if ($userdetails){
            $OXOResponse = new \Oxoresponse\OXOResponse("user details");
            $OXOResponse->addErrorToList("Please check with the administrator and try again");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($userdetails);
            
            return $OXOResponse;
        }else {
            $OXOResponse = new \Oxoresponse\OXOResponse("User Does Not exist");
            $OXOResponse->addErrorToList("Please check with the administrator and try again");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            
            return $OXOResponse;
        }


}
}
