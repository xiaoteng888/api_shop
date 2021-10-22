<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeixinSessionKeyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('weixin_openid')->nullable()->unique()->after('remember_token')->comment('用户openid');
            $table->string('weapp_openid')->nullable()->unique()->after('weixin_openid')->comment('小程序openid');
            $table->string('weixin_session_key')->nullable()->unique()->after('weapp_openid')->comment('用户的session_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('weixin_openid');
            $table->dropColumn('weapp_openid');
            $table->dropColumn('weixin_session_key');
        });
    }
}
