<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 20px; }
h1 { font-size: 15px; text-align: center; margin-bottom: 2px; }
h2 { font-size: 12px; text-align: center; color: #4b5563; margin-top: 0; margin-bottom: 16px; }
.summary { background: #eff6ff; border: 1px solid #bfdbfe; padding: 8px 12px; border-radius: 4px; margin-bottom: 14px; }
.summary strong { color: #1e40af; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
thead th { background: #1e40af; color: white; padding: 6px 8px; text-align: left; }
tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
tbody tr:nth-child(even) td { background: #f9fafb; }
.skor-green { color: #15803d; font-weight: bold; }
.skor-yellow { color: #b45309; font-weight: bold; }
.skor-red { color: #b91c1c; font-weight: bold; }
.badge-kerjasama { background: #ddd6fe; color: #5b21b6; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.badge-utama { background: #d1fae5; color: #065f46; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 6px; }
</style>
</head>
<body>
<h1>LAPORAN IKU {{ strtoupper($namaBulan) }} {{ $tahun }}</h1>
<h2>{{ $summary?->opd?->name ?? 'OPD' }}</h2>

@if($summary)
<div class="summary">
    <strong>Rekap Skor:</strong>
    Skor Utama: <strong>{{ $summary->skor_utama ?? '-' }}</strong> |
    Skor Kerjasama: <strong>{{ $summary->skor_kerjasama ?? '-' }}</strong> |
    Skor Total: <strong>{{ $summary->skor_total ?? 'Belum dihitung' }}</strong>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    Status: {{ $summary->is_complete ? 'Lengkap' : 'Sebagian' }}
</div>
@endif

<table>
<thead>
<tr>
    <th style="width:4%">#</th>
    <th style="width:30%">Nama Indikator</th>
    <th style="width:10%">Kategori</th>
    <th style="width:16%">Bidang</th>
    <th style="width:7%">Bobot</th>
    <th style="width:8%">Skor AI</th>
    <th style="width:8%">Skor TA</th>
    <th style="width:8%">Skor Bupati</th>
    <th style="width:9%">Status</th>
</tr>
</thead>
<tbody>
@forelse($indikators as $i => $ind)
@php
    $sk = $ind->skorings->first();
    $skor = $sk?->skor_bupati;
    $cls = $skor === null ? '' : ($skor >= 8 ? 'skor-green' : ($skor >= 6 ? 'skor-yellow' : 'skor-red'));
@endphp
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $ind->nama }}</td>
    <td>
        @if($ind->category === 'kerjasama')
            <span class="badge-kerjasama">Kerjasama</span>
        @else
            <span class="badge-utama">Utama</span>
        @endif
    </td>
    <td>{{ $ind->bidang?->name ?? '-' }}</td>
    <td>{{ $ind->bobot }}%</td>
    <td>{{ $sk?->skor_ai ?? '-' }}</td>
    <td>{{ $sk?->skor_ta ?? '-' }}</td>
    <td class="{{ $cls }}">{{ $skor ?? '-' }}</td>
    <td>{{ $sk ? ucfirst($sk->status) : 'Pending' }}</td>
</tr>
@empty
<tr><td colspan="9" style="text-align:center; color:#9ca3af;">Tidak ada indikator</td></tr>
@endforelse
</tbody>
</table>

<div class="footer">
    Dicetak: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Sistem IKU Pringsewu
</div>
</body>
</html>
