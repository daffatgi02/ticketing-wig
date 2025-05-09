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
        .signature {
            width: 45%;
            float: left;
            margin-right: 10%;
        }
        .signature-right {
            width: 45%;
            float: left;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 50px;
            margin-bottom: 5px;
        }
        .attachments-section {
            margin-top: 30px;
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA</h1>
    </div>

    <div class="content">
        <div class="subject">
            <p>Hal: Berita Acara Kerusakan {{
                $ticket->category ? ($ticket->category->name == 'Hardware Issue' ? 'Hardware' :
                ($ticket->category->name == 'Network Issue' ? 'Jaringan' :
                ($ticket->category->name == 'Software Issue' ? 'Software' : 'Perangkat'))) : 'Perangkat'
            }}</p>

            <p>Kepada Yth:<br>
            {{ $ticket->report_recipient ?? 'Bpk Agus' }} - {{ $ticket->report_recipient_position ?? 'General Affair' }}<br>
            Ditempat</p>

            <p>Dengan hormat,</p>

            <p>Kami dari departemen {{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Staff IT' }} melaporkan bahwa pada :</p>

            <table class="info-table">
                <tr>
                    <td>Hari / Tanggal</td>
                    <td>:</td>
                    <td>{{ $ticket->incident_date ? $ticket->incident_date->isoFormat('dddd, D MMMM YYYY') : now()->isoFormat('dddd, D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Pukul</td>
                    <td>:</td>
                    <td>{{ $ticket->incident_time ? $ticket->incident_time->format('H:i') : now()->format('H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Masalah</td>
                    <td>:</td>
                    <td>{{ $ticket->issue_detail ?? $ticket->description }}</td>
                </tr>
                <tr>
                    <td>Tindakan</td>
                    <td>:</td>
                    <td>{{ $ticket->actions_taken ?? '- membuat berita acara dari IT ke dept GA (General Affair)' }}</td>
                </tr>
            </table>

            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <p>Demikian berita acara ini kami buat untuk menjadi periksa adanya.</p>
        </div>

        <div class="footer">
            <p>{{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Sleman' }}, {{ now()->isoFormat('D MMMM YYYY') }}</p>

            <div class="signature">
                <p>Dibuat oleh,</p>
                <div class="signature-line"></div>
                <p>{{ $ticket->assignedTo ? $ticket->assignedTo->name : Auth::user()->name }}</p>
            </div>

            <div class="signature-right">
                <p>Diketahui oleh,</p>
                <div class="signature-line"></div>
                <p>________________</p>
            </div>
        </div>
    </div>

    <!-- Attachments Section -->
    <div class="attachments-section">
        <h2 style="text-align: center;">LAMPIRAN</h2>
        <p style="text-align: center;">Berita Acara Kejadian: {{ $ticket->ticket_id }}</p>

        <p>Foto dan bukti pendukung:</p>

        <div style="text-align: center; margin-top: 30px; font-style: italic;">
            [Foto-foto bukti pendukung akan ditampilkan di sini]
        </div>
    </div>

    <div style="text-align: center; margin-top: 50px; font-style: italic; color: #666;">
        [Footer Logo Perusahaan]
    </div>
</body>
</html>
