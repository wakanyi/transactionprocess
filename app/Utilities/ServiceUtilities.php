<?php

namespace App\Utilities;

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use App\Utilities\Notification;
use Illuminate\Http\Request;

Class ServiceUtilities {

    public static function upload(Request $request,$fieldName)
    {
       
        if($request->hasFile($fieldName)) 
        {

            $files = [];

            foreach($request->file($fieldName) as $image)
            {
                $filePath = time().'_'. $image->getClientOriginalName();

                $result = Storage::disk('s3')->put($filePath, file_get_contents($image),'public');

                array_push($files,Storage::disk('s3')->url($filePath));

            }

            return $files;    
        }
    }

    public static function uploadOthers($otherfiles)
    {
        $files = [];
        
        foreach($otherfiles as $otherfile){

            $object = array();

            if(is_file($otherfile['file'])){

                
                $file = $otherfile['file'];

                $filePath = time().'_'. $file->getClientOriginalName();

                $result = Storage::disk('s3')->put($filePath, file_get_contents($file),'public');

                $object['url'] = Storage::disk('s3')->url($filePath);

                $object['name'] = $otherfile['name'];
            }

            array_push($files,$object);
        }

        return $files;
    }

    public static function startsWith ($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public static function generateId($prefix, $id)
    {
        /*$dashHash = "-";
        $hashDash = "#-";*/
        $hyphen = "-";
        $year = date('Ymd');
        return $prefix.$hyphen.$year.$hyphen.$id;
    }

    public static function sendNotification($topic,$message){

        $instance = Notification::getInstance();

        $instance->publish($topic,$message);
    }
}