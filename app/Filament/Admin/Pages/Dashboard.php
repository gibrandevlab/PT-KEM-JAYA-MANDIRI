<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<string, int|null>
     */
    public function getColumns(): int | array
    {
        return ['default' => 1, 'md' => 2, '2xl' => 4];
    }
}
