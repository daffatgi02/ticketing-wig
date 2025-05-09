<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Kejadian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            font-size: 12pt;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 16pt;
            text-decoration: underline;
            font-weight: bold;
        }

        .content {
            text-align: justify;
        }

        .subject {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 80px;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature {
            width: 45%;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 50px;
            margin-bottom: 5px;
        }

        table.info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        table.info-table td:first-child {
            width: 200px;
            vertical-align: top;
            padding-bottom: 10px;
        }

        table.info-table td:nth-child(2) {
            width: 20px;
            vertical-align: top;
            padding-bottom: 10px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
        }

        @page {
            margin-bottom: 200px;
        }

        .page-break {
            page-break-after: always;
        }

        .attachments-header {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .attachments-content {
            margin-top: 20px;
        }

        .image-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .attachment-image {
            max-width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            display: block;
            page-break-inside: avoid;
        }

        .image-caption {
            font-style: italic;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>BERITA ACARA</h1>
    </div>

    <div class="content">
        <div class="subject">
            <p>Hal: Berita Acara Kerusakan
                {{ $ticket->category->name == 'Hardware Issue'
                    ? 'Hardware'
                    : ($ticket->category->name == 'Network Issue'
                        ? 'Jaringan'
                        : ($ticket->category->name == 'Software Issue'
                            ? 'Software'
                            : 'Perangkat')) }}
            </p>

            <p>Kepada Yth:<br>
                {{ $ticket->report_recipient }} - {{ $ticket->report_recipient_position }}<br>
                Ditempat</p>

            <p>Dengan hormat,</p>

            <p>Kami dari departemen
                {{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Staff IT' }}
                melaporkan bahwa pada :</p>

            <table class="info-table">
                <tr>
                    <td>Hari / Tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($ticket->incident_date)->isoFormat('dddd, D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Pukul</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($ticket->incident_time)->format('H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Masalah</td>
                    <td>:</td>
                    <td>{{ $ticket->issue_detail }}</td>
                </tr>
                <tr>
                    <td>Tindakan</td>
                    <td>:</td>
                    <td>{!! nl2br(e($ticket->actions_taken)) !!}</td>
                </tr>
            </table>

            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <p>Demikian berita acara ini kami buat untuk menjadi periksa adanya.</p>
        </div>

        <div class="footer">
            <p>{{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Sleman' }},
                {{ now()->isoFormat('D MMMM YYYY') }}</p>

            <div class="signature-container">
                <div class="signature">
                    <p>Dibuat oleh,</p>
                    <div class="signature-line"></div>
                    <p>{{ $ticket->assignedTo ? $ticket->assignedTo->name : '_______________' }}</p>
                </div>

                <div class="signature">
                    <p>Diketahui oleh,</p>
                    <div class="signature-line"></div>
                    <p>________________</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Page break for attachments -->
    <div class="page-break"></div>

    <div class="attachments-header">
        <h2>LAMPIRAN</h2>
        <p>Berita Acara Kejadian: {{ $ticket->ticket_id }}</p>
    </div>

    <div class="attachments-content">
        <p>Foto dan bukti pendukung:</p>

        @php
            $reportImages = $ticket->attachments->where('use_in_report', true)->sortBy('report_order');
        @endphp

        @if ($reportImages->count() > 0)
            @foreach ($reportImages as $image)
                @if ($image->isImage())
                    <div class="image-container">
                        <img src="{{ public_path('storage/' . $image->filepath) }}" class="attachment-image"
                            alt="{{ $image->filename }}">
                        <div class="image-caption">Lampiran: {{ $image->filename }}</div>
                    </div>
                @endif
            @endforeach
        @else
            <div style="margin-top: 20px; font-style: italic; color: #666; text-align: center;">
                [Tidak ada foto yang dilampirkan]
            </div>
        @endif
    </div>

    <div class="logo">
        [Footer Logo Perusahaan]
    </div>
</body>

</html>
