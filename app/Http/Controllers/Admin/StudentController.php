<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Imports\StudentImport;
use App\Models\StudentReview;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function store(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:students,phone",
            "email" => "nullable|unique:students,email",
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);

        $student = new Student();
        $student->name = $req->name;
        $student->phone = $req->phone;
        $student->email = $req->email;
        $student->school_id = $req->schoolId;
        $student->class_id = $req->classId;
        $student->section_id = $req->sectionId;
        $student->address = $req->address;

        if($req->hasFile("photo"))
        {
            $file = $req->file("photo");
            $new_name = time()."_".rand().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$new_name,"StudentPhotos");
            $photo_url = route("student-photo",["filename"=>$new_name]);
            $student->photo = $new_name;
            $student->photo_url = $photo_url;
        }

        $student->save();

        return [
            "status" => "ok",
            "msg" => "Student was added successfully"
        ];
    }

    public function getStudentList()
    {
        $students = Student::with(["school:id,name","class:id,name","section:id,name"])->orderBy("id","desc");

        return DataTables::of($students)
            ->addColumn("school",function($row){
                return $row->school->name;
            })
            ->addColumn("class",function($row){
                return $row->class->name;
            })
            ->addColumn("section",function($row){
                return $row->section->name;
            })
            ->addColumn("ratings",function($row){
                $totalRating = 0;
                $lastMonthRating = 0;

                $i = 0;
                $l = 0;

                $now = Carbon::now();
                $last = Carbon::now()->subMonth(1);

                $currentMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$now)->get();

                $lastMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$last)->get();

                // Current month
                if(count($currentMonth) > 0)
                {
                    foreach($currentMonth as $rate)
                    {
                        $i++;
                        $totalRating += $rate->total;
                    }
                    $totalRating = round($totalRating/$i,1);
                }
                // End

                // Last month
                if(count($lastMonth) > 0)
                {
                    foreach($lastMonth as $rate)
                    {
                        $l++;
                        $lastMonthRating += $rate->total;
                    }
                    $lastMonthRating = round($lastMonthRating/$l,1);
                }
                // End

                $ratingStat = "";

                if($lastMonthRating > 0)
                {
                    if($totalRating > $lastMonthRating)
                    {
                        $ratingStat = '<span title="Improved | Last month : '.$lastMonthRating.'" class="text-success"><i class="fas fa-arrow-up"></i></span>';
                    }
                    else if($totalRating < $lastMonthRating)
                    {
                        $ratingStat = '<span title="Decreased | Last month : '.$lastMonthRating.'" class="text-danger"><i class="fas fa-arrow-down"></i></span>';
                    }
                    else if($totalRating == $lastMonthRating)
                    {
                        $ratingStat = '<span title="Unchanged | Last month : '.$lastMonthRating.'" class="text-warning"><i class="fas fa-circle"></i></span>';
                    }
                }



                return $totalRating . " point &nbsp; &nbsp;" . $ratingStat;
            })
            ->addColumn("action",function($row){
                $html = "<button data-student-edit='$row->id' class='ml-2 mb-2 btn btn-sm btn-warning'><i class='fas fa-edit'></i></button>";
                $html .= "<button data-student-delete='$row->id' class='ml-2 mb-2 btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>";
                $html .= "<button title='See ratings' data-ratings='$row->id' class='ml-2 mb-2 btn btn-sm btn-primary'><i class='fas fa-star'></i></button>";
                return $html;
            })
            ->rawColumns(["action","ratings"])
            ->make(true);
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "studentId" => "required|exists:students,id"
        ]);

        $data = Student::find($req->studentId);

        if($data->photo != "")
        {
            if(Storage::disk("StudentPhotos")->exists($data->photo))
            {
                Storage::disk("StudentPhotos")->delete($data->photo);
            }
        }

        $data->delete();

        return [
            "status" => "ok",
            "msg" => "Student record was deleted"
        ];
    }

    public function getData(Request $req)
    {
        if($data = Student::with("school")->with("class")->with("section")->find($req->studentId))
        {
            $classes = Classes::where("school_id",$data->school_id)->get();
            $sections = Section::where("class_id",$data->class_id)->get();
            return [
                "status" => "ok",
                "student" => $data,
                "classes" => $classes,
                "section" => $sections,
                
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:students,phone,$req->studentId,id",
            "email" => "nullable|unique:students,email,$req->studentId,id",
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);

        $student = Student::find($req->studentId);
        $student->name = $req->name;
        $student->phone = $req->phone;
        $student->email = $req->email;
        $student->school_id = $req->schoolId;
        $student->class_id = $req->classId;
        $student->section_id = $req->sectionId;
        $student->address = $req->address;

        if($req->hasFile("photo"))
        {
            if($student->photo != "")
            {
                if(Storage::disk("StudentPhotos")->exists($student->photo))
                {
                    Storage::disk("StudentPhotos")->delete($student->photo);
                }
            }

            $file = $req->file("photo");
            $new_name = time()."_".rand().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$new_name,"StudentPhotos");
            $photo_url = route("student-photo",["filename"=>$new_name]);
            $student->photo = $new_name;
            $student->photo_url = $photo_url;
        }

        $student->save();

        return [
            "status" => "ok",
            "msg" => "Student was added successfully"
        ];
    }

    public function importStudent(Request $req)
    {
        $this->validate($req,[
            "excel" => "required"
        ]);

        $studentImport = new StudentImport;
        try
        {
            Excel::import($studentImport, $req->file("excel"));

            return [
                "status" => "ok",
                "success" => $studentImport->success,
                "errors" => $studentImport->errors,
                "errorLog" => $studentImport->errorLog,
                "msg" => "Student data was imported successfully. See the log to know result"
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "fail",
                "msg" => "Failed to import from the file",
            ];
        }
        
    }

    public function getRatings(Request $req)
    {
        if($student = Student::with("school:id,name")->find($req->studentId,["id","name","school_id","photo","photo_url","class_id","section_id"]))
        {
            if($req->year == "" && $req->month == "")
            {
                $ratings = StudentReview::where("student_id",$student->id)->with("rater:id,name,photo,photo_url");

                $totalPoints = 0;
                $i = 0;
                foreach($ratings->get() as $rate)
                {
                    $i ++;
                    $totalPoints += $rate->total;
                }

                $final = 0;
                $totalCount = $ratings->count();
                if($totalPoints > 0)
                {
                    $final = round($totalPoints/$i,1);
                }

                $ratings = $ratings->paginate(10);
                return [
                    "status" => "ok",
                    "ratings" => $ratings,
                    "teacherData" => $student,
                    "totalPoint" => $final,
                    "totalRatingCount" => $totalCount,
                ];
            }
            else
            {
                $date = Carbon::parse($req->year."-".$req->month."-"."1");
                $ratings = StudentReview::where("student_id",$student->id)
                ->whereYear("created_at","=",$date)->whereMonth("created_at","=",$date)
                ->with("rater:id,name,photo,photo_url")->get();

                $monthlyPoint = 0;
                $i = 0;
                foreach($ratings as $rate)
                {
                    $i++;
                    $monthlyPoint += $rate->total;
                }

                if($i > 0)
                {
                    $monthlyPoint = round($monthlyPoint/$i,1);
                }

                return [
                    "review" => $ratings,
                    "monthlyPoint" => $monthlyPoint,
                    "selectedMonth" => $date->format("F"),
                    "selectedYear" => $date->format("Y"),
                ];
            }
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }
}
