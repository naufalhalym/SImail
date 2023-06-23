<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Classification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreClassificationRequest;
use App\Http\Requests\UpdateClassificationRequest;

class ClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            return view('pages.reference.classification', [
                'data' => Classification::render($request->search),
                'search' => $request->search,
            ]);
        } else {
            abort(403);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClassificationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreClassificationRequest $request): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                Classification::create($request->validated());
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
     * @param UpdateClassificationRequest $request
     * @param Classification $classification
     * @return RedirectResponse
     */
    public function update(UpdateClassificationRequest $request, Classification $classification): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                $classification->update($request->validated());
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
     * @param Classification $classification
     * @return RedirectResponse
     */
    public function destroy(Classification $classification): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                $classification->delete();
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        } else {
            abort(403);
        }
    }
}
