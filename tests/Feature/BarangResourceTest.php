<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BarangResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Resources\Pages\DeleteRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Filament\Resources\BarangResource\Pages\EditBarang;
use App\Filament\Resources\BarangResource\Pages\ViewBarang;
use App\Filament\Resources\BarangResource\Pages\CreateBarang;

beforeEach(function () {
    $this->admin = User::where('email', 'admin@karyahutamaoxygen.com')->firstOrFail();
});


it('can list barang', function () {
    Barang::factory(1)->make();

    actingAs($this->admin)
        ->get(BarangResource::getUrl('index'))
        ->assertOk()
        ->assertSee(Barang::first()->nama);
});


it('buat barang', function () {
    $barangData = Barang::factory()->make()->toArray();

    // Simulasi form create di Filament
    Livewire::test(CreateBarang::class, ['resource' => BarangResource::class])
        ->set('data', $barangData)
        ->call('create')
        ->assertOk();
});



it('edit barang', function () {
    $barang = Barang::factory()->create();
    $updatedData = ['nama_barang' => 'Barang Update'];

    Livewire::test(EditBarang::class, ['record' => $barang->getRouteKey()])
        ->set('data.nama_barang', $updatedData['nama_barang'])
        ->call('save')
        ->assertHasNoErrors();

    expect(Barang::find($barang->id)->nama_barang)->toBe('Barang Update');
});

it('hapus barang', function () {
    $barang = Barang::factory()->create();

    // Pastikan barang ada sebelum dihapus
    expect(Barang::find($barang->id))->not->toBeNull();

    Livewire::test(EditBarang::class, ['record' => $barang->getRouteKey()])
        ->call('deleteRecord')
        ->assertHasNoErrors();

    // Cek apakah barang sudah dihapus
    expect(Barang::find($barang->id))->toBeNull();
});
