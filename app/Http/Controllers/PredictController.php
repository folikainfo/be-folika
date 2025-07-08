<?php

namespace App\Http\Controllers;

use App\Models\Advice;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Cloudinary\Cloudinary;

class PredictController extends Controller
{
    /* ───────────────────────────────────────── store() ───────────────────────────────────────── */
    public function store(Request $request)
    {
        /* 1. Validasi file */
        $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $user = $request->user();

        /* 2. Upload ke Cloudinary */
        $cloudinary = new Cloudinary();
        $upload     = $cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder'     => "users/{$user->id}/histories",
                'invalidate' => true,
            ]
        );

        $imageUrl      = $upload['secure_url'];
        $imagePublicId = $upload['public_id'];
        $fileName      = basename($imageUrl);

        /* 3. Panggil ML API */
        $ml = Http::attach(
            'file',
            file_get_contents($request->file('image')->getRealPath()),
            $fileName
        )
            ->post(config('services.ml.endpoint', env('ML_ENDPOINT')))
            ->throw()
            ->json();

        /* 4. Kalau ML me‑return warning → abort 422 */
        if (
            isset($ml['message']) &&
            str_contains(strtolower($ml['message']), 'bukan gambar rambut')
        ) {
            return response()->json(['message' => $ml['message']], 422);
        }

        /* 5. Normalisasi & whitelist label */
        $prediction = trim($ml['prediction'] ?? '');
        $confidence = $ml['confidence'] ?? null;
        $normalized = strtolower($prediction);

        $allowed = [
            'normal',
            'lichen planopilaris',
            'psoriasis',
            'rusak',
            'folliculitis',
        ];

        if (! in_array($normalized, $allowed)) {
            return response()->json([
                'message' => 'Peringatan! Foto yang Anda unggah bukan gambar rambut. '
                    . 'Mohon unggah foto kulit kepala atau rambut agar dapat dideteksi dengan benar.',
            ], 422);
        }

        /* 6. Ambil Advice (semua sudah ada lewat seeder) */
        $advice = Advice::where('prediction', $normalized)->firstOrFail();

        /* 7. Simpan History */
        $history = History::create([
            'users_id' => auth()->user()->id,
            'advices_id'      => $advice->id,
            'image_url'       => $imageUrl,
            'image_public_id' => $imagePublicId,
            'prediction'      => ucfirst($normalized),
            'confidence'      => $confidence,
        ])->load('advice');

        /* 8. Response sukses */
        return response()->json([
            'message' => 'Prediction saved to history!',
            'history' => $history->load('advice'),
        ], 201);
    }

    /* ───────────────────────────────────────── index() ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $user = $request->user();

        $histories = History::with('advice')
            ->where('users_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'message'   => 'Histori Berhasil Ditampilkan',
            'histories' => $histories,
        ]);
    }

    /* ───────────────────────────────────────── latestHistory() ───────────────────────────────── */
    public function latestHistory(Request $request)
    {
        $user = $request->user();

        $latest = History::with('advice')
            ->where('users_id', $user->id)
            ->latest()
            ->first();

        return response()->json([
            'message' => $latest ? 'Riwayat terbaru berhasil ditampilkan.' : 'Belum ada riwayat.',
            'history' => $latest?->load('advice'),
        ], 200);
    }
}
