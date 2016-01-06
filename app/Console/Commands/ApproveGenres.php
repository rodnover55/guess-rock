<?php

namespace App\Console\Commands;

use App\DAL\Model\Genre;
use Illuminate\Console\Command;

class ApproveGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approve_genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'approve genres from stdin';

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
        Genre::query()->update([
            'approved' => false
        ]);

        $genres = explode("\n", file_get_contents("php://stdin"));

        foreach ($genres as $genre) {
            $query = Genre::where('name', $genre);

            if ($query->exists()) {
                $query->update(['approved' => true]);
            } else {
                Genre::create([
                    'name' => $genre,
                    'approved' => true
                ]);
            }
        }
    }
}
