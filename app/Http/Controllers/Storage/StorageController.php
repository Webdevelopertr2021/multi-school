<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    
    public function getUserPhoto($fileName)
    {
        if(Auth::check() || Auth::guard("supervisor")->check() || Auth::guard("teacher")->check())
        {
            $path = "user/photos/$fileName";
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
