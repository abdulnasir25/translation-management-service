<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationService $translationService
    ) {}

    // List / search
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['key', 'content', 'locale', 'tags']);
        $perPage = (int) $request->get('per_page', 15);

        $translations = $this->translationService->searchTranslations($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => $translations->items(),
            'pagination' => [
                'current_page' => $translations->currentPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
                'last_page' => $translations->lastPage()
            ]
        ]);
    }

    // Create
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required|string',
            'locale' => 'required|string',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string'
        ]);

        try {
            $translation = $this->translationService->createTranslation($data);

            return response()->json([
                'status' => 'success',
                'data' => $translation
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'description' => 'nullable|string'
        ]);

        try {
            $translation = $this->translationService->updateTranslation($id, $data);

            return response()->json([
                'status' => 'success',
                'data' => $translation
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->translationService->deleteTranslation($id);

            return response()->json([
                'status' => $deleted ? 'success' : 'failed',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Show specific translation by key + locale
    public function show(string $key, string $locale): JsonResponse
    {
        try {
            $content = $this->translationService->getTranslation($key, $locale);

            if (is_null($content)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Translation not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'key' => $key,
                    'locale' => $locale,
                    'content' => $content
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Export
    public function export(Request $request, string $locale): JsonResponse
    {
        try {
            // Handle tags parameter (can be string or array)
            $tags = $request->get('tags', []);
            if (is_string($tags)) {
                $tags = explode(',', $tags);
                $tags = array_map('trim', $tags);
                $tags = array_filter($tags);
            }

            // Get translations from service
            $translations = $this->translationService->exportTranslations($locale, $tags);

            return response()->json([
                'status' => 'success',
                'locale' => $locale,
                'tags' => $tags,
                'data' => $translations,
                'count' => count($translations),
                'exported_at' => now()->toISOString()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error exporting translations: ' . $e->getMessage()
            ], 500);
        }
    }
}
