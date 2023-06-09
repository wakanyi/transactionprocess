<?php

namespace App\Exceptions;

/**
 * CoreErrors
 *
 * This class shall list all errors related to the core Api
 * Catch errors thrown in other parts of the code
 *
 * Errors related to database operations will be in the range 8000 - 9000
 * Errors may also result from successful operations
 *
 * @author Salim Said <salim@oxoafrica.co.tz>
 * @Date    Nov 21 2019 14:13HRS
 */
class CoreErrors
{
    /**
     * User-generated error message. This is like an
     * this error is returned when a user tries to
     * read or update a resource and the operation is successful
     */
    const OPERATION_SUCCESSFUL = 8000;

    /**
     * User-generated error message.
     * this error is returned when a user tries to
     * read or update a resource that's not available
     */
    const RECORD_NOT_FOUND = 8001;

    /**
     * User-generated error message. This is like an
     * this error is returned when a user tries to
     * read a resource is found but could not be deleted
     */
    const DELETE_OPERATION_FAILED = 8002;

    /**
     * User-generated error message. This is like an
     * this error is returned when a user tries to
     * UPDATE a resource that is not found
     */
    const UPDATE_OPERATION_FAILED = 8003;
    
   /**
     * User-generated error message. This is like an
     * this error is returned when a user tries to
     * LOGIN a resource that is not found
     */
    const USER_NOT_FOUND = 8004;

    /**
     * User-generated error message. This is like an
     * this error is returned when a user tries to
     * LOGIN a resource that is not found
     */
    const FAILED_TO_CREATE_RECORD = 8005;

    /**
     * Record-generated success message. This is like an
     * this error is returned when a user tries to
     * LOGIN a resource that is not found
     */
    const RECORD_CREATED = 909;

     /**
     * Record-generated error message. This is like an
     * this error is returned when a user tries to
     * LOGIN a resource that is not found
     */
    const FAILED_TO_CREATE_PROCESS = 910;
}