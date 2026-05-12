<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = User::whereIs('admin')->get();

        return view('admin.index')->with('admin', $admin);
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Bouncer role
        $admin->assign('admin');

        return redirect()
            ->route('admin.index')
            ->withSuccess('Data saved');
    }

    public function edit(User $admin)
    {
        return view('admin.create')->with('admin', $admin);
    }

    public function update(Request $request, User $admin)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username,' . $admin->id,
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->password != null) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()
            ->route('admin.index')
            ->withSuccess('Data updated');
    }

    public function destroy(User $admin)
    {
        $admin->delete();

        return redirect()
            ->route('admin.index')
            ->withSuccess('Data deleted');
    }
}