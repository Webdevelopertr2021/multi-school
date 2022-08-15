<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:manager");
    }

    public function getList(Request $req)
    {
        $search = $req->search;
        $data = User::where("role","supervisor")->where("manager_id",auth("manager")->user()->id)
        ->orderBy("name","asc");
        if($search != "")
        {
            $data->where("name","like","%$search%")->orWhere("phone",$search)->orWhere("email",$search);
        }
        $data = $data->with("school:id,name")->paginate(15);

        return response()->json($data);
    }
}
