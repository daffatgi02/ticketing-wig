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
            // Buat PDF menggunakan DomPDF dengan gambar
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('isRemoteEnabled', true); // Penting untuk load gambar
            $dompdf->setOptions($options);

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

            // Set default font
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(12);

            // Add section
            $section = $phpWord->addSection();

            // Header - Title
            $section->addText('BERITA ACARA', ['bold' => true, 'underline' => 'single'], ['alignment' => 'center']);
            $section->addTextBreak(1);

            // Subject
            $section->addText('Hal: Berita Acara Kerusakan ' . (
                $ticket->category->name == 'Hardware Issue' ? 'Hardware' : ($ticket->category->name == 'Network Issue' ? 'Jaringan' : ($ticket->category->name == 'Software Issue' ? 'Software' : 'Perangkat'))
            ));
            $section->addTextBreak(1);

            // Recipient
            $section->addText('Kepada Yth:');
            $section->addText($ticket->report_recipient . ' - ' . $ticket->report_recipient_position);
            $section->addText('Ditempat');
            $section->addTextBreak(1);

            $section->addText('Dengan hormat,');
            $section->addTextBreak(1);

            // Department
            $section->addText('Kami dari departemen ' . ($ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Staff IT') . ' melaporkan bahwa pada :');
            $section->addTextBreak(1);

            // Create a table for incident details
            $table = $section->addTable([
                'borderSize' => 0,
                'cellMargin' => 80,
            ]);

            // Date
            $table->addRow();
            $table->addCell(3000)->addText('Hari / Tanggal');
            $table->addCell(300)->addText(':');
            $table->addCell(6000)->addText(\Carbon\Carbon::parse($ticket->incident_date)->isoFormat('dddd, D MMMM YYYY'));

            // Time
            $table->addRow();
            $table->addCell(3000)->addText('Pukul');
            $table->addCell(300)->addText(':');
            $table->addCell(6000)->addText(\Carbon\Carbon::parse($ticket->incident_time)->format('H:i') . ' WIB');

            // Issue
            $table->addRow();
            $table->addCell(3000)->addText('Masalah');
            $table->addCell(300)->addText(':');
            $table->addCell(6000)->addText($ticket->issue_detail);

            // Actions
            $table->addRow();
            $table->addCell(3000)->addText('Tindakan');
            $table->addCell(300)->addText(':');
            $cell = $table->addCell(6000);

            // Split actions by new line and add them individually
            $actions = explode("\n", $ticket->actions_taken);
            foreach ($actions as $action) {
                $cell->addText(trim($action));
            }

            $section->addTextBreak(3);

            // Closing
            $section->addText('Demikian berita acara ini kami buat untuk menjadi periksa adanya.');
            $section->addTextBreak(2);

            // Signature section
            $section->addText(($ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Sleman') . ', ' . now()->isoFormat('D MMMM YYYY'));
            $section->addTextBreak(1);

            // Create a table for signatures
            $sigTable = $section->addTable([
                'borderSize' => 0,
                'cellMargin' => 80,
            ]);

            $sigTable->addRow();
            $sigTable->addCell(4000)->addText('Dibuat oleh,');
            $sigTable->addCell(4000)->addText('Diketahui oleh,');

            // Empty space for signatures
            $sigTable->addRow(1500); // Height for signature
            $sigTable->addCell(4000);
            $sigTable->addCell(4000);

            $sigTable->addRow();
            $sigTable->addCell(4000)->addText(($ticket->assignedTo ? $ticket->assignedTo->name : '_______________'));
            $sigTable->addCell(4000)->addText('________________');

            // Add a page break for attachments
            $section->addPageBreak();

            // Attachments page
            $section->addText('LAMPIRAN', ['bold' => true], ['alignment' => 'center']);
            $section->addText('Berita Acara Kejadian: ' . $ticket->ticket_id, null, ['alignment' => 'center']);
            $section->addTextBreak(1);

            $section->addText('Foto dan bukti pendukung:');
            $section->addTextBreak(1);

            // Add report images if any
            $reportImages = $ticket->attachments->where('use_in_report', true)->sortBy('report_order');

            if ($reportImages->count() > 0) {
                foreach ($reportImages as $image) {
                    if ($image->isImage()) {
                        // Get image path
                        $imagePath = storage_path('app/public/' . $image->filepath);

                        // Check if file exists
                        if (file_exists($imagePath)) {
                            // Add caption
                            $section->addText('Lampiran: ' . $image->filename, ['italic' => true]);

                            // Add image
                            $section->addImage($imagePath, [
                                'width' => 400,
                                'height' => 300,
                                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                            ]);

                            $section->addTextBreak(1);
                        }
                    }
                }
            } else {
                $section->addText('[Tidak ada foto yang dilampirkan]', ['italic' => true], ['alignment' => 'center']);
            }

            $section->addTextBreak(1);
            $section->addText('[Footer Logo Perusahaan]', ['italic' => true], ['alignment' => 'center']);

            $filename = 'BAK_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }


    public function generateCustomBAKDocument(Ticket $ticket, $htmlContent, $format = 'pdf')
    {
        $ticket->load(['user', 'category', 'assignedTo', 'attachments' => function ($query) {
            $query->where('use_in_report', true)->orderBy('report_order');
        }]);

        // Replace image URLs with actual paths for PDF
        if ($format == 'pdf') {
            // Find all image tags with src attribute containing storage/attachments
            preg_match_all('/<img.*?src="(.*?storage\/attachments\/.*?)".*?>/i', $htmlContent, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $imageUrl) {
                    // Convert URL to public path
                    $relativePath = str_replace(url('storage'), '', $imageUrl);
                    $publicPath = public_path('storage' . $relativePath);

                    // Replace with public path
                    $htmlContent = str_replace($imageUrl, $publicPath, $htmlContent);
                }
            }
        }

        if ($format == 'pdf') {
            // Buat PDF menggunakan DomPDF dengan konten custom
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('isRemoteEnabled', true); // Penting untuk load gambar
            $dompdf->setOptions($options);

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filename = 'BAK_' . $ticket->ticket_id . '_' . time() . '.pdf';
            $path = 'documents/' . $filename;

            Storage::put('public/' . $path, $output);

            return $path;
        } else {
            // Gunakan HTML-to-DOCX conversion
            $phpWord = new PhpWord();

            // Convert HTML to Word
            $section = $phpWord->addSection();
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

            $filename = 'BAK_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }

    public function generateCustomRKBDocument(Ticket $ticket, $htmlContent, $format = 'pdf')
    {
        // Similar to BAK method but for RKB
        $ticket->load(['user', 'category', 'assignedTo', 'attachments' => function ($query) {
            $query->where('use_in_report', true)->orderBy('report_order');
        }]);

        // Process the same way as BAK
        if ($format == 'pdf') {
            // Replace image URLs
            preg_match_all('/<img.*?src="(.*?storage\/attachments\/.*?)".*?>/i', $htmlContent, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $imageUrl) {
                    $relativePath = str_replace(url('storage'), '', $imageUrl);
                    $publicPath = public_path('storage' . $relativePath);
                    $htmlContent = str_replace($imageUrl, $publicPath, $htmlContent);
                }
            }

            // Generate PDF
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filename = 'RKB_' . $ticket->ticket_id . '_' . time() . '.pdf';
            $path = 'documents/' . $filename;

            Storage::put('public/' . $path, $output);

            return $path;
        } else {
            // Generate DOCX
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

            $filename = 'RKB_' . $ticket->ticket_id . '_' . time() . '.docx';
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
            // Buat PDF menggunakan DomPDF
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

            // Set default font
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(12);

            // Add section
            $section = $phpWord->addSection();

            // Header - Title
            $section->addText('RENCANA KERJA DAN BIAYA (RKB)', ['bold' => true, 'underline' => 'single'], ['alignment' => 'center']);
            $section->addText('Nomor: RKB/' . $ticket->ticket_id . '/' . date('Y'), null, ['alignment' => 'center']);
            $section->addTextBreak(1);

            // Info table
            $infoTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);

            // Ticket ID
            $infoTable->addRow();
            $infoTable->addCell(2000)->addText('ID Tiket');
            $infoTable->addCell(300)->addText(':');
            $infoTable->addCell(6000)->addText($ticket->ticket_id);

            // Date
            $infoTable->addRow();
            $infoTable->addCell(2000)->addText('Tanggal');
            $infoTable->addCell(300)->addText(':');
            $infoTable->addCell(6000)->addText(now()->isoFormat('D MMMM YYYY'));

            // Category
            $infoTable->addRow();
            $infoTable->addCell(2000)->addText('Kategori');
            $infoTable->addCell(300)->addText(':');
            $infoTable->addCell(6000)->addText($ticket->category->name);

            $section->addTextBreak(1);

            // Reporter Information
            $section->addText('1. INFORMASI PELAPOR', ['bold' => true]);

            $reporterTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);

            $reporterTable->addRow();
            $reporterTable->addCell(2000)->addText('Nama');
            $reporterTable->addCell(300)->addText(':');
            $reporterTable->addCell(6000)->addText($ticket->user->name);

            $reporterTable->addRow();
            $reporterTable->addCell(2000)->addText('Departemen');
            $reporterTable->addCell(300)->addText(':');
            $reporterTable->addCell(6000)->addText($ticket->user->department ? $ticket->user->department->name : '-');

            $reporterTable->addRow();
            $reporterTable->addCell(2000)->addText('Tanggal Laporan');
            $reporterTable->addCell(300)->addText(':');
            $reporterTable->addCell(6000)->addText($ticket->created_at->isoFormat('D MMMM YYYY, HH:mm'));

            $section->addTextBreak(1);

            // Support Staff Information
            $section->addText('2. INFORMASI PETUGAS', ['bold' => true]);

            $staffTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);

            $staffTable->addRow();
            $staffTable->addCell(2000)->addText('Nama');
            $staffTable->addCell(300)->addText(':');
            $staffTable->addCell(6000)->addText($ticket->assignedTo ? $ticket->assignedTo->name : 'Belum ditugaskan');

            $staffTable->addRow();
            $staffTable->addCell(2000)->addText('Departemen');
            $staffTable->addCell(300)->addText(':');
            $staffTable->addCell(6000)->addText($ticket->assignedTo && $ticket->assignedTo->department ? $ticket->assignedTo->department->name : '-');

            $section->addTextBreak(1);

            // Issue Description
            $section->addText('3. DESKRIPSI PERMASALAHAN', ['bold' => true]);
            $section->addText($ticket->issue_detail);
            $section->addTextBreak(1);

            // Actions Taken
            $section->addText('4. TINDAKAN YANG TELAH DILAKUKAN', ['bold' => true]);

            // Split actions by new line and add them individually
            $actions = explode("\n", $ticket->actions_taken);
            foreach ($actions as $action) {
                if (trim($action) != '') {
                    $section->addText(trim($action));
                }
            }
            $section->addTextBreak(1);

            // Recommended Solution
            $section->addText('5. REKOMENDASI SOLUSI', ['bold' => true]);
            $section->addText($ticket->external_support_reason);
            $section->addTextBreak(1);

            // Requirements Estimation
            $section->addText('6. ESTIMASI KEBUTUHAN', ['bold' => true]);

            // Requirements table
            $reqTableStyle = [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
            ];

            $reqTable = $section->addTable($reqTableStyle);

            // Header row
            $reqTable->addRow();
            $reqTable->addCell(500, ['bgColor' => 'f5f5f5'])->addText('No', ['bold' => true], ['alignment' => 'center']);
            $reqTable->addCell(4000, ['bgColor' => 'f5f5f5'])->addText('Deskripsi', ['bold' => true], ['alignment' => 'center']);
            $reqTable->addCell(1000, ['bgColor' => 'f5f5f5'])->addText('Jumlah', ['bold' => true], ['alignment' => 'center']);
            $reqTable->addCell(2000, ['bgColor' => 'f5f5f5'])->addText('Harga Satuan (Rp)', ['bold' => true], ['alignment' => 'center']);
            $reqTable->addCell(2000, ['bgColor' => 'f5f5f5'])->addText('Total (Rp)', ['bold' => true], ['alignment' => 'center']);

            // Empty rows for manual filling
            for ($i = 1; $i <= 3; $i++) {
                $reqTable->addRow();
                $reqTable->addCell(500)->addText($i, null, ['alignment' => 'center']);
                $reqTable->addCell(4000)->addText('');
                $reqTable->addCell(1000)->addText('', null, ['alignment' => 'center']);
                $reqTable->addCell(2000)->addText('', null, ['alignment' => 'right']);
                $reqTable->addCell(2000)->addText('', null, ['alignment' => 'right']);
            }

            // Total row
            $reqTable->addRow();
            $cell = $reqTable->addCell(7500, ['gridSpan' => 4]);
            $cell->addText('TOTAL', ['bold' => true], ['alignment' => 'right']);
            $reqTable->addCell(2000)->addText('', ['bold' => true], ['alignment' => 'right']);

            $section->addText('(Detail kebutuhan barang/jasa yang diperlukan akan diisi manual setelah dokumen diekspor)', ['italic' => true]);
            $section->addTextBreak(1);

            // Additional Notes
            if ($ticket->additional_notes) {
                $section->addText('7. CATATAN TAMBAHAN', ['bold' => true]);
                $section->addText($ticket->additional_notes);
                $section->addTextBreak(1);
            }

            // Approval
            $section->addText(($ticket->additional_notes ? '8' : '7') . '. PERSETUJUAN', ['bold' => true]);

            // Approval table
            $approvalTableStyle = [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
            ];

            $approvalTable = $section->addTable($approvalTableStyle);

            // Header row
            $approvalTable->addRow();
            $approvalTable->addCell(3000, ['bgColor' => 'f5f5f5'])->addText('Dibuat oleh', ['bold' => true], ['alignment' => 'center']);
            $approvalTable->addCell(3000, ['bgColor' => 'f5f5f5'])->addText('Mengetahui', ['bold' => true], ['alignment' => 'center']);
            $approvalTable->addCell(3000, ['bgColor' => 'f5f5f5'])->addText('Menyetujui', ['bold' => true], ['alignment' => 'center']);

            // Empty space for signatures
            $approvalTable->addRow(1500);
            $approvalTable->addCell(3000);
            $approvalTable->addCell(3000);
            $approvalTable->addCell(3000);

            // Names
            $approvalTable->addRow();
            $approvalTable->addCell(3000)->addText($ticket->assignedTo ? $ticket->assignedTo->name : '_______________', null, ['alignment' => 'center']);
            $approvalTable->addCell(3000)->addText('_______________', null, ['alignment' => 'center']);
            $approvalTable->addCell(3000)->addText('_______________', null, ['alignment' => 'center']);

            // Departments
            $approvalTable->addRow();
            $approvalTable->addCell(3000)->addText($ticket->assignedTo ? ($ticket->assignedTo->department ? $ticket->assignedTo->department->name : 'Staff IT') : 'Staff IT', null, ['alignment' => 'center']);
            $approvalTable->addCell(3000)->addText('Dept. ' . $ticket->report_recipient_position, null, ['alignment' => 'center']);
            $approvalTable->addCell(3000)->addText('Dept. Finance', null, ['alignment' => 'center']);

            $section->addTextBreak(2);

            // Footer
            $section->addText('[Footer Logo Perusahaan]', ['italic' => true], ['alignment' => 'center']);

            $filename = 'RKB_' . $ticket->ticket_id . '_' . time() . '.docx';
            $path = 'documents/' . $filename;

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/' . $path));

            return $path;
        }
    }
}
