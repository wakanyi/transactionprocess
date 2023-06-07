<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Process;
use App\Beneficiaries;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;
use Oxoresponse\OXOResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Exceptions\CoreErrors;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class ProcessesController extends BaseController
{

    public function authenticateToken(Request $request){

        //$token= str_replace('Bearer ', "" , $request->header('Authorization'));

        $token = $request->token;

        try { 
            // \Tymon\JWTAuth\JWTAuth::setToken($token); //<-- set token and check
            auth()->setToken($token);
            if (! $claim = auth()->getPayload()) {
                return response()->json(array('message'=>'user_not_found'), 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(array('message'=>'token_expired','code'=>'401'), 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(array('message'=>'token_invalid','code'=>'401'), 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(array('message'=>'token_absent','code'=>'401'), 401);
        }

        return response()->json(array('message'=>'OK','code'=>'200'),200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'basic',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }


    public function index(){

        $process = Process::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($process);

        return $OXOResponse->jsonSerialize();
    }

    public function create(Request $request){
        $process = Process::where('companyId', '=', $request->input('companyId'))->firstOr(function () {
            
            $OXOResponse = new \Oxoresponse\OXOResponse("Does not exist.");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        }
        );
        if($process instanceof OXOResponse)
        {
            $this->validate(
                $request, [
                    'companyId' => 'required|string',
                    'batchNo' => 'required|string',
                    'batchDate' => 'required|string',
                    'description' => 'required|string',
                    'beneficiaries' => 'required|json',
                ]
            );
    
            $process = new Process();
            $process->companyId = $request->input('companyId');
            $process->batchNo = $request->input('batchNo');
            $process->batchDate = $request->input('batchDate');
            $process->description = $request->input('description');
            $beneficiaries = 0;
            $sum = 0;
            foreach($request->input('beneficiaries') as $beneficiaryData){
                $process->totalbeneficiaries = $beneficiaries++;
            }
            foreach($request->input('beneficiaries') as $key=>$value)
            {
                $sum+= $value;
            }
            $process->totalAmount = $sum;

            $process->beneficiaries = json_encode($data);


            if($process->save()):
                Mail::to("supportteam@nbc.co.tz")->send(new VerificationEmail($user));
                $OXOResponse = new \Oxoresponse\OXOResponse("Success");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_CREATED);
                $OXOResponse->setObject($process);
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_PROCESS);
                $OXOResponse->setObject($process);
                return $OXOResponse->jsonSerialize();
            endif;

        }
    }
}
?>