<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class IsActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'is:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count every two seconds and log it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 1;

        while (true) {
            Log::info("IsActive count: {$count}");
            $this->info("Count: {$count}");

            $count++;

            sleep(2); // wait 2 seconds
        }

        return Command::SUCCESS;
    }
}
