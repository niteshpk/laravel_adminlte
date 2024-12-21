<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class clean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        File::put(storage_path('/logs/laravel.log'), '');
        $this->info('Everything cleaned');
    }
}