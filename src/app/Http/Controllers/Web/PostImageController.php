<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostImageController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $file = $request->file('image');
        if (is_array($file)) {
            return response()->json(['error' => 'Image upload failed.'], 400);
        }

        $path = $file->store('images/posts', 'public');
        if (!$path) {
            return response()->json(['error' => 'Unable to process the uploaded image.'], 422);
        }

        return response()->json([
            'path' => $path,
            'url' => Storage::url($path),
        ], 201);
    }
}
