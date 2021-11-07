<?php

namespace App\Console\Commands;

use App\Models\KeyStatistics;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class everyDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this will be remove all request records users daily';

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
        DB::update('UPDATE users set daily_number_of_requests = 0');
        DB::table('key_statistics')->delete();
    }
}
