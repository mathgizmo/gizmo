<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;

class UserController extends Controller
{

    public function __construct()
    {
        // $this->authorizeResource(User::class);
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        if ($request->has('sort') and $request->has('order')) {
            $users = User::orderBy($request->sort, $request->order)->get();
        } else {
            $users = User::latest()->get();
        }
        return view('user_views.index', compact('users'));
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        return view('user_views.create');
    }

    public function store(Requests\UserRequest $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);
        return redirect(route('users.index'))->with('status', 'User has been successfully added.');
    }

    public function edit(User $user)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        return view('user_views.edit', compact('user'));
    }

    public function update(User $user)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $this->validate(request(), [
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);
        if (request()->name == '') {
            return back()->withErrors(['name' => 'Name can\'t be empty.']);
        }
        $update = [
            'email' => request()->email,
            'name' => request()->name,
            'role' => request()->role
        ];
        if (request()->password != '') {
            $update['password'] = bcrypt(request()->password);
        }
        $user->update($update);
        return redirect(route('users.index'))->with('status', 'User has been successfully updated.');
    }

    public function destroy(User $user)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $user->delete();
        return redirect(route('users.index'));
    }
}
