<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\Mail\ResetPassword;
use App\OXOResponse;
use Illuminate\Support\Facades\Auth;
use Aws\Exception\MultipartUploadException;
use Illuminate\Support\Facades\Hash;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;

class UsersController extends BaseController{

   
    public function index(){

        $user = User::all();
       

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($user);

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

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
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

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $response = response()->json($this->guard()->user());
        $OXOResponse = new \Oxoresponse\OXOResponse("User Found");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($response);
        return $OXOResponse->jsonSerialize();
    }

    public function login(Request $request) {
        
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        // Find the user by email
        $profile = User::where('email', $request->input('email'))->first();
        
        if ($profile != null):
            if ($profile->email_verify === false):
                $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email and verify your account");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                
                return $OXOResponse;
            else:
                if (Hash::check($request->input('password'), $profile->password)) {
                    $credentials = $request->only('email', 'password');
                    if ($token = Auth::attempt($credentials)) {
                        $object = $this->respondWithToken($token);
                        $OXOResponse = new \Oxoresponse\OXOResponse("Login Successfully");
                        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                        $OXOResponse->setObject($object);
                        return $OXOResponse->jsonSerialize();
                    }
                } else {
                    $OXOResponse = new \Oxoresponse\OXOResponse("Failed to login in.");
                    $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                    $OXOResponse->addErrorToList("Kindly check your credentials and try again");
                    return $OXOResponse;

                }
            endif;
        else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email ");
                //$OXOResponse->addErrorToList("Email does not exist");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                
                return $OXOResponse;
        endif;
        
    }


    public function create(Request $request){
        //check if the email exists in the system
        $profilerec = User::where('email', $request->input('email'))->first();
        
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if($profilerec){

            $OXOResponse = new \Oxoresponse\OXOResponse("User Exists. Kindly use another email or contact Adminstrator to get your logins");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($profilerec);

            return $OXOResponse;

        }else{
            $this->validate(
                $request, [
                    'name' => 'required|string',
                    'email' => 'required|string',
                    'phone_number' => 'required|string',
                    'address' => 'required|string',
                    'country' => 'required|string',
                    'region' => 'required|string',
                    'password' => 'required|string',
                ]
            );

            $user = new User();
            $user->userID = $this->generate_controlno($permitted_chars, 5);
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone_number = $request->get('phone_number');
            $user->address = $request->get('address');
            $user->country = $request->get('country');
            $user->region = $request->get('region');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->usertype = $request->input('usertype');

            

            $profilepic = [];
            $iddoc = [];
            $tincert = [];
            $passport = [];

            if($request->has('profile_picture')):
                $profilepic = $this->uploaddoc($request,'profile_picture');
                $user->profile_picture = $profilepic;
            endif;

            if($request->has('id_document')):
                $iddoc = $this->uploaddoc($request,'id_document');
                $user->id_document = $iddoc;
            endif;

            if($request->has('tin_certificate')):
                $tincert = $this->uploaddoc($request,'tin_certificate');
                $user->tin_certificate = $tincert;
            endif;

            if($request->has('passport')):
                $passport = $this->uploaddoc($request,'passport');
                $user->passport = $passport;
            endif;
            
            if($user->save()):
                Mail::to($user->email)->send(new VerificationEmail($user));

                $OXOResponse = new \Oxoresponse\OXOResponse("User created successfully. Kindly check your email to verify account.");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($user);
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create user. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($user);
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

    public function resetPassword(Request $request){

        $user = User::where(['email' => $request->email])->firstOr(function () {
            $OXOResponse = new \Oxoresponse\OXOResponse("Email does not exist kindly check and try again.");
            $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);

            return $OXOResponse;
        }
        );

        if($user instanceof OXOResponse)
        {
            return $user->jsonSerialize();
        }
        else
        {
            
            Mail::to($request->email)->send(new ResetPassword($user));

            $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email to reset your password");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($user);

            return $OXOResponse->jsonSerialize();

        }

    }

    public function forgotPassword(Request $request, $userID){

        $user = User::where(['userID' => $userID])->firstOr(function () {
            $OXOResponse = new \Oxoresponse\OXOResponse("User Does not exist. Kindly check again and try again later.");
            $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);

            return $OXOResponse;
        }
        );

        if($user instanceof OXOResponse)
        {
            return $user->jsonSerialize();
        }
        else
        {
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            $OXOResponse = new \Oxoresponse\OXOResponse("User Password Updated Successfully");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($user);

            return $OXOResponse->jsonSerialize();

        }
    }

    public function fetchEmail(Request $request, $user_email){
        $profile = User::where('email', $user_email)->first();
         if ($profile != null) :

           
            $OXOResponse = new \Oxoresponse\OXOResponse("User Exists");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($profile);

            return $OXOResponse->jsonSerialize();
        else:

            $OXOResponse = new \Oxoresponse\OXOResponse("User does not exist");
            $OXOResponse->addErrorToList("Please check with the administrator and try again");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            
            return $OXOResponse;
        endif;
    }

    public function verifyEmail(Request $request, $userID){
        $user = User::where(['userID' => $userID])->first();
        
        if(!$user)
        {
            $OXOResponse = new \Oxoresponse\OXOResponse("User Does Not Exist. Kindly sign up");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($user);

            return $OXOResponse->jsonSerialize();
        }
        else{
            if($user->email_verify === true)
                {
                    $OXOResponse = new \Oxoresponse\OXOResponse("User Already Verified kindly Log in");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($user);

                    return $OXOResponse->jsonSerialize();
                }
            else
            {
                $user->email_verify = true;
                if($user->save()){
                $OXOResponse = new \Oxoresponse\OXOResponse("Verification Successful. Kindly login to proceed");
                $OXOResponse->setErrorCode(8000);
                $OXOResponse->setObject($user);
            //$msg = "true"; 
            return $OXOResponse->jsonSerialize();
            //return redirect('http://localhost/?msg='.$msg);
        }
            }
        }        
    }

public function verifyAdmin(Request $request, $userID){
    $user = User::where(['userID' => $userID])->first();


        if(!$user)
        {
            $OXOResponse = new \Oxoresponse\OXOResponse("User Does Not Exist. Kindly sign up");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($user);

            return $OXOResponse->jsonSerialize();
        }
        else{
            if($user->admin_verify === true)
                {
                    $OXOResponse = new \Oxoresponse\OXOResponse("User Already Verified kindly Log in");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($user);

                    return $OXOResponse->jsonSerialize();
                }
            else
            {
                $user->admin_verify = true;

                if($user->save()){
                $OXOResponse = new \Oxoresponse\OXOResponse("Verification Successful. Kindly login to proceed");
                $OXOResponse->setErrorCode(8000);
                $OXOResponse->setObject($user);
            $msg = "true"; 
        
            return redirect('http://localhost:8080/#/?msg='.$msg);
        }

                
            }
        }        
    }

    public function getUserType(Request $request, $usertype){
        if($usertype == 'internal'):
            
            $individualUser = User::where('usertype', 'like', '%internal%')->get();
                $OXOResponse = new \Oxoresponse\OXOResponse("List of All Internal Users");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($individualUser);

                return $OXOResponse->jsonSerialize();
        else:
            $externalUser = User::where('usertype', 'not like', '%internal%')->get();;
            $OXOResponse = new \Oxoresponse\OXOResponse("List of All External Users");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($externalUser);

            return $OXOResponse->jsonSerialize();
        endif;
    }
    
}
