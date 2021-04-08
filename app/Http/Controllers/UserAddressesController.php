<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
    	return view('user_addresses.index',[
               'addresses' => $request->user()->addresses,
    	]);
    }

    public function create()
    {
    	return view('user_addresses.create_and_edit',['address' => new UserAddress()]);
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
                'province',
	            'city',
	            'district',
	            'address',
	            'zip',
	            'contact_name',
	            'contact_phone',
        ]));
        return redirect()->route('user_addresses.index');  
    }

    public function edit(UserAddress $address)
    {
    	$this->authorize('own',$address);
        return view('user_addresses.create_and_edit',['address'=>$address]);
    }

    public function update(UserAddress $user_address,UserAddressRequest $request)
    {
    	$this->authorize('own',$user_address);
    	$user_address->update($request->only([
                'province',
	            'city',
	            'district',
	            'address',
	            'zip',
	            'contact_name',
	            'contact_phone',
    	]));
    	return redirect()->route('user_addresses.index');
    }

    public function destroy(UserAddress $address)
    {
    	$this->authorize('own',$address);
    	$address->delete();
    	return [];
    }
}
