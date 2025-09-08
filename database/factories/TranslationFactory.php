<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'locale_id' => Locale::factory(),
            'content' => $this->faker->sentence(),
            'is_active' => true
        ];
    }
}
