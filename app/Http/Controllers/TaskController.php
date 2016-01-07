<?php
namespace App\Http\Controllers;
use App\Services\GameService;
use Illuminate\Http\Request;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class TaskController extends Controller
{
    public function index(Request $request, GameService $gameService) {
        $game = $gameService->generateGame($request->user() ?? null);

        session(['game_id'=> $game['id']]);

        return view('task', [
            'task' => array_only($game['tasks'][0], ['image', 'choices']),
            'task_number' => 0
        ]);
    }
}