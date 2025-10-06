<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
public function index(Request $request)
{
    $logFile = storage_path('logs/laravel.log');
    $logs = [];

    if (File::exists($logFile)) {
        $lines = File::lines($logFile)->toArray();

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                $logs[] = ['header' => trim($line)];
            }
        }
    }

    // Apply sorting
    $sort = $request->get('sort', 'desc');
    $logs = collect($logs);
    $logs = ($sort === 'asc') ? $logs->sortBy('header') : $logs->sortByDesc('header');

    // Paginate (10 per page)
    $paginatedLogs = new \Illuminate\Pagination\LengthAwarePaginator(
        $logs->forPage(request()->get('page', 1), 10),
        $logs->count(),
        10,
        request()->get('page', 1),
        ['path' => url()->current(), 'query' => request()->query()]
    );

    return view('logs.index', ['errorLogs' => $paginatedLogs]);
}



    public function show($id)
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = File::exists($logPath) ? File::lines($logPath)->toArray() : [];

        $errorLogs = [];
        $currentLog = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                if ($currentLog !== null) {
                    $errorLogs[] = $currentLog;
                }
                $currentLog = $line;
            } else {
                if ($currentLog !== null) {
                    $currentLog .= "\n" . $line;
                }
            }
        }

        if ($currentLog !== null) {
            $errorLogs[] = $currentLog;
        }

        
        $errorDetail = $errorLogs[$id] ?? 'Error not found';

        return view('logs.show', compact('errorDetail'));
    }
}
