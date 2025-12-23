<?php

namespace App\Plugins;

interface PluginInterface
{
    public function key(): string;
    public function name(): string;
    public function type(): string; // payment | server | notification
    public function enabled(): bool;
    public function config(): array;
}
