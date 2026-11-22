<x-layouts::app.sidebar :title="$unit->exists ? 'Edit unit inventaris' : 'Tambah unit inventaris'">
    <main class="mx-auto max-w-3xl space-y-6 p-4 sm:p-8">
        <header>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                {{ $tenant->name }}
            </p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">
                {{ $unit->exists ? 'Edit unit inventaris' : 'Tambah unit inventaris' }}
            </h1>
        </header>

        @if ($errors->any())
            <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900">
                <p class="font-semibold">Periksa kembali isian berikut:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $unit->exists
                    ? route('tenant.inventory.update', [$tenant, $unit])
                    : route('tenant.inventory.store', $tenant) }}"
              class="space-y-5 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            @csrf
            @if ($unit->exists)
                @method('PUT')
            @endif

            <div>
                <label for="product_id" class="mb-1 block font-semibold">Produk</label>
                <select id="product_id" name="product_id" required
                        class="min-h-11 w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">
                    <option value="">Pilih produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            @selected((string) old('product_id', $unit->product_id) === (string) $product->id)>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="unit_code" class="mb-1 block font-semibold">Kode unit</label>
                <input id="unit_code" name="unit_code" required maxlength="100"
                       value="{{ old('unit_code', $unit->unit_code) }}"
                       placeholder="Contoh: TENDA-2P-001"
                       class="min-h-11 w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">
            </div>

            <div>
                <label for="condition" class="mb-1 block font-semibold">Kondisi</label>
                <select id="condition" name="condition" required
                        class="min-h-11 w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">
                    @foreach (['good' => 'Baik', 'fair' => 'Cukup', 'damaged' => 'Rusak'] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('condition', $unit->condition ?? 'good') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block font-semibold">Status</label>
                <select id="status" name="status" required
                        class="min-h-11 w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">
                    <option value="available"
                        @selected(old('status', $unit->status ?? 'available') === 'available')>
                        Siap disewa
                    </option>
                    <option value="maintenance"
                        @selected(old('status', $unit->status) === 'maintenance')>
                        Perawatan
                    </option>
                </select>
            </div>

            <div>
                <label for="maintenance_note" class="mb-1 block font-semibold">
                    Kerusakan / catatan perawatan
                </label>
                <textarea id="maintenance_note" name="maintenance_note" rows="3"
                          class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">{{ old('maintenance_note', $unit->maintenance_note) }}</textarea>
                <p class="mt-1 text-sm text-zinc-500">Wajib diisi bila status unit adalah Perawatan.</p>
            </div>

            <div>
                <label for="maintenance_started_at" class="mb-1 block font-semibold">
                    Mulai perawatan
                </label>
                <input id="maintenance_started_at" type="date" name="maintenance_started_at"
                       value="{{ old('maintenance_started_at', $unit->maintenance_started_at?->format('Y-m-d')) }}"
                       class="min-h-11 w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900">
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="min-h-11 rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700">
                    Simpan
                </button>
                <a href="{{ route('tenant.inventory.index', $tenant) }}"
                   class="min-h-11 content-center rounded-lg border px-4 py-2 font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </main>
</x-layouts::app.sidebar>