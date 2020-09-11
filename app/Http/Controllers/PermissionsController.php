<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Oxoresponse\OXOResponse;

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
                'slug' => 'required|string',
            ]
        );

        $permission = new Permission();
        $permission->permID = $this->generate_controlno($permitted_chars, 5);
        $permission->permission = $request->get('permission');
        $permission->publish = $request->get('publish');
        $permission->slug = $request->get('slug');
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
            $permission->slug = $request->get('slug');
            $permission->save();

            $OXOResponse = new \Oxoresponse\OXOResponse("Permission Updated Successfully");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($permission);

            return $OXOResponse->jsonSerialize();

        }
    }

    public function roles()
    {   
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    	$dev_permission = Permission::where('slug','create-tasks')->first();
		$manager_permission = Permission::where('slug', 'edit-users')->first();

		//RoleTableSeeder.php
        $dev_role = new Role();
        $dev_role->roleID = $this->generate_controlno($permitted_chars, 5);
		$dev_role->slug = 'developer';
        $dev_role->role = 'Front-end Developer';
        $dev_role->publish = 1;
		$dev_role->save();
		$dev_role->permissions()->attach($dev_permission);

        $manager_role = new Role();
        $manager_role->roleID = $this->generate_controlno($permitted_chars, 5);
		$manager_role->slug = 'manager';
        $manager_role->role = 'Assistant Manager';
        $manager_role->publish = 1;
		$manager_role->save();
		$manager_role->permissions()->attach($manager_permission);

		$dev_role = Role::where('slug','developer')->first();
		$manager_role = Role::where('slug', 'manager')->first();

        $createTasks = new Permission();
        $createTasks->permID = $this->generate_controlno($permitted_chars, 5);
		$createTasks->slug = 'create-tasks';
		$createTasks->permission = 'Create Tasks';
		$createTasks->save();
		$createTasks->roles()->attach($dev_role);

        $editUsers = new Permission();
        $editUsers->permID = $this->generate_controlno($permitted_chars, 5);
		$editUsers->slug = 'edit-users';
        $editUsers->permission = 'Edit Users';
        $editUsers->publish = 1;
		$editUsers->save();
		$editUsers->roles()->attach($manager_role);

		$dev_role = Role::where('slug','developer')->first();
		$manager_role = Role::where('slug', 'manager')->first();
		$dev_perm = Permission::where('slug','create-tasks')->first();
		$manager_perm = Permission::where('slug','edit-users')->first();

        $developer = new User();
        $developer->userID = $this->generate_controlno($permitted_chars, 5);
		$developer->name = 'Mahedi Hasan';
        $developer->email = 'mahedi@gmail.com';
        $developer->phone_number = 123456789;
        $developer->address = 'hdbjkbdfekjb';
        $developer->country = 'Kenya';
        $developer->region = 'Nairobi';
        $developer->usertype = 'Internal';
        $developer->password = bcrypt('secrettt');
		$developer->save();
		$developer->roles()->attach($dev_role);
		$developer->permissions()->attach($dev_perm);

        $manager = new User();
        $manager->userID = $this->generate_controlno($permitted_chars, 5);
		$manager->name = 'Mahedi Hasan';
        $manager->email = 'mahedi@gmail.com';
        $manager->phone_number = 123456789;
        $manager->address = 'hdbjkbdfekjb';
        $manager->country = 'Kenya';
        $manager->region = 'Nairobi';
        $manager->usertype = 'Internal';
		$manager->password = bcrypt('secrettt');
		$manager->save();
		$manager->roles()->attach($manager_role);
		$manager->permissions()->attach($manager_perm);

		
		return redirect()->back();
    }


}