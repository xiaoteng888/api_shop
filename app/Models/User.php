<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Dcat\Admin\Traits\HasDateTimeFormatter;

class User extends Authenticatable implements MustVerifyEmail,JWTSubject
{
    use Notifiable;
    use HasDateTimeFormatter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','weixin_openid','weixin_session_key','weapp_openid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        
    ];

    public function addresses()
    {
        return $this->hasMany(UserAddress::class,'user_id','id');
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class,'user_favorite_products')
                    ->withTimestamps()
                    ->orderBy('user_favorite_products.created_at','desc');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    //getJWTIdentifier 返回了 User 的 id，getJWTCustomClaims 是我们需要额外在 JWT 载荷中增加的自定义内容，这里返回空数组。打开 tinker，执行如下代码，尝试生成一个 token。
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($value)
    {
        if(strlen($value) != 60){
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;  
    }

    
}
