<?php


namespace App\Http\Controllers;

use App\Notification;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Oxoresponse\OXOResponse;
use Aws\Exception\MultipartUploadException;

class NotificationController extends BaseController{

   
    public function create(Request $request){

        $notification = new Notification();
	
        if($request->has('userID'))
	    $notification->userID = $request->userID;

        if($request->has('tenderID'))
         $notification->tenderID = $request->tenderID;

        if($request->has('tender_id'))
         $notification->tender_id = $request->tender_id;

        $notification->notification = $request->notification;
        $notification->icons = $request->icons;
        $notification->role = $request->role;
        $notification->category = $request->category;
        $notification->save();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operational successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($notification);

        return $OXOResponse->jsonSerialize();
    }

    public function getUnreadNotifications($userID){

	$user = User::where('id', $userID)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find user ");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($user instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notifications = Notification::where(['userID'=>$userID,'is_read'=>0])->get();
	    //$notifications = DB::table('notifications')->where(['userID'=>$userID,'is_read'=>0])->distinct()->get(['notification']);
	
	    $OXOResponse = new OXOResponse("user notifications");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($notifications);

            return $OXOResponse->jsonSerialize();
        }
    }

       public function getReadNotifications($userID){

        $user = User::where('id', $userID)->firstOr(function () {

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

        $user = User::where('id', $userID)->firstOr(function () {

            $OXOResponse = new OXOResponse("Could not find user ");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            return $OXOResponse;
        });

        if ($user instanceof OXOResponse) {

            return $user->jsonSerialize();

        } else {

            $notifications = Notification::where(['userID'=>$userID])->orderBy('id', 'desc')->get();

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

            return $notification->jsonSerialize();

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

    public function getAllNotification_by_role(Request $request)
    {

        $notification = Notification::where('role', $request->role)->orderBy('id', 'desc')->get();
         if ($notification != null) :

           
            $OXOResponse = new \Oxoresponse\OXOResponse("Notifications Exists");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($notification);

            return $OXOResponse->jsonSerialize();
        else:

            $OXOResponse = new \Oxoresponse\OXOResponse("Notifications Does Not exist");
            $OXOResponse->addErrorToList("Please check with the administrator and try again");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            
            return $OXOResponse;
        endif;
    }


    public function getAllUnreadNotifications_by_role(Request $request)
    {

        $unread_notification = Notification::where(['role'=>$request->role, 'is_read'=>0])->get();
         if ($unread_notification != null) :

           
            $OXOResponse = new \Oxoresponse\OXOResponse("Unread Notifications Exists");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($unread_notification);

            return $OXOResponse->jsonSerialize();
        else:

            $OXOResponse = new \Oxoresponse\OXOResponse("Unread Notifications Does Not exist");
            $OXOResponse->addErrorToList("Please check with the administrator and try again");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
            
            return $OXOResponse;
        endif;
    }
   
}
