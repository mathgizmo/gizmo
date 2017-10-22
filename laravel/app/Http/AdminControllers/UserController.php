<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
    public function index()
    {
        $this->checkAccess(auth()->user()->is_admin);

        $users = User::latest()->get();
        return view('user_views.index', compact('users'));
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->is_admin);

        return view('user_views.create');
    }

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

    public function edit(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);

        return view('user_views.edit', compact('user'));
    }

    public function update(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);

        $this->validate(request(), [
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
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

    public function destroy(User $user)
    {
        $this->checkAccess(auth()->user()->is_admin);

        $user->delete();
        return redirect(route('users.index'));
    }

    public function checkAccess($role) {
        if (!$role) {
            abort('503');
        }
    }
}
