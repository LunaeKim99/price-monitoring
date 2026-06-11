<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    private string $apiKey;
    private string $endpoint;
    private string $primaryModel;
    private string $fallbackModel;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->endpoint = config('services.groq.endpoint', 'https://api.groq.com/openai/v1');
        $this->primaryModel = config('services.groq.primary_model', 'llama-3.3-70b-versatile');
        $this->fallbackModel = config('services.groq.fallback_model', 'llama-3.1-8b-instant');
        $this->timeout = (int) config('services.groq.timeout', 30);
    }

    /**
     * Generate AI-powered market insight for batch predictions.
     * Original method signature preserved for backward compatibility.
     */
    public function generateInsight(
        array $predictionsData,
        array $commodityMap,
        array $regionMap,
        array $newsContext = []
    ): ?string {
        if (empty($this->apiKey)) {
            Log::warning('AiInsightService: GROQ_API_KEY tidak dikonfigurasi.');
            return null;
        }

        if (empty($predictionsData)) {
            return 'Tidak ada data prediksi yang tersedia untuk dianalisis.';
        }

        $prompt = $this->buildPrompt($predictionsData, $commodityMap, $regionMap, $newsContext);
        return $this->callWithFallback($prompt);
    }

    /**
     * Generate AI-powered dashboard market summary.
     */
    public function generateDashboardInsight(array $data): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AiInsightService: GROQ_API_KEY tidak dikonfigurasi.');
            return null;
        }

        if (empty($data)) {
            Log::warning('AiInsightService: Data kosong untuk dashboard insight.');
            return null;
        }

        $prompt = $this->buildDashboardInsightPrompt($data);
        return $this->callWithFallback($prompt);
    }

    /**
     * Try primary model first, fallback on failure.
     */
    private function callWithFallback(string $prompt): ?string
    {
        $result = $this->tryModel($this->primaryModel, $prompt);

        if ($result !== null) {
            Log::info('AiInsightService: Primary model berhasil.', ['model' => $this->primaryModel]);
            return $result;
        }

        Log::info('AiInsightService: Mencoba fallback model.', ['model' => $this->fallbackModel]);

        $result = $this->tryModel($this->fallbackModel, $prompt);

        if ($result !== null) {
            return $result;
        }

        Log::error('AiInsightService: Semua model gagal.');
        return null;
    }

    /**
     * Execute a single model API call.
     */
    private function tryModel(string $model, string $prompt): ?string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->post(rtrim($this->endpoint, '/') . '/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten analis pasar komoditas yang memberikan ringkasan tren harga dalam Bahasa Indonesia. Berikan jawaban dalam bentuk paragraf naratif yang natural, bukan poin-poin atau markdown.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                if ($content) {
                    return trim($content);
                }

                Log::warning('AiInsightService: Response tidak mengandung konten.', [
                    'model' => $model,
                    'response' => $response->json(),
                ]);
                return null;
            }

            Log::error('AiInsightService: Gagal memanggil API.', [
                'model' => $model,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AiInsightService: Exception saat memanggil API.', [
                'model' => $model,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function buildPrompt(
        array $predictionsData,
        array $commodityMap,
        array $regionMap,
        array $newsContext = []
    ): string {
        $summary = "Berikut adalah data prediksi harga komoditas terbaru:\n\n";

        foreach ($predictionsData as $data) {
            $commodityName = $commodityMap[$data['commodity_id']] ?? 'Komoditas #' . $data['commodity_id'];
            $regionName = $regionMap[$data['region_id']] ?? 'Region #' . $data['region_id'];
            $summary .= "- {$commodityName} di {$regionName}: Rp " . number_format((float) $data['price'], 0, ',', '.') . " (prediksi: {$data['date']}, confidence: " . ($data['confidence'] * 100) . "%)\n";
        }

        $summary .= "\nBerdasarkan data di atas, berikan ringkasan analisis yang mencakup:\n";
        $summary .= "1. Tren harga secara umum (naik/turun/stabil)\n";
        $summary .= "2. Tiga komoditas dengan pergerakan harga paling signifikan\n";
        $summary .= "3. Tingkat kepercayaan (confidence) secara keseluruhan\n";
        $summary .= "Gunakan bahasa Indonesia yang natural. Jawab dalam bentuk paragraf naratif saja, tanpa markdown atau bullet points.";

        if (!empty($newsContext)) {
            $summary .= "\n\n=== KONTEKS BERITA TERBARU ===\n";
            $summary .= "Berita terkait komoditas dalam 7 hari terakhir:\n\n";
            foreach (array_slice($newsContext, 0, 8) as $news) {
                $summary .= "- {$news['title']} ({$news['source']})\n";
            }
            $summary .= "\nGunakan konteks berita di atas jika relevan dengan analisis harga.\n";
        }

        return $summary;
    }

    public function buildDashboardInsightPrompt(array $data): string
    {
        $prompt = "Berikut adalah data terkini pasar komoditas:\n\n";
        $prompt .= "- Total komoditas: {$data['total_commodities']}\n";
        $prompt .= "- Total wilayah: {$data['total_regions']}\n";
        $prompt .= "- Total data harga: {$data['total_price_records']}\n";
        $prompt .= "- Rata-rata harga: Rp {$data['average_price']}\n";
        $prompt .= "- Arah tren: {$data['trend_direction']}\n";

        if (!empty($data['trending_commodities'])) {
            $prompt .= "- Komoditas terpopuler: " . implode(', ', $data['trending_commodities']) . "\n";
        }

        $prompt .= "\nBerdasarkan data di atas, berikan ringkasan kondisi pasar komoditas saat ini dalam Bahasa Indonesia. Jawab dalam bentuk paragraf naratif natural (bukan poin atau markdown). Sertakan sentimen pasar secara keseluruhan dan rekomendasi singkat.";

        return $prompt;
    }
}
