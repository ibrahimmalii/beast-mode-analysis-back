<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class everyMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'month:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete all requests from requests table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::update('UPDATE requests SET monthly_number_of_requests = 0 , remaining_of_requests = 0');
        DB::update('UPDATE users SET monthly_number_of_requests = 0, daily_number_of_requests =0, avg_monthly_number_of_requests =0');
    }
}
