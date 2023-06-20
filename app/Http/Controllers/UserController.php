<?php

namespace App\Http\Controllers;

use App\Enums\Config as ConfigEnum;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Config;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use TheSeer\Tokenizer\Exception;

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
                return back()->with('success', __('menu.general.success'));
            } catch (\Throwable $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }else {
            abort(403);
        }
    }
}
