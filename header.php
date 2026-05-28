<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      
        body { 
            background: url('assets/img/IMG-20240309-WA0019-1068x712.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;   
        }
            
        .container-login {
            height: 100%;
            display: flex;
            align-items: center; /* Membuat ke tengah secara vertikal */
            justify-content: center; /* Membuat ke tengah secara horizontal */
        }
        .card-login {
            background: rgba(255, 255, 255, 0.52);
            backdrop-filter: blur (15px);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .btn-custom {
            background: #dc2b2bff;
            color: white;
            border-radius: 10px;
            padding: 10px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #1c22d1ff;
            color: white;
        }
        .alert-custom {
            background-color: #fce4e4;
            color: #cc0033;
            border: 1px solid #f84c4c;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>