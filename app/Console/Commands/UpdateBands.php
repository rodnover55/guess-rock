<?php

namespace App\Console\Commands;

use App\DAL\Model\Genre;
use App\Services\ExternalServices\EchoNestService;
use Illuminate\Console\Command;

class UpdateBands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:bands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update artists and images by all genres';

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
     * @return mixed
     */
    public function handle()
    {
        app(EchoNestService::class)->updateArtists(Genre::all());
    }
}
