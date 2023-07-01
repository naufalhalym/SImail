<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Letter;
use App\Models\Division;
use App\Enums\LetterType;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\Classification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Spatie\Activitylog\Models\Activity;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use Spatie\Activitylog\Traits\LogsActivity;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.transaction.incoming.index', [
            'data' => Letter::incoming()->render($request->search),
            'search' => $request->search,
            'divisions' => Division::all()
        ]);
    }

    /**
     * Display a listing of the incoming letter agenda.
     *
     * @param Request $request
     * @return View
     */
    // public function agenda(Request $request): View
    // {
    //     return view('pages.transaction.incoming.agenda', [
    //         'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->render($request->search),
    //         'search' => $request->search,
    //         'since' => $request->since,
    //         'until' => $request->until,
    //         'filter' => $request->filter,
    //         'query' => $request->getQueryString(),
    //     ]);
    // }

    /**
     * @param Request $request
     * @return View
     */
    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.incoming_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";
        return view('pages.transaction.incoming.print', [
            'data' => Letter::incoming()->get(),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value','code')->toArray(),
            'title' => $title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            return view('pages.transaction.incoming.create', [
                'classifications' => Classification::all(),
                'divisions' => Division::all()
            ]);
        }else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Ketua P3MP') {
            try {
                $user = auth()->user();

                if ($request->type != LetterType::INCOMING->type()) throw new \Exception(__('menu.transaction.incoming_letter'));
                $newLetter = $request->validated();
                $newLetter['user_id'] = $user->id;
                $letter = Letter::create($newLetter);
                if ($request->hasFile('attachments')) {
                    foreach ($request->attachments as $attachment) {
                        $extension = $attachment->getClientOriginalExtension();
                        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;
                        $filename = time() . '-'. $attachment->getClientOriginalName();
                        $filename = str_replace(' ', '-', $filename);
                        $attachment->storeAs('public/attachments', $filename);
                        Attachment::create([
                            'filename' => $filename,
                            'extension' => $extension,
                            'user_id' => $user->id,
                            'letter_id' => $letter->id,
                        ]);
                    }
                }

                //creating the event will cause an activity being logged
                $activity = Activity::all()->last();

                $activity->description; //returns 'created'
                $activity->subject; //returns the instance of event that was created
                $activity->changes; //returns ['attributes' => ['name' => 'original name', 'text' => 'Lorum']];

                return redirect()
                    ->route('transaction.incoming.index')
                    ->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function show(Letter $incoming): View
    {
        return view('pages.transaction.incoming.show', [
            'data' => $incoming->load(['classification', 'user', 'attachments']),
            'divisions' => Division::all()
        ]);
        // dd($incoming->load(['classification', 'user', 'attachments']));
        // $incoming = Letter::show($id);
        // $data = $incoming->load(['classification', 'user', 'attachments']);
        // $divisions = Division::all();

        // return view('pages.transaction.incoming.show', compact('data', 'divisions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data' => $incoming,
            'classifications' => Classification::all(),
            'divisions' => Division::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterRequest $request
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $incoming->update($request->validated());
            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;
                    $filename = time() . '-'. $attachment->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $attachment->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->user()->id,
                        'letter_id' => $incoming->id,
                    ]);
                }
            }

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
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            $incoming->delete();

            //deleting the event will cause an activity being logged
            $activity = Activity::all()->last();

            $activity->description; //returns 'deleted'
            $activity->changes; //returns ['attributes' => ['name' => 'updated name', 'text' => 'Lorum']];

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
