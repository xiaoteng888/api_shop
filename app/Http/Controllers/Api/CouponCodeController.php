<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CouponCode;
use App\Http\Resources\CouponCodeResource;
use App\Exceptions\CouponCodeUnavailableException;

class CouponCodeController extends Controller
{
    public function show($code,Request $request)
    {
        if(!$record = CouponCode::where('code',$code)->first()){
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        $record->checkAvailable($request->user());
        return new CouponCodeResource($record);
    }
}
