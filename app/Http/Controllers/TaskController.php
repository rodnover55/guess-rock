<?php
namespace App\Http\Controllers;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class TaskController extends Controller
{
    public function index() {
        return view('task');
    }
}