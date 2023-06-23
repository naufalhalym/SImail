<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP'){
            return view('pages.reference.division', [
                'data' => Division::render($request->search),
                'search' => $request->search,
            ]);
        }else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDivisionRequest $request): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP'){
            try {
                Division::create($request->validated());
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDivisionRequest $request, Division $division): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP'){
            try {
                $division->update($request->validated());
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Division $division): RedirectResponse
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP'){
            try {
                $division->delete();
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }
    }
}
