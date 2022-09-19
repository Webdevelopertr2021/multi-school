<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    
    public function getUserPhoto($filename)
    {
        if(Auth::check() || Auth::guard("supervisor")->check() || Auth::guard("teacher")->check() || Auth::guard("manager")->check())
        {
            $path = "user/photos/$filename";
            if(Storage::exists($path))
            {
                return Storage::response($path);
            }
            else
            {
                return response("File not found",404);
            }
        }
        else
        {
            return response("File not found",404);
        }
    }

    public function getStudentPhoto($filename)
    {
        if(Auth::check() || Auth::guard("supervisor")->check() || Auth::guard("teacher")->check() || Auth::guard("student")->check())
        {
            $path = "student/photos/$filename";
            if(Storage::exists($path))
            {
                return Storage::response($path);
            }
            else
            {
                return response("File not found",404);
            }
        }
        else
        {
            return response("File not found",404);
        }
    }

    public function getBankPhoto($filename)
    {
        if(Auth::check() || Auth::guard("supervisor")->check() || Auth::guard("teacher")->check() || Auth::guard("manager")->check())
        {
            $path = "bank/photos/$filename";
            if(Storage::exists($path))
            {
                return Storage::response($path);
            }
            else
            {
                return response("File not found",404);
            }
        }
        else
        {
            return response("File not found",404);
        }
    }
}
