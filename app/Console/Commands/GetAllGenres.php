<?php

namespace App\Console\Commands;

use App\DAL\Model\Genre;
use App\Services\ExternalServices\EchoNestService;
use App\Tests\EchoNestServiceTest;
use Illuminate\Console\Command;

class GetAllGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all genres';
    protected $genreService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->genreService = app(EchoNestService::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $genres = $this->genreService->getAllGenres();

        foreach ($genres as $genre) {
            Genre::updateOrCreate(['name' => $genre['name']], [
                    'name' => $genre['name'],
                    'approved' => true
                ]);
        }
    }
}
