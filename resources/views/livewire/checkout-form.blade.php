<div class="max-w-3xl mx-auto py-12 px-6">
    <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Form Peminjaman Alat (Order)</h2>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="submitOrder" class="space-y-6">
            <!-- Tenant ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ID Tenant / Toko</label>
                <input type="text" wire:model="tenant_id" placeholder="Masukkan UUID Tenant..." 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 border">
                @error('tenant_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tanggal Sewa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Peminjaman</label>
                    <input type="datetime-local" wire:model="start_date" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 border">
                    @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengembalian</label>
                    <input type="datetime-local" wire:model="end_date" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5 border">
                    @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Order Items Table -->
            <div>
                <h3 class="text-md font-semibold text-gray-800 mb-3">Daftar Item Disewa</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarif / Hari</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['product_name'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($item['price_per_day'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <input type="number" wire:model="items.{{ $index }}.quantity" min="1" 
                                        class="w-20 border-gray-300 rounded p-1 text-center border">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md transition duration-200">
                    Buat Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>