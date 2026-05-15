<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    /**
     * LIST OWNERS
     */
    public function index()
    {
        $owners = User::whereIs('owner')->get();

        return view('owner.index', compact('owners'));
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('owner.create');
    }

    /**
     * STORE OWNER
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $owner = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Bouncer role
        $owner->assign('owner');

        return redirect()->route('owner.index')
            ->withSuccess('Owner created successfully');
    }

    /**
     * EDIT PAGE
     */
    public function edit(User $owner)
    {
        return view('owner.create', compact('owner'));
    }

    /**
     * UPDATE OWNER
     */
    public function update(Request $request, User $owner)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $owner->id,
            'email' => 'required|email|unique:users,email,' . $owner->id,
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

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $owner->update($data);

        return redirect()->route('owner.index')
            ->withSuccess('Owner updated successfully');
    }

    /**
     * DELETE OWNER
     */
    public function destroy(User $owner)
    {
        $owner->delete();

        return redirect()->route('owner.index')
            ->withSuccess('Owner deleted successfully');
    }
}