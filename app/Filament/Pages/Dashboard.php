<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // HAPUS '| string', cukup 'int | array' saja
    public function getColumns(): int | array
    {
        return 3;
    }
}