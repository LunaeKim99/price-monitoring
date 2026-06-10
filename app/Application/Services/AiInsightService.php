<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    private string $apiKey;
    private string $endpoint;
    private string $model;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->endpoint = config('services.groq.endpoint', 'https://api.groq.com/openai/v1');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->timeout = (int) config('services.groq.timeout', 30);
    }

    /**
     * Generate AI-powered market insight summary in Bahasa Indonesia.
     *
     * @param array $predictionsData Array of prediction arrays with keys: commodity, region, price, date, confidence
     * @param array $commodityMap commodityId => name
     * @param array $regionMap regionId => name
     * @return string|null
     */
    public function generateInsight(array $predictionsData, array $commodityMap, array $regionMap): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AiInsightService: GROQ_API_KEY tidak dikonfigurasi.');
            return null;
        }

        if (empty($predictionsData)) {
            return 'Tidak ada data prediksi yang tersedia untuk dianalisis.';
        }

        $prompt = $this->buildPrompt($predictionsData, $commodityMap, $regionMap);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '/chat/completions', [
                    'model' => $this->model,
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

                Log::warning('AiInsightService: Response tidak mengandung konten.', $response->json());
                return null;
            }

            Log::error('AiInsightService: Gagal memanggil API.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AiInsightService: Exception saat memanggil API.', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function buildPrompt(array $predictionsData, array $commodityMap, array $regionMap): string
    {
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

        return $summary;
    }
}
