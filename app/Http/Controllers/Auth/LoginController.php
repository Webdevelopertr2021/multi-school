<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware("guest:supervisor")->except("logout");
        $this->middleware("guest:teacher")->except("logout");
        $this->middleware("guest:manager")->except("logout");
        $this->middleware("guest:student")->except("logout");
    }

    public function loginForm()
    {
        return view("auth.auth-login");
    }

    public function studentLoginForm()
    {
        return view("auth.student-login");
    }

    public function attemptLogin(Request $req)
    {
        $this->validate($req,[
            "email" => "required",
            "password" => "required"
        ]);

        $user = null;
        if($req->loginType == "student")
        {
            $user = Student::where("email",$req->email)->orWhere("phone",$req->email)->first();
        }
        else
        {
            $user = User::where("email",$req->email)->orWhere("phone",$req->email)->first();
        }

        if($user)
        {
            $credentials = array();
            if($req->email == $user->email)
            {
                $credentials["email"] = $req->email;
            }
            else if($req->email == $user->phone)
            {
                $credentials["phone"] = $req->email;
            }
            else
            {
                return response()->json([
                    "errors" => [
                        "email" => "Email or phone number doesn't match"
                    ]
                ],422);
            }

            $guard = null;
            if($req->loginType == "student")
            {
                $guard = "student";
            }
            else
            {
                $guard = $user->role=='super'?'web':$user->role;
            }

            $credentials["password"] = $req->password;
            $redirectUrl = null;
            if($guard == "web")
            {
                $redirectUrl = url("/admin/dashboard");
            }
            else if($guard == "supervisor")
            {
                $redirectUrl = url("/supervisor/dashboard");
            }
            else if($guard == "teacher")
            {
                $redirectUrl = url("/teacher/dashboard");
            }
            else if($guard == "manager")
            {
                $redirectUrl = url("/manager/dashboard");
            }
            else if($guard == "student")
            {
                $redirectUrl = url("/student/dashboard");
            }

            if(Auth::guard($guard)->attempt($credentials,$req->remember))
            {
                return [
                    "status" => "ok",
                    "msg" => "Login success",
                    "redirect_url" => $redirectUrl,
                ];
            }
            else
            {
                return [
                    "status" => "fail",
                    "msg" => "Wrong email or password"
                ];
            }

        }
        else
        {
            return response()->json([
                "errors" => [
                    "email" => "Email or phone number doesn't match"
                ]
            ],422);
        }
    }

    public function logout(Request $req)
    {
        $this->validate($req,[
            "role" => "required|in:super,teacher,supervisor,manager,student"
        ]);

        if($req->role == "super")
        {
            Auth::logout();
        }
        else if($req->role == "supervisor")
        {
            Auth::guard("supervisor")->logout();
        }
        else if($req->role == "teacher")
        {
            Auth::guard("teacher")->logout();
        }
        else if($req->role == "manager")
        {
            Auth::guard("manager")->logout();
        }
        else if($req->role == "student")
        {
            Auth::guard("student")->logout();
        }

        return redirect()->route("user-login");
    }
}
