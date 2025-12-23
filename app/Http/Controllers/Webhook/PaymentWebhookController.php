<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use App\Plugins\PluginManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected PluginManager $manager;

    public function __construct(PluginManager $manager)
    {
        $this->manager = $manager;
        $this->manager->discover();
    }

    public function handle(Request $request, $pluginKey)
    {
        $meta = $this->manager->get($pluginKey);
        if (!$meta) {
            Log::warning('Webhook for unknown plugin', ['plugin' => $pluginKey]);
            return response()->json(['error' => 'unknown_plugin'], 404);
        }

        $class = $meta['class'] ?? null;
        if (!$class || !class_exists($class)) {
            Log::warning('Webhook plugin class missing', ['plugin' => $pluginKey]);
            return response()->json(['error' => 'plugin_missing'], 500);
        }

        $plugin = app($class);

        if (!method_exists($plugin, 'handleWebhook')) {
            Log::warning('Webhook plugin missing handler', ['plugin' => $pluginKey]);
            return response()->json(['error' => 'handler_missing'], 500);
        }

        $result = $plugin->handleWebhook($request);

        if (!empty($result['handled'])) {
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ignored', 'message' => $result['message'] ?? null], 200);
    }
}
