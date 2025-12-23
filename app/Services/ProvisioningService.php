<?php

namespace App\Services;

use App\Plugins\PluginManager;
use Illuminate\Support\Facades\Log;

class ProvisioningService
{
    protected PluginManager $manager;

    public function __construct(PluginManager $manager)
    {
        $this->manager = $manager;
        $this->manager->discover();
    }

    protected function pluginForInvoice($invoice)
    {
        $module = $invoice->server_module ?? 'proxmox';
        $meta = $this->manager->get($module);
        return $meta;
    }

    public function createService($invoice): array
    {
        $meta = $this->pluginForInvoice($invoice);

        if (!$meta) {
            Log::error('No server plugin for invoice', ['invoice_id' => $invoice->id, 'module' => $invoice->server_module]);
            return ['success' => false, 'message' => 'no_plugin'];
        }

        // Plugin should provide HTTP API endpoint or class to call; for now we provide a generic hook via a command
        // Real implementation will call plugin's API client
        // For skeleton, return success with a fake service id
        return ['success' => true, 'service_id' => 'srv-' . uniqid()];
    }

    public function suspend($invoice): bool
    {
        $meta = $this->pluginForInvoice($invoice);
        if (!$meta) {
            Log::error('No server plugin for suspend', ['invoice_id' => $invoice->id]);
            return false;
        }

        // call plugin suspend API - skeleton
        return true;
    }

    public function unsuspend($invoice): bool
    {
        $meta = $this->pluginForInvoice($invoice);
        if (!$meta) {
            Log::error('No server plugin for unsuspend', ['invoice_id' => $invoice->id]);
            return false;
        }

        return true;
    }

    public function terminate($invoice): bool
    {
        $meta = $this->pluginForInvoice($invoice);
        if (!$meta) {
            Log::error('No server plugin for terminate', ['invoice_id' => $invoice->id]);
            return false;
        }

        return true;
    }
}
