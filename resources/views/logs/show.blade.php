<!DOCTYPE html>
<html>
<head>
    <title>Error Log Detail</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
            color: #212529;
            font-family: "Inter", "Segoe UI", Tahoma, sans-serif;
        }
        .page-title {
            font-weight: 600;
            font-size: 1.6rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        pre {
            background-color: #f8f9fa;
            color: #212529;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .btn-back {
            background-color: #f39c12;
            border: none;
            padding: 6px 14px;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 6px;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .btn-back:hover {
            background-color: #d68910;
            color: #fff;
        }
        .btn-container {
            display: flex;
            justify-content: flex-end; /* Aligns Back button to right */
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <!-- Title -->
    <h2 class="page-title">Error Log Detail</h2>

    <!-- Back Button (Right Aligned) -->
    <div class="btn-container">
        <a href="{{ route('error_logs.index') }}" class="btn-back">Back</a>
    </div>

    <!-- Log Detail -->
    {{-- <div class="card shadow-sm"> --}}
        <pre>{{ $errorDetail }}</pre>
    {{-- </div> --}}
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
