<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $tasks = auth()->user()->tasks()->latest('created_at')->paginate(10);

        return view('dashboard', compact('tasks'));
    }
}
