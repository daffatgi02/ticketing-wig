<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Kerja dan Biaya (RKB)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #666;
        }
        .content {
            text-align: justify;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            background-color: #f5f5f5;
            padding: 5px 10px;
            border-left: 4px solid #0066cc;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 180px;
        }
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        .approval-table th, .approval-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        .approval-table th {
            background-color: #f5f5f5;
        }
        .sign-area {
            height: 80px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RENCANA KERJA DAN BIAYA (RKB)</h1>
        <h2>Nomor: RKB/{{ $ticket->ticket_id }}/{{ date('Y') }}</h2>
    </div>

    <div class="content">
        <table class="info-table">
            <tr>
                <td>ID Tiket</td>
                <td>: {{ $ticket->ticket_id }}</td>
            </tr>
            <tr>
                <td>Kategori Permasalahan</td>
                <td>: {{ $ticket->category->name }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>: {{ now()->format('d F Y') }}</td>
            </tr>
        </table>

        <div class="section">
            <h3>INFORMASI PELAPOR</h3>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>: {{ $ticket->user->name }}</td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: {{ $ticket->user->department ? $ticket->user->department->name : '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Laporan</td>
                    <td>: {{ $ticket->created_at->format('d F Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>INFORMASI PETUGAS</h3>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>: {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan' }}</td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: {{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>DESKRIPSI PERMASALAHAN</h3>
            <p>{{ $ticket->description }}</p>
        </div>

        <div class="section">
            <h3>REKOMENDASI SOLUSI</h3>
            @php
                $lastSupportComment = $ticket->comments->where('user_id', $ticket->assigned_to)->last();
            @endphp

            @if($lastSupportComment)
                <p>{{ $lastSupportComment->comment }}</p>
            @else
                <p>Belum ada rekomendasi yang diberikan.</p>
            @endif
        </div>

        <div class="section">
            <h3>ESTIMASI KEBUTUHAN</h3>
            <p><i>(Detail kebutuhan barang/jasa yang diperlukan akan diisi manual setelah dokumen diekspor)</i></p>
        </div>

        <div class="section">
            <h3>PERSETUJUAN</h3>
            <table class="approval-table">
                <tr>
                    <th width="33%">Dibuat oleh</th>
                    <th width="33%">Mengetahui</th>
                    <th width="33%">Menyetujui</th>
                </tr>
                <tr>
                    <td class="sign-area"></td>
                    <td class="sign-area"></td>
                    <td class="sign-area"></td>
                </tr>
                <tr>
                    <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : '___________________' }}</td>
                    <td>___________________</td>
                    <td>___________________</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Dokumen ini dibuat secara otomatis pada {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>
