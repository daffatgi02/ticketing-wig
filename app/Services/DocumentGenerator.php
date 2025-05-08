<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPdf\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Str;
use Dompdf\Dompdf;

class DocumentGenerator
{
    /**
     * Generate resolution document for a ticket
     *
     * @param Ticket $ticket
     * @param string $format 'pdf' or 'docx'
     * @return string Document path
     */
    public function generateResolutionDocument(Ticket $ticket, $format = 'pdf')
    {
        $ticket->load(['user', 'category', 'assignedTo', 'assignedBy', 'comments.user', 'attachments']);

        if ($format == 'pdf') {
            // Gunakan DomPDF langsung, tanpa facade
            $dompdf = new Dompdf();
            $html = view('documents.resolution', compact('ticket'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filename = 'resolution_' . $ticket->ticket_id . '_' . time() . '.pdf';
            $path = 'documents/' . $filename;

            Storage::put('public/' . $path, $output);

            return $path;
        } else {
            // Word generation code tetap sama
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Add content to Word document
            $section->addTitle('Ticket Resolution Report', 1);
            $section->addText('Ticket ID: ' . $ticket->ticket_id);
            $section->addText('Title: ' . $ticket->title);
            $section->addText('Category: ' . $ticket->category->name);
            $section->addText('Reported by: ' . $ticket->user->name);
            $section->addText('Assigned to: ' . ($ticket->assignedTo ? $ticket->assignedTo->name : 'Not assigned'));
            $section->addText('Status: ' . ucfirst($ticket->status));

            $section->addTextBreak();
            $section->addTitle('Description', 2);
            $section->addText($ticket->description);

            $section->addTextBreak();
            $section->addTitle('Resolution', 2);
            $resolutionComment = $ticket->comments->where('user_id', $ticket->assigned_to)->last();
            if ($resolutionComment) {
                $section->addText($resolutionComment->comment);
            } else {
                $section->addText('No resolution comment provided.');
            }

            $section->addTextBreak();
            $section->addTitle('Comments History', 2);
            foreach ($ticket->comments as $comment) {
                $section->addText($comment->user->name . ' (' . $comment->created_at->format('M d, Y H:i') . '):');
                $section->addText($comment->comment);
                $section->addTextBreak();
            }

            $filename = 'resolution_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }


    /**
     * Generate BAK (Berita Acara Kejadian) document
     *
     * @param Ticket $ticket
     * @param string $format 'pdf' or 'docx'
     * @return string Document path
     */
    public function generateBAKDocument(Ticket $ticket, $format = 'pdf')
    {
        $ticket->load(['user', 'category', 'assignedTo', 'comments.user', 'attachments']);

        if ($format == 'pdf') {
            // Gunakan DomPDF langsung
            $dompdf = new Dompdf();
            $html = view('documents.bak', compact('ticket'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filename = 'BAK_' . $ticket->ticket_id . '_' . time() . '.pdf';
            $path = 'documents/' . $filename;

            Storage::put('public/' . $path, $output);

            return $path;
        } else {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Add content to Word document
            $section->addTitle('BERITA ACARA KEJADIAN (BAK)', 1);
            $section->addTitle('Nomor: BAK/' . $ticket->ticket_id . '/' . date('Y'), 2);
            $section->addTextBreak();

            $section->addText('Pada hari ini ' . now()->format('l') . ' tanggal ' . now()->format('d F Y') . ', yang bertanda tangan di bawah ini:');
            $section->addTextBreak();

            // Informasi petugas
            $section->addText('Nama: ' . ($ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan'));
            $section->addText('Jabatan: ' . ($ticket->assignedTo ? $ticket->assignedTo->position : '-'));
            $section->addText('Departemen: ' . ($ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-'));
            $section->addTextBreak();

            $section->addText('Telah melakukan pengecekan atas laporan kerusakan/gangguan dengan detail sebagai berikut:');
            $section->addTextBreak();

            $section->addText('ID Tiket: ' . $ticket->ticket_id);
            $section->addText('Kategori: ' . $ticket->category->name);
            $section->addText('Dilaporkan oleh: ' . $ticket->user->name);
            $section->addText('Departemen: ' . ($ticket->user->department ? $ticket->user->department->name : '-'));
            $section->addText('Tanggal Laporan: ' . $ticket->created_at->format('d F Y H:i'));
            $section->addTextBreak();

            $section->addTitle('DESKRIPSI KEJADIAN/PERMASALAHAN', 3);
            $section->addText($ticket->description);
            $section->addTextBreak();

            $section->addTitle('TINDAKAN YANG SUDAH DILAKUKAN', 3);

            // Get comments from support staff
            $supportComments = $ticket->comments->where('user_id', $ticket->assigned_to);

            if ($supportComments->count() > 0) {
                foreach ($supportComments as $comment) {
                    $section->addText('- ' . $comment->comment);
                }
            } else {
                $section->addText('Belum ada tindakan yang dilakukan.');
            }

            $section->addTextBreak();

            $section->addTitle('KESIMPULAN', 3);
            $section->addText('Berdasarkan pengecekan yang dilakukan, permasalahan ini memerlukan dukungan dari pihak eksternal dengan alasan sebagai berikut:');
            $section->addText($ticket->external_support_reason ?: 'Belum ada alasan yang dicatat.');

            $section->addTextBreak(2);

            // Signature section
            $section->addText('Yang membuat berita acara,');
            $section->addTextBreak(3);
            $section->addText(($ticket->assignedTo ? $ticket->assignedTo->name : '___________________'));

            $filename = 'BAK_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }

    /**
     * Generate RKB (Rencana Kerja dan Biaya) document
     *
     * @param Ticket $ticket
     * @param string $format 'pdf' or 'docx'
     * @return string Document path
     */
    public function generateRKBDocument(Ticket $ticket, $format = 'pdf')
    {
        $ticket->load(['user', 'category', 'assignedTo', 'comments.user', 'attachments']);

        if ($format == 'pdf') {
            // Gunakan DomPDF langsung
            $dompdf = new Dompdf();
            $html = view('documents.rkb', compact('ticket'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filename = 'RKB_' . $ticket->ticket_id . '_' . time() . '.pdf';
            $path = 'documents/' . $filename;

            Storage::put('public/' . $path, $output);

            return $path;
        } else {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Add content to Word document
            $section->addTitle('RENCANA KERJA DAN BIAYA (RKB)', 1);
            $section->addTitle('Nomor: RKB/' . $ticket->ticket_id . '/' . date('Y'), 2);
            $section->addTextBreak();

            $section->addText('ID Tiket: ' . $ticket->ticket_id);
            $section->addText('Kategori Permasalahan: ' . $ticket->category->name);
            $section->addText('Tanggal Pengajuan: ' . now()->format('d F Y'));
            $section->addTextBreak();

            // Informasi Pelapor & Petugas
            $section->addTitle('INFORMASI PELAPOR', 3);
            $section->addText('Nama: ' . $ticket->user->name);
            $section->addText('Departemen: ' . ($ticket->user->department ? $ticket->user->department->name : '-'));
            $section->addText('Tanggal Laporan: ' . $ticket->created_at->format('d F Y H:i'));

            $section->addTextBreak();

            $section->addTitle('INFORMASI PETUGAS', 3);
            $section->addText('Nama: ' . ($ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan'));
            $section->addText('Departemen: ' . ($ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-'));

            $section->addTextBreak();

            $section->addTitle('DESKRIPSI PERMASALAHAN', 3);
            $section->addText($ticket->description);

            $section->addTextBreak();

            $section->addTitle('REKOMENDASI SOLUSI', 3);
            // Get the latest support comment
            $lastSupportComment = $ticket->comments->where('user_id', $ticket->assigned_to)->last();
            if ($lastSupportComment) {
                $section->addText($lastSupportComment->comment);
            } else {
                $section->addText('Belum ada rekomendasi yang diberikan.');
            }

            $section->addTextBreak();

            $section->addTitle('ESTIMASI KEBUTUHAN', 3);
            $section->addText('(Detail kebutuhan barang/jasa yang diperlukan akan diisi manual setelah dokumen diekspor)');

            $section->addTextBreak();

            $section->addTitle('PERSETUJUAN', 3);
            $tableStyle = array(
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
                'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            );

            $table = $section->addTable($tableStyle);

            // Header row
            $table->addRow();
            $table->addCell(3000)->addText('Dibuat oleh', array('bold' => true));
            $table->addCell(3000)->addText('Mengetahui', array('bold' => true));
            $table->addCell(3000)->addText('Menyetujui', array('bold' => true));

            // Content row
            $table->addRow(1000);
            $table->addCell(3000)->addText('');
            $table->addCell(3000)->addText('');
            $table->addCell(3000)->addText('');

            // Footer row
            $table->addRow();
            $table->addCell(3000)->addText($ticket->assignedTo ? $ticket->assignedTo->name : '_________________');
            $table->addCell(3000)->addText('_________________');
            $table->addCell(3000)->addText('_________________');

            $filename = 'RKB_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }
}
