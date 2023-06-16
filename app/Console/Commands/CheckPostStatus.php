<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\DeviceController;
use App\Repository\DeviceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckPostStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:status {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command to check status from request';

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
        $id = $this->argument('id');
        (new DeviceController(new DeviceRepository))->checkStatus($id);
    }
}
