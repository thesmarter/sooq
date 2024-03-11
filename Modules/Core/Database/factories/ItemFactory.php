<?php

namespace Modules\Core\Database\factories;

use Carbon\Carbon;
use App\Models\User;
use Modules\Core\Entities\Item;
use Modules\Core\Entities\ItemInfo;
use Modules\Core\Entities\Shop;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\Subcategory;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\LocationTownship;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = Category::all()->random();
        $city = LocationCity::all()->random();
        $price = $this->faker->numberBetween($min = 10, $max = 9000);
        $items = [
            'title' => ucwords($this->faker->word),
            'category_id' => $category->id,
            'subcategory_id' => Subcategory::where('category_id', $category->id)->get()->random(),
            'currency_id' => Currency::all()->random(),
            'shop_id' => Shop::all()->random(),
            'location_city_id' => $city->id,
            'location_township_id' => LocationTownship::where('location_city_id', $city->id)->get()->random(),
            'price' => $price,
            'original_price' => $price,
            'description' => $this->faker->paragraph,
            'search_tag' => $this->faker->word,
            'lat' => $this->faker->latitude($min = -90, $max = 90),
            'lng' => $this->faker->longitude($min = -180, $max = 180),
            'status' =>1,
            'ordering' => $this->faker->numberBetween($min = 1, $max = 10),
            'is_available' => $this->faker->numberBetween($min = 0, $max = 1),
            'item_touch_count' => $this->faker->numberBetween($min = 1, $max = 300),
            'favourite_count' => $this->faker->numberBetween($min = 1, $max = 300),
            'overall_rating' => $this->faker->numberBetween($min = 0, $max = 5),
            'added_user_id' => User::all()->random(),
            'added_date'=>Carbon::now(),
            ];

        return $items;
    }
}

