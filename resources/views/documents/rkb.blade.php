<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Kerja dan Biaya</title>
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
        .footer {
            margin-top: 80px;
        }
        .subject {
            margin-bottom: 20px;
        }
        .signature-container {
            width: 100%;
            margin-top: 50px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table th, .signature-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        .signature-table th {
            background-color: #f5f5f5;
        }
        .signature-space {
            height: 60px;
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
        .requirements-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .requirements-table th, .requirements-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .requirements-table th {
            background-color: #f5f5f5;
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RENCANA KERJA DAN BIAYA (RKB)</h1>
        <p>Nomor: RKB/{{ $ticket->ticket_id }}/{{ date('Y') }}</p>
    </div>

    <div class="content">
        <div class="subject">
            <table class="info-table">
                <tr>
                    <td>ID Tiket</td>
                    <td>:</td>
                    <td>{{ $ticket->ticket_id }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ now()->isoFormat('D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Kategori</td>
                    <td>:</td>
                    <td>{{ $ticket->category->name }}</td>
                </tr>
            </table>

            <h3>1. INFORMASI PELAPOR</h3>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $ticket->user->name }}</td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>:</td>
                    <td>{{ $ticket->user->department ? $ticket->user->department->name : '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Laporan</td>
                    <td>:</td>
                    <td>{{ $ticket->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                </tr>
            </table>

            <h3>2. INFORMASI PETUGAS</h3>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan' }}</td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>:</td>
                    <td>{{ $ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-' }}</td>
                </tr>
            </table>

            <h3>3. DESKRIPSI PERMASALAHAN</h3>
            <p>{{ $ticket->issue_detail }}</p>

            <h3>4. TINDAKAN YANG TELAH DILAKUKAN</h3>
            <p>{!! nl2br(e($ticket->actions_taken)) !!}</p>

            <h3>5. REKOMENDASI SOLUSI</h3>
            <p>{{ $ticket->external_support_reason }}</p>

            <h3>6. ESTIMASI KEBUTUHAN</h3>
            <table class="requirements-table">
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi</th>
                    <th width="10%">Jumlah</th>
                    <th width="20%">Harga Satuan (Rp)</th>
                    <th width="20%">Total (Rp)</th>
                </tr>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>TOTAL</strong></td>
                    <td style="text-align: right;"><strong></strong></td>
                </tr>
            </table>
            <p><i>(Detail kebutuhan barang/jasa yang diperlukan akan diisi manual setelah dokumen diekspor)</i></p>

            @if($ticket->additional_notes)
                <h3>7. CATATAN TAMBAHAN</h3>
                <p>{{ $ticket->additional_notes }}</p>
            @endif

            <h3>{{ $ticket->additional_notes ? '8' : '7' }}. PERSETUJUAN</h3>
            <table class="signature-table">
                <tr>
                    <th width="33%">Dibuat oleh</th>
                    <th width="33%">Mengetahui</th>
                    <th width="33%">Menyetujui</th>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : '_______________' }}</td>
                    <td>_______________</td>
                    <td>_______________</td>
                </tr>
                <tr>
                    <td>{{ $ticket->assignedTo ? ($ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Staff IT') : 'Staff IT' }}</td>
                    <td>Dept. {{ $ticket->report_recipient_position }}</td>
                    <td>Dept. Finance</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="logo">
        [Footer Logo Perusahaan]
    </div>
</body>
</html>
