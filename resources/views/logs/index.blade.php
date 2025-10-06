<!DOCTYPE html>
<html>
<head>
    <title>Error Logs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa; /* Light background */
            color: #212529;
            font-family: "Inter", "Segoe UI", Tahoma, sans-serif;
        }
        .page-title {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead {
            background-color: #f1f1f1;
            color: #2c3e50;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        .table tbody td {
            color: #495057;
            font-size: 0.9rem;
        }
        .btn-sort {
            background-color: #f39c12;
            border: none;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 6px;
            color: #fff;
        }
        .btn-sort:hover {
            background-color: #d68910;
            color: #fff;
        }
        .btn-view {
            background-color: #f39c12;
            border: none;
            padding: 4px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
            color: #fff;
        }
        .btn-view:hover {
            background-color: #d68910;
            color: #fff;
        }
        .badge-custom {
            background: #f1f1f1;
            color: #2c3e50;
            font-size: 0.8rem;
            border-radius: 5px;
            padding: 6px 12px;
            border: 1px solid #dee2e6;
        }
        /* Pagination Styling */
        .pagination {
            margin-top: 15px;
        }
        .pagination .page-link {
            background-color: #fff;
            border: 1px solid #dee2e6;
            color: #2c3e50;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }
        .pagination .page-link:hover {
            background-color: #f8f9fa;
            color: #f39c12;
        }
        .pagination .active .page-link {
            background-color: #f39c12 !important;
            border-color: #f39c12 !important;
            color: #fff !important;
            font-weight: 600;
        }
        .pagination .disabled .page-link {
            background-color: #fff;
            color: #adb5bd;
            border: 1px solid #dee2e6;
        }
        .btn-sort,
.btn-view {
    text-decoration: none !important;
    display: inline-block;
}

    </style>
</head>
<body>

<div class="container py-5">
    <!-- Title -->
    <h2 class="page-title">Error Logs</h2>

    <!-- Sorting & Total -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('error_logs.index', ['sort' => 'asc']) }}" class="btn-sort">Oldest</a>
            <a href="{{ route('error_logs.index', ['sort' => 'desc']) }}" class="btn-sort">Newest</a>
        </div>
        <div>
            <span class="badge-custom">Total Logs: {{ $errorLogs->total() }}</span>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Log Entry</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($errorLogs as $index => $log)
                        <tr>
                            <td>{{ $log['header'] }}</td>
                            <td class="text-center">
                                <a href="{{ route('error_logs.show', $index) }}" class="btn-view">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">No error logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Pagination -->

    {{ $errorLogs->appends(['sort' => request('sort')])->links('pagination::bootstrap-5') }}


</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
