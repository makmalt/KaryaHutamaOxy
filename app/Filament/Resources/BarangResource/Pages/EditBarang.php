<?php

namespace App\Filament\Resources\BarangResource\Pages;

use Filament\Actions;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BarangResource;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;

    #[On('refresh')]
    public function refresh()
    {
        $this->fillForm();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function deleteRecord(): void
    {
        $this->record->delete(); // Hapus barang dari database

        Notification::make()
            ->title('Barang berhasil dihapus')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
