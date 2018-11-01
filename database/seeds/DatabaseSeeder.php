<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Setting;
use App\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        Permission::create(['name' => 'create articles']);
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);
        Permission::create(['name' => 'edit settings']);

        // create roles and assign created permissions
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'moderator']);
        $role->givePermissionTo(['publish articles', 'unpublish articles']);

        $role = Role::create(['name' => 'writer']);
        $role->givePermissionTo(['edit articles', 'create articles']);

        $role = Role::create(['name' => 'reader']);
        $role->givePermissionTo('create articles');

        $user = User::create([
            'firstname'=>'Abu',
            'lastname' => 'Lawwy',
            'email' => 'abula@gmail.com',
            'password' => bcrypt('abula'),
            'username' => 'abula',
        ]);
        $user->assignRole('super-admin');

        $user = User::create([
            'firstname'=>'habdej',
            'lastname' => 'Lawwy',
            'email' => 'habdej@gmail.com',
            'password' => bcrypt('abula'),
            'username' => 'habdej',
        ]);
        $user->assignRole('moderator');

        $user = User::create([
            'firstname'=>'mama lee',
            'lastname' => 'Lawwy',
            'email' => 'habde@gmail.com',
            'password' => bcrypt('abula'),
            'username' => 'mama',
        ]);
        $user->assignRole('writer');

        $user = User::create([
            'firstname'=>'babdu',
            'lastname' => 'Lawwy',
            'email' => 'abu@gmail.com',
            'password' => bcrypt('abula'),
            'username' => 'babdu',
        ]);
        $user->assignRole('reader');

        setting([
            'sitename' => 'Altechtic Blog Vue',
            'comments' => true,
            'approve_comment' => false,
            'posts_per_page' => 10,
            'comments_per_posts' => 10,
    ])->save();
        $Category = Category::create([
            'name'=>'General',
        ]);
        $Category = Category::create([
            'name'=>'Mobile',
        ]);


    for($i = 1; $i < 100; $i++) {
            App\Post::create([
                'title' => $faker->catchPhrase,
                'slug' => 'this-is-a-test-'.$i,
                'user_id' => $faker->numberBetween(1,4),
                'image' => str_replace('\\', '/', str_replace('public', '', $faker->image('public/images',400,300))),
                'body' => $faker->paragraph,
                'status' => $faker->numberBetween(0,1),
            ]);
        }
      
    }
}
