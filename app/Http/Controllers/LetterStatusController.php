<?php

namespace App\Http\Controllers;

use App\Models\LetterStatus;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreLetterStatusRequest;
use App\Http\Requests\UpdateLetterStatusRequest;

class LetterStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request): View
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            return view('pages.reference.status', [
                'data' => LetterStatus::render($request->search),
                'search' => $request->search,
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterStatusRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLetterStatusRequest $request): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                LetterStatus::create($request->validated());
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterStatusRequest $request
     * @param LetterStatus $status
     * @return RedirectResponse
     */
    public function update(UpdateLetterStatusRequest $request, LetterStatus $status): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                $status->update($request->validated());
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LetterStatus $status
     * @return RedirectResponse
     */
    public function destroy(LetterStatus $status): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                $status->delete();
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        } else {
            abort(403);
        }
    }
}
