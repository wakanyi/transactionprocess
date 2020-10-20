<?php


namespace App\Http\Controllers;

use App\Notification;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Oxoresponse\OXOResponse;
use Aws\Exception\MultipartUploadException;

class NotificationController extends BaseController{

   
    public function create(Request $request){

        $notification = new Notification();

	$notification->userID = $request->userID;
        $notification->notification = $request->notification;

        $notification->save();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operational successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($notification);

        return $OXOResponse->jsonSerialize();
    }

    public function getUnreadNotifications($userID){

	$user = User::where('userID', $userID)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find user ");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($user instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notifications = Notification::where(['userID'=>$userID,'is_read'=>0])->get();
	
	    $OXOResponse = new OXOResponse("user notifications");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($notifications);

            return $OXOResponse->jsonSerialize();
        }
    }

       public function getReadNotifications($userID){

        $user = User::where('userID', $userID)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find user ");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($user instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notifications = Notification::where(['userID'=>$userID,'is_read'=>1])->get();

            $OXOResponse = new OXOResponse("user notifications");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($notifications);

            return $OXOResponse->jsonSerialize();
        }

    }

      public function getAllNotifications($userID){

        $user = User::where('userID', $userID)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find user ");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($user instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notifications = Notification::where(['userID'=>$userID])->get();

            $OXOResponse = new OXOResponse("user notifications");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($notifications);

            return $OXOResponse->jsonSerialize();
        }

    }

     public function markAsRead(Request $request){

        $notification = Notification::where('id', $request->id)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find notification");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($notification instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notification->is_read = true;

	    if($notification->save()){
	    	
		$OXOResponse = new OXOResponse("Notification updated ");
            	$OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            	$OXOResponse->setObject($notification);

           	return $OXOResponse->jsonSerialize();

            }
        }

    }
}
