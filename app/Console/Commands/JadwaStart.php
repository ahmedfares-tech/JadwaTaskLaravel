<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class JadwaStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwa:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initi Data';

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
        User::create([
            'name' => 'Ahmed Fares',
            'email' => 'admin@jadwa.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
        ]);
        $parentCategory = Category::create([
            'name' => 'Clothes',
            'image' => '/storage/products/1/16310280560.PNG',
            'parent_id' => null
        ]);
        $child = Category::create([
            'name' => 'Dress\'s',
            'image' => '/storage/products/1/16310280560.PNG',
            'parent_id' => $parentCategory->id
        ]);
        return 0;
    }
}
