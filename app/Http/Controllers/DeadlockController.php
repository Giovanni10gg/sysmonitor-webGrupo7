<?php

namespace App\Http\Controllers;

class DeadlockController extends Controller
{
    public function index()
    {
        return view('deadlocks.index');
    }
}
