<?php

namespace App\Http\Controllers;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\MonthlySummary;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    private array $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function excelRekap(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', Setting::get('active_year', now()->year));

        $summaries = MonthlySummary::with('opd')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('skor_total')
            ->get();

        $filename = "Rekap_IKU_{$this->namaBulan[$bulan]}_{$tahun}.csv";

        return response()->stream(function () use ($summaries) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'OPD', 'Skor Utama', 'Skor Kerjasama', 'Skor Total', 'Status']);
            foreach ($summaries as $i => $s) {
                $status = $s->skor_total === null ? 'Belum Dihitung' : ($s->is_complete ? 'Lengkap' : 'Sebagian');
                fputcsv($out, [
                    $i + 1,
                    $s->opd->name ?? '-',
                    $s->skor_utama ?? '-',
                    $s->skor_kerjasama ?? '-',
                    $s->skor_total ?? '-',
                    $status,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function excelDetail(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', Setting::get('active_year', now()->year));

        $skorings = IkuSkoring::with(['indikator.opd', 'indikator.bidang'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $filename = "Detail_IKU_{$this->namaBulan[$bulan]}_{$tahun}.csv";

        return response()->stream(function () use ($skorings) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'OPD', 'Bidang', 'Indikator', 'Kategori', 'Bobot(%)', 'Skor AI', 'Skor TA', 'Skor Bupati', 'Status']);
            foreach ($skorings as $i => $s) {
                fputcsv($out, [
                    $i + 1,
                    $s->indikator?->opd?->name ?? '-',
                    $s->indikator?->bidang?->name ?? '-',
                    $s->indikator?->nama ?? '-',
                    ucfirst($s->indikator?->category ?? 'utama'),
                    $s->indikator?->bobot ?? '-',
                    $s->skor_ai ?? '-',
                    $s->skor_ta ?? '-',
                    $s->skor_bupati ?? '-',
                    $s->status,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function pdfOpd(Request $request, int $opdId): Response
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', Setting::get('active_year', now()->year));

        $indikators = Indikator::with([
            'bidang',
            'skorings' => fn ($q) => $q->where('bulan', $bulan)->where('tahun', $tahun),
        ])
            ->where('opd_id', $opdId)
            ->where('status', 'disetujui')
            ->orderBy('nama')
            ->get();

        $summary = MonthlySummary::with('opd')
            ->where('opd_id', $opdId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        $namaBulan = $this->namaBulan[$bulan];

        $html = view('exports.pdf-opd', compact('indikators', 'summary', 'namaBulan', 'tahun'))->render();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"IKU_{$opdId}_{$namaBulan}_{$tahun}.pdf\"",
            ]);
        }

        return response($html, 200, ['Content-Type' => 'text/html']);
    }
}
