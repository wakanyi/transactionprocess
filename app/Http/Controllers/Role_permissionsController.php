<?php

namespace App\Http\Controllers;

use App\Role_permission;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;

class Role_permissionsController extends BaseController{

    public function index(){

        $prole = Role_permission::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($prole);

        return $OXOResponse->jsonSerialize();
    }

}