<?php

namespace App\Http\Controllers;

use App\User;

use App\Http\Requests;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->checkAccess(auth()->user()->is_admin);
        $users = User::latest()->get();
        return view('user_views.index', compact('users'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->checkAccess(auth()->user()->is_admin);
        return view('user_views.create');
    }

    /**
     * @param Requests\UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Requests\UserRequest $request)
    {
        $this->checkAccess(auth()->user()->is_admin);
        $is_admin = $request->is_admin=='1' ? true : false;
        $users = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'is_admin' => $is_admin
        ]);
        return redirect(route('users.index'))->with('status', 'User has been successfully added.');
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);
        return view('user_views.edit', compact('user'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);
        $this->validate(request(), [
            'email' => 'required|email|max:255|unique:users, email, ' . $user->id,
        ]);
        if (request()->name == '') {
            return back()->withErrors(['name' => 'Name can\'t be empty.']);
        }
        $is_admin = request()->is_admin=='1' ? true : false;
        $update = [
            'email' => request()->email,
            'name' => request()->name,
            'is_admin' => $is_admin
        ];
        if (request()->password != '') {
            $update['password'] = bcrypt(request()->password);
        }
        $user->update($update);
        return redirect(route('users.index'))->with('status', 'User has been successfully updated.');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);
        $user->delete();
        return redirect(route('users.index'));
    }

    /**
     * @param $role
     */
    public function checkAccess($role) {
        if (!$role) {
            abort('503');
        }
    }
}
