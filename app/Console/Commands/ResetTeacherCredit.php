<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTeacherCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teacher-credit:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit reset';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where("role","teacher")->update([
            "credit_without_salary"=>1,
            "credit_time" => 3,
        ]);
        $this->info("Credit reseted succesfully");
    }
}
