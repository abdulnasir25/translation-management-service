<?php
namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TranslationRepositoryInterface
{
    public function findByKey(string $key, string $locale): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): object;
    public function delete(int $id): bool;
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function getTranslationsForExport(string $locale, array $tags = []): Collection;
    public function bulkCreate(array $translations): bool;
}
