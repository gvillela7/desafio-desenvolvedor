<?php

namespace App\Console\Commands;

use App\Services\ServiceReadFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ConsumerUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumer Queue Upload';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('start listening for queue creation') . PHP_EOL;
        Redis::subscribe('upload', function ($upload) {
            Log::info('message received!');
            app(ServiceReadFile::class)->saveDataFile($upload);
        });
    }
}
