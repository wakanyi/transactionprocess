<?php

namespace App\Http\Controllers;

use App\Kin;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Oxoresponse\OXOResponse;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use Aws\Exception\MultipartUploadException;
use Illuminate\Support\Facades\Hash;

class KinsController extends BaseController{

    public function index(){

        $kin = Kin::all();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($kin);

        return $OXOResponse->jsonSerialize();
    }

    public function create(Request $request, $userID){
        $kin = Kin::where('userID', '=', $userID)->firstOr(function () {
            
            $OXOResponse = new \Oxoresponse\OXOResponse("User Kin Does not exist. Kindly consult the administrator.");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        }
        );
        if($kin instanceof OXOResponse)
        {
            

            $this->validate(
                $request, [
                    'name' => 'required|string',
                    'email' => 'required|string',
                    'phone_number' => 'required|string',
                    'gender' => 'required|string',
                    'address' => 'required|string',
                    'country' => 'required|string',
                    'region' => 'required|string',
                ]
            );
    
            $kin = new Kin();
            $kin->userID = $userID;
            $kin->name = $request->get('name');
            $kin->email = $request->get('email');
            $kin->phone_number = $request->get('phone_number');
            $kin->gender = $request->get('gender');
            $kin->address = $request->get('address');
            $kin->country = $request->get('country');
            $kin->region = $request->get('region');
    
            $identitycard = [];
            $passportdoc = [];
    
            if($request->has('identitycard')):
                $identitycard = $this->uploaddoc($request,'identitycard');
                $kin->identitycard = $identitycard;
            endif;
    
            if($request->has('identitycard')):
                $passportdoc = $this->uploaddoc($request,'identitycard');
                $kin->passportdoc = $passportdoc;
            endif;
    
            if($kin->save()):
                $OXOResponse = new \Oxoresponse\OXOResponse("Next of Kin created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($kin);
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create Next of Kin. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($kin);
                return $OXOResponse->jsonSerialize();
            endif;
        }
        else
        {
            
            $kin->name = $request->input('name');
            $kin->email = $request->input('email');
            $kin->phone_number = $request->input('phone_number');
            $kin->gender = $request->input('gender');
            $kin->address = $request->input('address');
            $kin->country = $request->input('country');
            $kin->region = $request->input('region');
    
            $identitycard = [];
            $passportdoc = [];
    
            if($request->has('identitycard')):
                $identitycard = $this->uploaddoc($request,'identitycard');
                $kin->identitycard = $identitycard;
            endif;
    
            if($request->has('identitycard')):
                $passportdoc = $this->uploaddoc($request,'identitycard');
                $kin->passportdoc = $passportdoc;
            endif;
    
            if($kin->save()):
                $OXOResponse = new \Oxoresponse\OXOResponse("Next of Kin created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($kin);
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create Next of Kin. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($kin);
                return $OXOResponse->jsonSerialize();
            endif;
        }
    }

    public function uploaddoc(Request $request,$fieldName)
    {

        if($request->hasFile($fieldName))
        {
            $obJson = [];
            foreach ($request->file($fieldName) as $image)
            {
                // Get filename with extension
                $filenameWithExtension = $image->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                // Get just extension
                $extension = $image->getClientOriginalExtension();
                //Get file name stored with time and extension
                $filenameToStore = $filename.'_'.time().".".$extension;
                //Object with all details
                $key = basename($filenameToStore);

                $source = fopen($image, 'r+');

                //Create s3
                $s3aws = new S3Client(['version'=> 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'), ], ]);


                //Prepare the upload parameters.
                $uploader = new MultipartUploader(
                    $s3aws,
                    $source,
                    [
                        'bucket'=> env('AWS_BUCKET'),
                        'key' => $key,
                        'ACL' => 'public-read', ]
                );

                do {
                    try {

                        $result = $uploader->upload();
                        //Create array awss3 and store the $result
                        $awss3 = [$result];

                        //Iterate over array $awss3
                        foreach ($awss3 as $val) :
                            {
                                //Insert more elements to the end of an array
                                array_push(
                                    $obJson,
                                    (response()->json(
                                        [
                                            'name'=>$key, 'url'=>$val['ObjectURL'], ]
                                    ))->getOriginalContent()
                                ); }
                        endforeach;
                    } catch (MultipartUploadException $exp) {
                        echo esc_html($exp)->getMessage();
                    }
                } while (!isset($result));
            }
            return $obJson;
        } else
        {
            $OXOResponse = new OXOResponse("Could not upload");
            $OXOResponse->addErrorToList("make sure you have passed a file");
            $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);

            return $OXOResponse;
        }
    }

    public function getKin(Request $request, $userID){
        $userKin = Kin::where(['userID' => $userID])->first();


        if(!$userKin)
        {
            $OXOResponse = new \Oxoresponse\OXOResponse("Kin Does Not Exist. Kindly sign up");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($userKin);

            return $OXOResponse->jsonSerialize();
        }
        else{
            
                $OXOResponse = new \Oxoresponse\OXOResponse("Kin Exists");
                $OXOResponse->setErrorCode(8000);
                $OXOResponse->setObject($userKin);

                return $OXOResponse->jsonSerialize();
        }
    }


}