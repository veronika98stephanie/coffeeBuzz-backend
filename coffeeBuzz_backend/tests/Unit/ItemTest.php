<?php

namespace Tests\Unit;

use App\Drink;
use App\DrinkName;
use App\DrinkSize;
use App\Food;
use App\Item;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Role::class)->create([
            'id' => 3,
            'role' => 'customer'
        ]);

        factory(User::class)->create([
            'id' => 1,
            'username' => "sue",
            'role_id' => 3,
            'email' => 'Sue@gmail.com',
            'password' => Hash::make("secret"),
            'remember_token' => Str::random(10),
        ]);

        factory(User::class)->create([
            'id' => 2,
            'username' => "susan",
            'role_id' => 3,
            'email' => 'Susan@gmail.com',
            'password' => Hash::make("secret"),
            'remember_token' => Str::random(10),
        ]);

        factory(DrinkName::class)->create([
            'id' =>  1,
            'name' =>  'cappucino',
        ]);

        factory(DrinkName::class)->create([
            'id' =>  2,
            'name' =>  'coffee',
        ]);

        factory(DrinkSize::class)->create([
            'id' => 1,
            'size' => 's',
        ]);

        factory(DrinkSize::class)->create([
            'id' => 2,
            'size' => 'm',
        ]);

        factory(Drink::class)->create([
            'id' => 1,
            'name_id' => 1,
            'size_id' => 1,
            'price' => 5,
        ]);

        factory(Drink::class)->create([
            'id' => 2,
            'name_id' => 1,
            'size_id' => 2,
            'price' => 8,
        ]);

        factory(Drink::class)->create([
            'id' => 3,
            'name_id' => 2,
            'size_id' => 1,
            'price' => 5,
        ]);

        factory(Food::class)->create(
            [
                'id' =>1,
                'name' =>  'Burritos',
                'qty' =>  5,
                'price' => 5,
            ],
            [
                'id' =>2,
                'name' =>  'Sandwich',
                'qty' =>  7,
                'price' => 5,
            ]
        );

        $login = $this->call('POST', 'api/auth/login',
            [
                'username' => 'sue',
                'password' => 'secret',
            ]
        );
        $login->assertStatus(200);

        $response = $this->call('POST', 'api/items',
            [
                'item_type' => 'food',
                'food_id' => 1,
            ],
            $this->transformHeadersToServerVars([ 'Authorization' => $login->json("access_token")])
        );
        $response->assertStatus(200);

        $item = Item::items();
        $this->assertCount(1, $item);

        $json_content = json_decode($response->getContent());
        $item_id = $json_content->data->id;

        $this->assertEquals([
            [
                "id" => $item_id,
                "item_type" => "food",
                "food_id" => 1,
                "drink_id" => null
            ]
        ], $item->toArray());
    }
}
