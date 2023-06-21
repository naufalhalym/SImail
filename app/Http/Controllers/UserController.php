<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Config;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use App\Enums\Config as ConfigEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreUserRequest;
use Spatie\Activitylog\Models\Activity;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request, User $user): View
    {
        // $this->authorize('isAdmin') || $this->authorize('isStaff');
        if ($user->role === 'admin' || 'staff'){
            return view('pages.user', [
                'data' => User::render($request->search),
                'search' => $request->search,
            ]);
        }else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request, User $user): RedirectResponse
    {
        // $this->authorize('isAdmin') || $this->authorize('isStaff');
        if ($user->role === 'admin' || 'staff'){
            try {
                $newUser = $request->validated();
                $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
                User::create($newUser);

                //creating the newsItem will cause an activity being logged
                $activity = Activity::all()->last();

                $activity->description; //returns 'created'
                $activity->subject; //returns the instance of NewsItem that was created
                $activity->changes; //returns ['attributes' => ['name' => 'original name', 'text' => 'Lorum']];

                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // $this->authorize('isAdmin') || $this->authorize('isStaff');
        if ($user->role === 'admin' || 'staff'){
            try {
                $newUser = $request->validated();
                $newUser['is_active'] = isset($newUser['is_active']);
                if ($request->reset_password)
                    $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
                $user->update($newUser);


                //updating the newsItem will cause an activity being logged
                $activity = Activity::all()->last();

                $activity->description; //returns 'updated'
                $activity->subject; //returns the instance of NewsItem that was created

                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user): RedirectResponse
    {
        // $this->authorize('isAdmin') || $this->authorize('isStaff');
        if ($user->role === 'admin' || 'staff'){
            try {
                $user->delete();

                //deleting the newsItem will cause an activity being logged
                $activity = Activity::all()->last();

                $activity->description; //returns 'deleted'
                $activity->changes; //returns ['attributes' => ['name' => 'updated name', 'text' => 'Lorum']];

                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }else {
            abort(403);
        }
    }
}
