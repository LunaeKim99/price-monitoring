<?php

namespace App\Application\Services;

use App\Models\NewsArticle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsScraperService
{
    private const RSS_SOURCES = [
        'kontan'       => 'https://rss.kontan.co.id/category/nasional',
        'detik_finance' => 'https://finance.detik.com/rss',
        'google_news'  => 'https://news.google.com/rss/search?q=harga+bahan+pokok&hl=id&gl=ID&ceid=ID:id',
    ];

    private const RELEVANCE_KEYWORDS = [
        'harga', 'beras', 'cabai', 'bawang', 'minyak goreng',
        'gula', 'tepung', 'daging', 'telur', 'komoditas', 'pangan', 'inflasi',
    ];

    private const COMMODITY_KEYWORDS = [
        'beras', 'cabai', 'bawang merah', 'bawang putih',
        'minyak goreng', 'gula pasir', 'tepung terigu',
        'daging ayam', 'daging sapi', 'telur',
    ];

    public function scrape(): array
    {
        $totalFetched = 0;
        $totalSaved   = 0;
        $totalSkipped = 0;
        $totalErrors  = 0;
        $sources      = 0;

        foreach (self::RSS_SOURCES as $sourceName => $url) {
            $sources++;
            try {
                $items = $this->fetchRss($url);

                foreach ($items as $item) {
                    $totalFetched++;

                    if (!$this->isRelevant($item['title'], $item['summary'])) {
                        $totalSkipped++;
                        continue;
                    }

                    if ($this->saveArticle($item, $sourceName)) {
                        $totalSaved++;
                    } else {
                        $totalSkipped++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('NewsScraperService: Gagal scrape source.', [
                    'source'  => $sourceName,
                    'message' => $e->getMessage(),
                ]);
                $totalErrors++;
            }
        }

        Log::info('NewsScraperService: Scraping selesai.', [
            'sources' => $sources,
            'fetched' => $totalFetched,
            'saved'   => $totalSaved,
            'skipped' => $totalSkipped,
            'errors'  => $totalErrors,
        ]);

        return compact('sources', 'totalFetched', 'totalSaved', 'totalSkipped', 'totalErrors');
    }

    public function fetchRss(string $url): array
    {
        $response = Http::timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; PriceMonitorBot/1.0)',
                'Accept'     => 'application/rss+xml, application/xml, text/xml',
            ])
            ->get($url);

        if (!$response->successful()) {
            Log::warning('NewsScraperService: RSS HTTP {status}', ['url' => $url, 'status' => $response->status()]);
            return [];
        }

        $xml = simplexml_load_string($response->body());
        if ($xml === false) {
            Log::warning('NewsScraperService: Gagal parse XML.', ['url' => $url]);
            return [];
        }

        $items = [];

        // RSS 2.0: <channel><item>
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $parsed = $this->parseRssItem($item);
                if ($parsed) {
                    $items[] = $parsed;
                }
            }
        }

        // Atom: <feed><entry>
        if (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $parsed = $this->parseAtomEntry($entry);
                if ($parsed) {
                    $items[] = $parsed;
                }
            }
        }

        return $items;
    }

    private function parseRssItem(\SimpleXMLElement $item): ?array
    {
        $title = trim((string) $item->title);
        $link  = trim((string) $item->link);

        if (empty($title) || empty($link)) {
            return null;
        }

        return [
            'title'        => $title,
            'url'          => $link,
            'summary'      => strip_tags(trim((string) ($item->description ?? ''))),
            'published_at' => $this->parseDate((string) ($item->pubDate ?? '')),
        ];
    }

    private function parseAtomEntry(\SimpleXMLElement $entry): ?array
    {
        $title = trim((string) $entry->title);

        // Atom <link> has href attribute
        $link = '';
        if (isset($entry->link)) {
            foreach ($entry->link as $l) {
                $attrs = $l->attributes();
                if (isset($attrs['href'])) {
                    $link = trim((string) $attrs['href']);
                    break;
                }
            }
        }

        if (empty($title) || empty($link)) {
            return null;
        }

        return [
            'title'        => $title,
            'url'          => $link,
            'summary'      => strip_tags(trim((string) ($entry->summary ?? $entry->content ?? ''))),
            'published_at' => $this->parseDate((string) ($entry->published ?? $entry->updated ?? '')),
        ];
    }

    private function parseDate(string $dateStr): ?Carbon
    {
        if (empty($dateStr)) {
            return null;
        }

        try {
            return Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isRelevant(string $title, string $summary): bool
    {
        $text = strtolower($title . ' ' . $summary);

        foreach (self::RELEVANCE_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function extractCommodityTags(string $title, string $summary): array
    {
        $text = strtolower($title . ' ' . $summary);
        $tags = [];

        foreach (self::COMMODITY_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                $tags[] = $keyword;
            }
        }

        return array_unique($tags);
    }

    private function saveArticle(array $item, string $sourceName): bool
    {
        if (NewsArticle::where('url', $item['url'])->exists()) {
            return false;
        }

        $tags = $this->extractCommodityTags($item['title'], $item['summary']);

        NewsArticle::create([
            'title'          => mb_substr($item['title'], 0, 500),
            'url'            => mb_substr($item['url'], 0, 1000),
            'source'         => $sourceName,
            'published_at'   => $item['published_at'],
            'summary'        => mb_substr($item['summary'] ?? '', 0, 2000),
            'commodity_tags' => $tags,
            'is_relevant'    => true,
        ]);

        return true;
    }
}
