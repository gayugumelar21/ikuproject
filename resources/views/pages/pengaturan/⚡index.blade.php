<?php

use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Flux\Flux;

new #[Title('Pengaturan Sistem')] class extends Component
{
    public array $values = [];

    public function mount(): void
    {
        foreach (Setting::all() as $setting) {
            $this->values[$setting->key] = Setting::get($setting->key);
        }
    }

    #[Computed]
    public function settings(): \Illuminate\Support\Collection
    {
        return Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
    }

    public function simpan(): void
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            if (! array_key_exists($setting->key, $this->values)) {
                continue;
            }

            // Skip encrypted fields if left empty (keep existing value)
            if ($setting->type === 'encrypted' && $this->values[$setting->key] === '') {
                continue;
            }

            Setting::set($setting->key, $this->values[$setting->key]);
        }

        Flux::toast('Pengaturan berhasil disimpan.');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Pengaturan Sistem</flux:heading>
            <flux:text class="text-zinc-500 mt-1">Konfigurasi AI, WhatsApp, dan pengaturan umum aplikasi</flux:text>
        </div>
        <flux:button variant="primary" icon="check" wire:click="simpan" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="simpan">Simpan Pengaturan</span>
            <span wire:loading wire:target="simpan">Menyimpan...</span>
        </flux:button>
    </div>

    <div class="space-y-8">
        @foreach($this->settings as $group => $groupSettings)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="bg-zinc-50 dark:bg-zinc-800 px-5 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="sm" class="capitalize">
                        @if($group === 'ai') Konfigurasi AI (Anthropic Claude)
                        @elseif($group === 'whatsapp') Konfigurasi WhatsApp (Fonnte)
                        @else Pengaturan Umum
                        @endif
                    </flux:heading>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($groupSettings as $setting)
                        <div class="flex items-start justify-between gap-6 px-5 py-4">
                            <div class="flex-1">
                                <div class="font-medium text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $setting->label ?? $setting->key }}
                                </div>
                                @if($setting->description)
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $setting->description }}</div>
                                @endif
                                <div class="text-xs text-zinc-400 font-mono mt-0.5">{{ $setting->key }}</div>
                            </div>

                            <div class="w-72 flex-shrink-0">
                                @if($setting->type === 'boolean')
                                    <flux:checkbox
                                        wire:model="values.{{ $setting->key }}"
                                        label="{{ isset($this->values[$setting->key]) && $this->values[$setting->key] ? 'Aktif' : 'Nonaktif' }}"
                                    />
                                @elseif($setting->type === 'integer')
                                    <flux:input
                                        type="number"
                                        wire:model="values.{{ $setting->key }}"
                                    />
                                @elseif($setting->type === 'encrypted')
                                    <flux:input
                                        type="password"
                                        wire:model="values.{{ $setting->key }}"
                                        placeholder="Isi untuk mengubah, kosongkan untuk tidak mengubah"
                                    />
                                @else
                                    <flux:input wire:model="values.{{ $setting->key }}" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-end">
        <flux:button variant="primary" icon="check" wire:click="simpan" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="simpan">Simpan Pengaturan</span>
            <span wire:loading wire:target="simpan">Menyimpan...</span>
        </flux:button>
    </div>
</div>
