<?php

use App\Models\Barang;
use Filament\Pages\Dashboard;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;


class CustomNotif extends Dashboard
{
    protected function getHeaderWidgets(): array
    {
        $this->checkLowStock();

        return parent::getHeaderWidgets();
    }

    private function checkLowStock()
    {
        $lowStockItems = Barang::where('stok_tersedia', '<', 10)->get();

        if ($lowStockItems->count() > 0) {
            $notification = Notification::make()
                ->title('Peringatan Stok Menipis!')
                ->body('Ada ' . $lowStockItems->count() . ' barang yang stoknya kurang dari 10.')
                ->danger();

            $notification->send(); //  toast
            $notification->sendToDatabase(Filament::auth()->user());
        }
    }
}
