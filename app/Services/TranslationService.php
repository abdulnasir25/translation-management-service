<?php
namespace App\Services;

use App\Repositories\Contracts\TranslationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TranslationService
{
    public function __construct(
        private TranslationRepositoryInterface $translationRepository
    ) {}

    public function createTranslation(array $data): object
    {
        $this->validateTranslationData($data);
        return $this->translationRepository->create($data);
    }

    public function updateTranslation(int $id, array $data): object
    {
        $this->validateTranslationUpdateData($data);
        return $this->translationRepository->update($id, $data);
    }

    public function deleteTranslation(int $id): bool
    {
        return $this->translationRepository->delete($id);
    }

    public function searchTranslations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->translationRepository->search($filters, $perPage);
    }

    public function exportTranslations(string $locale, array $tags = []): array
    {
        $translations = $this->translationRepository->getTranslationsForExport($locale, $tags);

        return $translations->pluck('content', 'key')->toArray();
    }

    public function getTranslation(string $key, string $locale): ?string
    {
        $translation = $this->translationRepository->findByKey($key, $locale);
        return $translation?->content;
    }

    private function validateTranslationData(array $data): void
    {
        if (empty($data['key'])) {
            throw new \InvalidArgumentException('Translation key is required');
        }

        if (empty($data['locale'])) {
            throw new \InvalidArgumentException('Locale is required');
        }

        if (empty($data['content'])) {
            throw new \InvalidArgumentException('Content is required');
        }
    }

    private function validateTranslationUpdateData(array $data): void
    {
        if (empty($data['content'])) {
            throw new \InvalidArgumentException('Content is required');
        }
    }
}
