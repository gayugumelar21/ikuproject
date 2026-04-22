<?php

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Services\AiSkoringService;
use App\Services\SkoringService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Flux\Flux;

new #[Title('Skoring Tenaga Ahli')] class extends Component {
    public int $bulan;
    public int $tahun;

    public ?int $selectedSkoringId = null;
    public ?int $filterOpdId = null;
    public string $aiModel = 'auto';
    public int $skorTa = 5;
    public string $taNotes = '';

    private SkoringService $service;
    private AiSkoringService $aiService;

    public function mount(): void
    {
        $this->bulan = (int) now()->format('n');
        $this->tahun = (int) now()->format('Y');
    }

    public function boot(SkoringService $service, AiSkoringService $aiService): void
    {
        $this->service = $service;
        $this->aiService = $aiService;
    }

    #[Computed]
    public function belumSkoring()
    {
        return $this->service->getIndikatorBelumSkoring($this->bulan, $this->tahun, $this->filterOpdId);
    }

    #[Computed]
    public function skorings()
    {
        return $this->service->getPendingUntukTa($this->bulan, $this->tahun, $this->filterOpdId);
    }

    #[Computed]
    public function sudahFinal()
    {
        return $this->service->getSudahFinal($this->bulan, $this->tahun, $this->filterOpdId);
    }

    #[Computed]
    public function filterOpds(): \Illuminate\Support\Collection
    {
        return \App\Models\Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])
            ->orderBy('name')
            ->get();
    }

    public function openSkoring(int $id): void
    {
        $this->selectedSkoringId = $id;
        $skoring = IkuSkoring::find($id);
        $this->skorTa = $skoring->skor_ta ?? ($skoring->skor_ai ?? 5);
        $this->taNotes = $skoring->ta_notes ?? '';
        Flux::modal('modal-skoring-ta')->show();
    }

    public function generateAi(int $indikatorId): void
    {
        $this->authorize('skoring-ai');
        $indikator = Indikator::findOrFail($indikatorId);
        $result = $this->aiService->generate($indikator, $this->bulan, $this->tahun, $this->aiModel);

        if ($result) {
            Flux::toast('Skor AI berhasil digenerate: ' . $result->skor_ai . '/10');
        } else {
            Flux::toast('Gagal generate skor AI. Pastikan ANTHROPIC_API_KEY di .env sudah diisi dan ada data realisasi bulan ini.', variant: 'danger');
        }

        unset($this->belumSkoring, $this->skorings);
    }

    public function generateAllAi(): void
    {
        $this->authorize('skoring-ai');
        $generated = 0;

        foreach ($this->belumSkoring as $indikator) {
            $result = $this->aiService->generate($indikator, $this->bulan, $this->tahun, $this->aiModel);
            if ($result) {
                $generated++;
            }
        }

        Flux::toast("Berhasil generate AI untuk {$generated} indikator.");
        unset($this->belumSkoring, $this->skorings);
    }

    public function simpan(): void
    {
        $this->authorize('skoring-ta');
        $this->validate([
            'skorTa' => ['required', 'integer', 'min:1', 'max:10'],
            'taNotes' => ['nullable', 'string', 'max:1000'],
        ]);

        $skoring = IkuSkoring::findOrFail($this->selectedSkoringId);
        $this->service->simpanSkorTa($skoring, $this->skorTa, $this->taNotes ?? '', auth()->user());
        Flux::toast('Pertimbangan TA berhasil disimpan.');
        Flux::modal('modal-skoring-ta')->close();
        unset($this->skorings);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Skoring Tenaga Ahli</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Berikan pertimbangan skor (1–10) sebelum dinilai Bupati.</flux:text>
        </div>

        {{-- Filter Bulan & Tahun --}}
        <div class="flex flex-wrap gap-3">
            <flux:field>
                <flux:label>Model AI</flux:label>
                <flux:select wire:model="aiModel" class="w-52">
                    <flux:select.option value="auto">🤖 Auto (Claude + Fallback)</flux:select.option>
                    <flux:select.option value="claude">🎭 Claude 3.5 Sonnet</flux:select.option>
                    <flux:select.option value="gemini">♊ Gemini 1.5 Flash</flux:select.option>
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Unit / OPD</flux:label>
                <flux:select wire:model.live="filterOpdId" class="w-64">
                    <flux:select.option value="">-- Semua Unit --</flux:select.option>
                    @foreach ($this->filterOpds as $opd)
                        <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                            {{ $opd->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Bulan</flux:label>
                <flux:select wire:model.live="bulan" class="w-36">
                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $idx => $nama)
                        <flux:select.option value="{{ $idx + 1 }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Tahun</flux:label>
                <flux:input type="number" wire:model.live="tahun" class="w-24" min="2020" max="2030" />
            </flux:field>
        </div>
    </div>

    {{-- Info Callout --}}
    <flux:callout variant="info" icon="information-circle">
        <flux:callout.heading>Skor TA Bersifat Pertimbangan</flux:callout.heading>
        <flux:callout.text>
            Skor Tenaga Ahli hanya sebagai bahan pertimbangan. Keputusan skoring mutlak berada di tangan Bupati.
        </flux:callout.text>
    </flux:callout>

    {{-- Seksi: Siap Generate AI --}}
    @if ($this->belumSkoring->isNotEmpty())
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">Siap Generate AI</flux:heading>
                    <flux:text class="text-zinc-500 text-sm">
                        {{ $this->belumSkoring->count() }} indikator sudah ada realisasi, belum masuk proses skoring.
                    </flux:text>
                </div>
                <flux:button size="sm" variant="primary" icon="cpu-chip" wire:click="generateAllAi"
                    wire:loading.attr="disabled"
                    wire:confirm="Generate AI untuk semua {{ $this->belumSkoring->count() }} indikator sekaligus?">
                    <span wire:loading.remove wire:target="generateAllAi">Generate Semua</span>
                    <span wire:loading wire:target="generateAllAi">Memproses...</span>
                </flux:button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-amber-200 dark:border-amber-700">
                <table class="w-full text-sm">
                    <thead class="bg-amber-50 dark:bg-amber-900/30 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">OPD / Bidang</th>
                            <th class="px-4 py-3 text-left font-medium">Indikator</th>
                            <th class="px-4 py-3 text-left font-medium">Realisasi Bulan Ini</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100 dark:divide-amber-800">
                        @foreach ($this->belumSkoring as $indikator)
                            @php $realisasi = $indikator->realisasi->first(); @endphp
                            <tr wire:key="belum-{{ $indikator->id }}"
                                class="bg-white dark:bg-zinc-900 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 text-xs">
                                        {{ $indikator->opd?->name ?? '-' }}
                                    </div>
                                    <div class="text-zinc-500 text-xs">{{ $indikator->bidang?->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 max-w-xs">
                                        {{ $indikator->nama }}
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-0.5">Bobot: {{ $indikator->bobot }}%</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 max-w-xs">
                                    @if ($realisasi)
                                        @if ($indikator->measurement_type !== 'kualitatif')
                                            <span class="font-medium">{{ $realisasi->nilai }}
                                                {{ $indikator->satuan }}</span>
                                        @endif
                                        @if ($realisasi->keterangan)
                                            <p class="text-xs text-zinc-500 mt-0.5 line-clamp-2">
                                                {{ $realisasi->keterangan }}</p>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 italic text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button size="sm" variant="filled" icon="cpu-chip"
                                        wire:click="generateAi({{ $indikator->id }})" wire:loading.attr="disabled"
                                        wire:target="generateAi({{ $indikator->id }})">
                                        <span wire:loading.remove
                                            wire:target="generateAi({{ $indikator->id }})">Generate AI</span>
                                        <span wire:loading wire:target="generateAi({{ $indikator->id }})">...</span>
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Seksi: Menunggu Pertimbangan TA --}}
    <div class="space-y-3">
        @if ($this->belumSkoring->isNotEmpty() && $this->skorings->isNotEmpty())
            <flux:heading size="lg">Menunggu Pertimbangan TA</flux:heading>
        @endif

        @if ($this->skorings->isNotEmpty())
            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">OPD / Bidang</th>
                            <th class="px-4 py-3 text-left font-medium">Indikator</th>
                            <th class="px-4 py-3 text-center font-medium">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium">Realisasi</th>
                            <th class="px-4 py-3 text-center font-medium">Skor AI</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->skorings as $skoring)
                            <tr wire:key="skoring-{{ $skoring->id }}"
                                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 text-xs">
                                        {{ $skoring->indikator->opd?->name ?? '-' }}
                                    </div>
                                    <div class="text-zinc-500 text-xs">
                                        {{ $skoring->indikator->bidang?->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 max-w-xs">
                                        {{ $skoring->indikator->nama }}
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-0.5">Bobot: {{ $skoring->indikator->bobot }}%
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($skoring->indikator->measurement_type === 'kualitatif')
                                        <flux:badge variant="blue" size="sm">Kualitatif</flux:badge>
                                    @else
                                        <flux:badge variant="green" size="sm">Kuantitatif</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 max-w-xs">
                                    @if ($skoring->realisasi)
                                        @if ($skoring->indikator->measurement_type !== 'kualitatif')
                                            <span class="font-medium">{{ $skoring->realisasi->nilai }}
                                                {{ $skoring->indikator->satuan }}</span>
                                        @endif
                                        @if ($skoring->realisasi->keterangan)
                                            <p class="text-xs text-zinc-500 mt-0.5 line-clamp-2">
                                                {{ $skoring->realisasi->keterangan }}</p>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 italic text-xs">Belum ada realisasi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($skoring->skor_ai)
                                        @php
                                            $aiColor =
                                                $skoring->skor_ai >= 7
                                                    ? 'green'
                                                    : ($skoring->skor_ai >= 5
                                                        ? 'yellow'
                                                        : 'red');
                                        @endphp
                                        <flux:badge variant="{{ $aiColor }}" size="sm">
                                            {{ $skoring->skor_ai }}/10</flux:badge>
                                        @if ($skoring->ai_reasoning)
                                            <p class="text-xs text-zinc-500 mt-1 line-clamp-2 max-w-32">
                                                {{ $skoring->ai_reasoning }}</p>
                                        @endif
                                    @else
                                        <flux:button size="xs" variant="ghost" icon="cpu-chip"
                                            wire:click="generateAi({{ $skoring->indikator_id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="generateAi({{ $skoring->indikator_id }})">
                                            <span wire:loading.remove
                                                wire:target="generateAi({{ $skoring->indikator_id }})">Generate
                                                AI</span>
                                            <span wire:loading
                                                wire:target="generateAi({{ $skoring->indikator_id }})">...</span>
                                        </flux:button>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <flux:button size="sm" variant="primary" icon="star"
                                        wire:click="openSkoring({{ $skoring->id }})">
                                        Pertimbangan
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif ($this->belumSkoring->isEmpty())
            <div
                class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-16 text-center">
                <flux:icon name="check-badge" class="h-12 w-12 text-zinc-300 dark:text-zinc-600 mb-3" />
                <flux:heading size="sm" class="text-zinc-500">Tidak ada indikator yang menunggu skoring TA
                </flux:heading>
                <flux:text class="text-zinc-400 mt-1 text-sm">
                    Semua indikator sudah diberi pertimbangan atau belum ada realisasi bulan ini.
                </flux:text>
            </div>
        @endif
    </div>

    {{-- Modal Skoring TA --}}
    <flux:modal name="modal-skoring-ta" class="md:w-[520px]">
        @if ($selectedSkoringId)
            @php $skoring = IkuSkoring::find($selectedSkoringId); @endphp
            @if ($skoring)
                <div class="space-y-5">
                    <div>
                        <flux:heading>Pertimbangan Tenaga Ahli</flux:heading>
                        <flux:text class="mt-1 text-zinc-500 text-sm">{{ $skoring->indikator->nama }}</flux:text>
                    </div>

                    @if ($skoring->skor_ai)
                        <div
                            class="rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <flux:icon name="cpu-chip" class="h-4 w-4 text-blue-600" />
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Skor AI:
                                    {{ $skoring->skor_ai }}/10</span>
                            </div>
                            @if ($skoring->ai_reasoning)
                                <p class="text-sm text-blue-600 dark:text-blue-400 italic">
                                    {{ $skoring->ai_reasoning }}</p>
                            @endif
                        </div>
                    @endif

                    <div>
                        <flux:label class="mb-2 block font-medium">Skor Pertimbangan TA (1–10)</flux:label>
                        <div class="flex gap-2 flex-wrap">
                            @for ($s = 1; $s <= 10; $s++)
                                <button wire:click="$set('skorTa', {{ $s }})"
                                    class="w-10 h-10 rounded-lg font-bold text-sm transition-colors
                                        {{ $skorTa === $s
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    {{ $s }}
                                </button>
                            @endfor
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">
                            1–3: Sangat Kurang &nbsp;|&nbsp; 4–6: Cukup &nbsp;|&nbsp; 7–8: Baik &nbsp;|&nbsp; 9–10:
                            Sangat Baik
                        </p>
                    </div>

                    <flux:field>
                        <flux:label>Catatan Pertimbangan</flux:label>
                        <flux:textarea wire:model="taNotes" rows="3"
                            placeholder="Tuliskan catatan atau pertimbangan Anda..." />
                        <flux:error name="taNotes" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-2">
                        <flux:button variant="ghost" x-on:click="$flux.modal('modal-skoring-ta').close()">Batal
                        </flux:button>
                        <flux:button variant="primary" wire:click="simpan" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="simpan">Simpan Pertimbangan</span>
                            <span wire:loading wire:target="simpan">Menyimpan...</span>
                        </flux:button>
                    </div>
                </div>
            @endif
        @endif
    </flux:modal>
</div>
