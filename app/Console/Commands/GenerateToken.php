<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:generate_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快速为用户生成token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->ask('输入用户ID');
        $user = User::find($userId);
        if(!$user){
            return $this->error('用户不存在');
        }
        // 一年以后过期，单位分钟
        $ttl = 365 * 24 * 60;
        $this->info(auth('api')->setTTL($ttl)->login($user));
    }
}
