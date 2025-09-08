<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\TranslationKey;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;

class PopulateTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translations-populate {count=100000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate database with translations for testing';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = (int) $this->argument('count');
        $this->info("Populating {$count} translations...");

        DB::transaction(function () use ($count) {
            // Create locales
            $locales = [
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'fr', 'name' => 'French'],
                ['code' => 'es', 'name' => 'Spanish'],
                ['code' => 'de', 'name' => 'German'],
                ['code' => 'it', 'name' => 'Italian'],
            ];

            foreach ($locales as $locale) {
                Locale::firstOrCreate(['code' => $locale['code']], $locale);
            }

            // Create tags
            $tags = ['mobile', 'desktop', 'web', 'api', 'admin', 'user'];
            foreach ($tags as $tagName) {
                Tag::firstOrCreate(['name' => $tagName]);
            }

            // Create translation keys and translations
            $batchSize = 1000;
            $batches = ceil($count / $batchSize);

            for ($i = 0; $i < $batches; $i++) {
                $currentBatchSize = min($batchSize, $count - ($i * $batchSize));
                $this->createTranslationBatch($currentBatchSize);

                $this->info("Created batch " . ($i + 1) . " of {$batches}");
            }
        });

        $this->info("Successfully populated {$count} translations!");
    }

    private function createTranslationBatch(int $size): void
    {
        $locales = Locale::all();
        $tags = Tag::all();

        for ($i = 0; $i < $size; $i++) {
            $key = 'test.key.' . uniqid();

            $translationKey = TranslationKey::create([
                'key' => $key,
                'description' => 'Test translation key ' . $i
            ]);

            // Add random tags
            $randomTags = $tags->random(rand(1, 3));
            $translationKey->tags()->attach($randomTags->pluck('id'));

            // Create translations for each locale
            foreach ($locales as $locale) {
                Translation::create([
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id,
                    'content' => "Test content {$i} in {$locale->name}"
                ]);
            }
        }
    }
}
