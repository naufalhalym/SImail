<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class LogController extends Controller
{
    public function index(Request $request, User $user): View
    {
        if ($user->role === 'admin'){
            $logs = ActivityLog::all();
            return view('pages.log.index', [
                'logs' => $logs
            ]);
        }else {
            abort(403);
        }
    }
}
