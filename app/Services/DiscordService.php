<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    public function send(string $webhook, string $message, array $embeds = []) : bool
    {
        try {
            $resp = Http::post($webhook, ['content' => $message, 'embeds' => $embeds]);
            return $resp->successful();
        } catch (\Throwable $e) {
            Log::error('Discord webhook failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
