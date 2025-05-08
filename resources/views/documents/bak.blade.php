<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Kejadian (BAK)</title>
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
        .footer {
            margin-top: 80px;
        }
        .signature {
            width: 50%;
            text-align: center;
            float: right;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA KEJADIAN (BAK)</h1>
        <h2>Nomor: BAK/{{ $ticket->ticket_id }}/{{ date('Y') }}</h2>
    </div>

    <div class="content">
        <p>
            Pada hari ini {{ now()->format('l') }} tanggal {{ now()->format('d F Y') }}, yang bertanda tangan di bawah ini:
        </p>

        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>: {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $ticket->assignedTo ? $ticket->assignedTo->position : '-' }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-' }}</td>
            </tr>
        </table>

        <p>
            Telah melakukan pengecekan atas laporan kerusakan/gangguan dengan detail sebagai berikut:
        </p>

        <table class="info-table">
            <tr>
                <td>ID Tiket</td>
                <td>: {{ $ticket->ticket_id }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: {{ $ticket->category->name }}</td>
            </tr>
            <tr>
                <td>Dilaporkan oleh</td>
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

        <div class="section">
            <h3>DESKRIPSI KEJADIAN/PERMASALAHAN</h3>
            <p>{{ $ticket->description }}</p>
        </div>

        <div class="section">
            <h3>TINDAKAN YANG SUDAH DILAKUKAN</h3>
            @php
                $supportComments = $ticket->comments->where('user_id', $ticket->assigned_to);
            @endphp

            @if($supportComments->count() > 0)
                <ul>
                    @foreach($supportComments as $comment)
                        <li>{{ $comment->comment }}</li>
                    @endforeach
                </ul>
            @else
                <p>Belum ada tindakan yang dilakukan.</p>
            @endif
        </div>

        <div class="section">
            <h3>KESIMPULAN</h3>
            <p>
                Berdasarkan pengecekan yang dilakukan, permasalahan ini memerlukan dukungan dari pihak eksternal dengan alasan sebagai berikut:
            </p>
            <p>{{ $ticket->external_support_reason ?: 'Belum ada alasan yang dicatat.' }}</p>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Yang membuat berita acara,</p>
                <br><br><br><br>
                <p>{{ $ticket->assignedTo ? $ticket->assignedTo->name : '___________________' }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
