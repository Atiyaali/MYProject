<?php

namespace App\Filament\Resources\Batches\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class JobsCounter extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jobs', DB::table('jobs')->count())
                ->description('Remaining Jobs in the queue')
                ->color('success'),
        ];
    }
}

