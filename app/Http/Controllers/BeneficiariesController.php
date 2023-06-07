<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Beneficiary;

class BeneficiariesController extends BaseController
{
    public function index(){

        $beneficiary = Beneficiary::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($beneficiary);

        return $OXOResponse->jsonSerialize();
    }

    public function create(Request $request){
        
            $this->validate(
                $request, [
                    'fullName' => 'required|string',
                    'amount' => 'required|string',
                    'acc' => 'required|string',
                    'ref' => 'required|string',
                ]
            );
    
            $beneficiary = new Beneficiary();
            $beneficiary->fullName = $request->input('fullName');
            $beneficiary->amount = $request->input('amount');
            $beneficiary->acc = $request->input('acc');
            $beneficiary->ref = $request->input('ref');


            if($beneficiary->save()):
                $OXOResponse = new \Oxoresponse\OXOResponse("Success");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_CREATED);
                $OXOResponse->setObject($beneficiary);
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_PROCESS);
                $OXOResponse->setObject($beneficiary);
                return $OXOResponse->jsonSerialize();
            endif;

        
    }
}
?>