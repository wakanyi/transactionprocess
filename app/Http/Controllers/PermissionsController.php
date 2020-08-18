<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;

class PermissionsController extends BaseController{

    public function index(){

        $permission = Permission::all();
       

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($permission);

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
                'permission' => 'required|string',
                'publish' => 'required|string',
            ]
        );

        $permission = new Permission();
        $permission->permID = $this->generate_controlno($permitted_chars, 5);
        $permission->permission = $request->get('permission');
        $permission->publish = $request->get('publish');
        $permission->save();

        $OXOResponse = new \Oxoresponse\OXOResponse("Permission created successfully");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($permission);
        return $OXOResponse->jsonSerialize();

    }

    public function update(Request $request, $permID)
    {
        $permission = Permission::where(['id' => $permID])->firstOr(function () {
            $OXOResponse = new \Oxoresponse\OXOResponse("Could not update permission record");
            $OXOResponse->addErrorToList("make sure you have passed correct permission ID");
            $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);

            return $OXOResponse;
        }
        );

        if($permission instanceof OXOResponse)
        {
            return $permission->jsonSerialize();
        }
        else
        {
            $permission->permission = $request->get('permission');
            $permission->publish = $request->get('publish');
            $permission->save();

            $OXOResponse = new \Oxoresponse\OXOResponse("Permission Updated Successfully");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($permission);

            return $OXOResponse->jsonSerialize();

        }
    }


}