<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Queries\UserAddressQuery;
use App\Http\Resources\UserAddressResource;
use App\Http\Requests\Api\UserAddressRequest;
use App\Models\UserAddress;

class UserAddressesController extends Controller
{
    public function index(Request $request,UserAddressQuery $query)
    {
        $user_addresses = $query->where('user_id',$request->user()->id)->get();
        return UserAddressResource::collection($user_addresses);
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
        return response('创建成功',201);
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
        return new UserAddressResource($user_address);
    }

    public function destroy(UserAddress $user_address)
    {
        $this->authorize('own',$user_address);
        $user_address->delete();
        return response(null,204);
    }

    
}
