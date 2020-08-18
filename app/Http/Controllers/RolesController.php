<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;

class RolesController extends BaseController{

    public function index(){

        $roles = Role::all();
       

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($roles);

        return $OXOResponse->jsonSerialize();
    }

    public function generate_controlno($input, $strength = 16)
    {
        //Generating random values for the control number
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
    
        return $random_string;
    }

    public function create(Request $request){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $this->validate(
            $request, [
                'role' => 'required|string',
                'publish' => 'required|string',
            ]
        );

        $role = new Role();
        $role->roleID = $this->generate_controlno($permitted_chars, 5);
        $role->role = $request->get('role');
        $role->publish = $request->get('publish');
        $role->save();

        $OXOResponse = new \Oxoresponse\OXOResponse("Role created successfully");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($role);
        return $OXOResponse->jsonSerialize();

    }

    public function update(Request $request, $roleID)
    {
        $role = Role::where(['id' => $roleID])->firstOr(function () {
            $OXOResponse = new \Oxoresponse\OXOResponse("Could not update role record");
            $OXOResponse->addErrorToList("make sure you have passed correct role ID");
            $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);

            return $OXOResponse;
        }
        );

        if($role instanceof OXOResponse)
        {
            return $role->jsonSerialize();
        }
        else
        {
            $role->role = $request->get('role');
            $role->publish = $request->get('publish');
            $role->save();

            $OXOResponse = new \Oxoresponse\OXOResponse("Role Updated Successfully");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($role);

            return $OXOResponse->jsonSerialize();

        }
    }


}