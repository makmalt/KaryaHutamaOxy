<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Barang;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition(): array
    {
        return [
            'nama_barang' => $this->faker->word(5),
            'deskripsi' => $this->faker->sentence(),
            'image' => null,
            'barcode' => $this->faker->unique()->ean13(),
            'harga' => $this->faker->randomNumber(5),
            'stok_tersedia' => $this->faker->numberBetween(10, 20),
            'sku_id' => $this->faker->uuid(),
            'hargaBeli' => $this->faker->randomNumber(5),
            'kategori_id' => 1, // Atau isi dengan ID kategori yang valid
        ];
    }
}
