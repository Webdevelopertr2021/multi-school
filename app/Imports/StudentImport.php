<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    public $success = 0;
    public $errors = 0;
    public $errorLog = "
        <ol class='text-danger'>
    ";

    public function collection(Collection $rows)
    {
        $array = array();
        foreach ($rows as $row) 
        {
            $school = School::find($row["school_id"]);
            $class = Classes::find($row["class_id"]);
            $section = Section::find($row["section_id"]);
            if(!empty($school) && !empty($class) && !empty($section))
            {
                Student::create([
                    'name' => $row["student_name"],
                    "phone" => $row["phone"],
                    "email" => $row["email"],
                    "address" => $row["address"],
                    "school_id" => $row["school_id"],
                    "class_id" => $row["class_id"],
                    "section_id" => $row["section_id"],
                    "photo" => $row["photo_url"]??null,
                    "photo_url" => $row["photo_url"]??null,
                ]);
                $this->success++;
            }
            else
            {
                $this->errors++;
                $reason = "";
                if(empty($school))
                {
                    $reason .= "# INVALID SCHOOL ID ";
                }
                if(empty($class))
                {
                    $reason .= "# INVALID CLASS ID ";
                }
                if(empty($section))
                {
                    $reason .= "# INVALID SECTION ID";
                }
                $this->errorLog .= "
                    <li>Md Sazzad <strong>[ERRORS : $reason]</strong></li>
                ";
                
            }
            
        }
        $this->errorLog .= "</ol>";
    }

    public function headingRow() : int
    {
        return 1;
    }
}
