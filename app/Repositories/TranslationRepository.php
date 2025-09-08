<?php
namespace App\Repositories;

use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\Locale;
use App\Models\Tag;
use App\Repositories\Contracts\TranslationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TranslationRepository implements TranslationRepositoryInterface
{
    /**
     * Find translation by key and locale
     */
    public function findByKey(string $key, string $locale): ?object
    {
        return Translation::query()
            ->select(['translations.*', 'translation_keys.key', 'locales.code'])
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->join('locales', 'translations.locale_id', '=', 'locales.id')
            ->where('translation_keys.key', $key)
            ->where('locales.code', $locale)
            ->where('translations.is_active', true)
            ->first();
    }

    /**
     * Create a new translation
     */
    public function create(array $data): object
    {
        return DB::transaction(function () use ($data) {
            // Find or create translation key
            $translationKey = TranslationKey::firstOrCreate(
                ['key' => $data['key']],
                ['description' => $data['description'] ?? null]
            );

            // Find or create locale
            $locale = Locale::firstOrCreate(
                ['code' => $data['locale']],
                ['name' => ucfirst($data['locale'])]
            );

            // Create or update translation
            $translation = Translation::updateOrCreate(
                [
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id
                ],
                ['content' => $data['content']]
            );

            // Attach tags if provided
            if (!empty($data['tags'])) {
                $tagIds = $this->getOrCreateTags($data['tags']);
                $translationKey->tags()->sync($tagIds);
            }

            // Clear cache for this locale
            $this->clearTranslationCache($data['locale']);

            return $translation->load(['translationKey', 'locale']);
        });
    }

    /**
     * Update an existing translation
     */
    public function update(int $id, array $data): object
    {
        return DB::transaction(function () use ($id, $data) {
            $translation = Translation::findOrFail($id);

            // Update content
            $translation->update(['content' => $data['content']]);

            // Update tags if provided
            if (isset($data['tags'])) {
                $tagIds = $this->getOrCreateTags($data['tags']);
                $translation->translationKey->tags()->sync($tagIds);
            }

            // Clear cache for this locale
            $this->clearTranslationCache($translation->locale->code);

            return $translation->load(['translationKey', 'locale']);
        });
    }

    /**
     * Delete a translation
     */
    public function delete(int $id): bool
    {
        $translation = Translation::findOrFail($id);
        $localeCode = $translation->locale->code;

        $result = $translation->delete();

        if ($result) {
            $this->clearTranslationCache($localeCode);
        }

        return $result;
    }

    /**
     * Search translations with filters and pagination
     */
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Translation::query()
            ->select([
                'translations.*',
                'translation_keys.key',
                'locales.code as locale_code'
            ])
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->join('locales', 'translations.locale_id', '=', 'locales.id')
            ->where('translations.is_active', true);

        // Apply filters
        if (!empty($filters['key'])) {
            $query->where('translation_keys.key', 'like', '%' . $filters['key'] . '%');
        }

        if (!empty($filters['content'])) {
            // Use LIKE for content search (can be optimized with full-text search later)
            $query->where('translations.content', 'like', '%' . $filters['content'] . '%');
        }

        if (!empty($filters['locale'])) {
            $query->where('locales.code', $filters['locale']);
        }

        if (!empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
            $query->whereHas('translationKey.tags', function ($q) use ($tags) {
                $q->whereIn('tags.name', $tags);
            });
        }

        return $query->with(['translationKey.tags', 'locale'])
                    ->orderBy('translations.updated_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Get translations for export (optimized for frontend)
     */
    public function getTranslationsForExport(string $locale, array $tags = []): Collection
    {
        $cacheKey = "translations_export_{$locale}_" . md5(implode(',', $tags));

        return Cache::remember($cacheKey, 300, function () use ($locale, $tags) {
            $query = Translation::query()
                ->select([
                    'translation_keys.key',
                    'translations.content'
                ])
                ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
                ->join('locales', 'translations.locale_id', '=', 'locales.id')
                ->where('locales.code', $locale)
                ->where('translations.is_active', true);

            if (!empty($tags)) {
                $query->whereHas('translationKey.tags', function ($q) use ($tags) {
                    $q->whereIn('tags.name', $tags);
                });
            }

            return $query->get();
        });
    }

    /**
     * Bulk create translations (for testing/seeding)
     */
    public function bulkCreate(array $translations): bool
    {
        return DB::transaction(function () use ($translations) {
            try {
                // Process in chunks to avoid memory issues
                $chunks = array_chunk($translations, 1000);

                foreach ($chunks as $chunk) {
                    foreach ($chunk as $translationData) {
                        $this->create($translationData);
                    }
                }

                // Clear all translation caches after bulk insert
                Cache::flush();

                return true;
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    /**
     * Get or create tags and return their IDs
     */
    private function getOrCreateTags(array $tagNames): array
    {
        $tags = collect($tagNames)->map(function ($tagName) {
            return Tag::firstOrCreate(
                ['name' => trim($tagName)],
                ['description' => 'Auto-created tag']
            );
        });

        return $tags->pluck('id')->toArray();
    }

    /**
     * Clear translation cache for a specific locale
     */
    private function clearTranslationCache(string $localeCode): void
    {
        // Clear specific cache patterns (in production, use more sophisticated cache tagging)
        $patterns = [
            "translations_export_{$localeCode}_*"
        ];

        // For simplicity, we'll clear all cache
        Cache::flush();
    }
}
