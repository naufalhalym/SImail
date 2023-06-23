<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Disposition;
use App\Models\LetterStatus;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use App\Http\Requests\StoreDispositionRequest;
use App\Http\Requests\UpdateDispositionRequest;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class DispositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Letter $letter
     * @return View
     */
    public function index(Request $request, Letter $letter): View
    {
        return view('pages.transaction.disposition.index', [
            'data' => Disposition::render($letter, $request->search),
            'letter' => $letter,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Letter $letter
     * @return View
     */
    public function create(Letter $letter): View
    {
        return view('pages.transaction.disposition.create', [
            'letter' => $letter,
            'statuses' => LetterStatus::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Letter $letter
     * @param StoreDispositionRequest $request
     * @return RedirectResponse
     */
    public function store(StoreDispositionRequest $request, Letter $letter): RedirectResponse
    {
        try {
            $newDisposition = $request->validated();
            $newDisposition['user_id'] = auth()->user()->id;
            $newDisposition['letter_id'] = $letter->id;
            Disposition::create($newDisposition);

            //creating the event will cause an activity being logged
            $activity = Activity::all()->last();

            $activity->description; //returns 'created'
            $activity->subject; //returns the instance of event that was created
            $activity->changes; //returns ['attributes' => ['name' => 'original name', 'text' => 'Lorum']];

            return redirect()
                ->route('transaction.disposition.index', $letter)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $letter
     * @param Disposition $disposition
     * @return View
     */
    public function edit(Letter $letter, Disposition $disposition): View
    {
        return view('pages.transaction.disposition.edit', [
            'data' => $disposition,
            'letter' => $letter,
            'statuses' => LetterStatus::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDispositionRequest $request
     * @param Letter $letter
     * @param Disposition $disposition
     * @return RedirectResponse
     */
    public function update(UpdateDispositionRequest $request, Letter $letter, Disposition $disposition): RedirectResponse
    {
        try {
            $disposition->update($request->validated());

            //updating the event will cause an activity being logged
            $activity = Activity::all()->last();

            $activity->description; //returns 'updated'
            $activity->subject; //returns the instance of event that was created

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $letter
     * @param Disposition $disposition
     * @return RedirectResponse
     */
    public function destroy(Letter $letter, Disposition $disposition): RedirectResponse
    {
        try {
            $disposition->delete();

            //deleting the event will cause an activity being logged
            $activity = Activity::all()->last();

            $activity->description; //returns 'deleted'
            $activity->changes; //returns ['attributes' => ['name' => 'updated name', 'text' => 'Lorum']];

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
