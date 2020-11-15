<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check logged user
        $user               =           Auth::user();
        if(!is_null($user)) {
            $tasks          =           Task::where("user_id", $user->id)->get();
            if(count($tasks) > 0) {
                return response()->json(["status" => "success", "count" => count($tasks), "data" => $tasks], 200);
            }

            else {
                return response()->json(["status" => "failed", "count" => count($tasks), "message" => "Failed! no task found"], 200);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // check logged user
        $user               =           Auth::user();

        if(!is_null($user)) {

            // create task
            $validator      =   Validator::make($request->all(), [
                "title"         =>      "required",
                "description"   =>      "required"            
            ]);

            if($validator->fails()) {
                return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }

            $taskInput              =       $request->all();
            $taskInput['user_id']   =       $user->id; 

            $task           =       Task::create($taskInput);
            if(!is_null($task)) {
                return response()->json(["status" => "success", "message" => "Success! task created", "data" => $task]);
            }

            else {
                return response()->json(["status" => "failed", "message" => "Whoops! task not created"]);
            }
        }      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user           =       Auth::user();
        if(!is_null($user)) {
            $task       =       Task::where("user_id", $user->id)->where("id", $id)->first();
            if(!is_null($task)) {
                return response()->json(["status" => "success", "data" => $task], 200);
            }
            else {
                return response()->json(["status" => "failed", "message" => "Failed! no task found"], 200);
            }
        }
        else {
            return response()->json(["status" => "failed", "message" => "Un-authorized user"], 403);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $input          =           $request->all();
        $user           =           Auth::user();

        if(!is_null($user)) {

            // validation
            $validator      =       Validator::make($request->all(), [
                "title"           =>      "required",
                "description"     =>      "required",
            ]);

            if($validator->fails()) {
                return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }

            // update post
            $update       =           $task->update($request->all());

            return response()->json(["status" => "success", "message" => "Success! task updated", "data" => $task], 200);

        }
        else {
            return response()->json(["status" => "failed", "message" => "Un-authorized user"], 403);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $user           =       Auth::user();
        
        if(!is_null($user)) {
            $task       =       Task::where("id", $task)->where("user_id", $user->id)->delete();
            return response()->json(["status" => "success", "message" => "Success! task deleted"], 200);
        }

        else {
            return response()->json(["status" => "failed", "message" => "Un-authorized user"], 403);
        }
    }
}
