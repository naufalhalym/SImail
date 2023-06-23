<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        if (Auth::user()->role == 'Admin'){
            return view('pages.log.index', [
                'data' => ActivityLog::render($request->search),
                'search' => $request->search,
            ]);
        }else {
            abort(403);
        }
    }
}
