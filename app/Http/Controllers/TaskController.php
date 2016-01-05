<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class TaskController extends Controller
{
    public function index(Request $request) {
        dd((array)$request->user());
        return view('task');
    }
}