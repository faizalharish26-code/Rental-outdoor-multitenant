<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Form Pengembalian Alat (Rental Fulfillment)</h2>
    
    <!-- Informasi Ringkasan Order -->
    <div class="mb-6 p-4 bg-gray-50 rounded-md border">
        <p class="text-sm text-gray-600">No. Order: <span class="font-semibold text-gray-800">{{ $order->order_number }}</span></p>
        <p class="text-sm text-gray-600">Peminjam (User ID): <span class="font-semibold text-gray-800">{{ $order->user_id }}</span></p>
        <p class="text-sm text-gray-600">Periode Sewa: <span class="font-semibold text-gray-800">{{ $order->start_date }} s/d {{ $order->end_date }}</span></p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="saveFulfillment">
        <!-- Waktu Pengembalian -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu Pengembalian Aktual</label>
            <input type="datetime-local" wire:model="returned_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            @error('returned_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Kondisi Barang -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kondisi Barang Saat Dikembalikan</label>
            <select wire:model="condition_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                <option value="good">Baik (Good)</option>
                <option value="damaged">Rusak (Damaged)</option>
                <option value="lost">Hilang (Lost)</option>
            </select>
            @error('condition_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Jumlah Denda -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nominal Denda (Jika ada keterlambatan/kerusakan)</label>
            <input type="number" step="0.01" wire:model="fine_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            @error('fine_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Catatan / Notes -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Catatan Pemeriksaan Kondisi Barang</label>
            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2" placeholder="Contoh: Pasak tenda kurang 2 buah, kain bagian atas sedikit kotor..."></textarea>
            @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                Simpan & Selesaikan Pengembalian
            </button>
        </div>
        <div>
            <a href="{{ route('orders.fulfillment', $order->id) }}">
    Proses Pengembalian
</a>
        </div>
    </form>
</div>