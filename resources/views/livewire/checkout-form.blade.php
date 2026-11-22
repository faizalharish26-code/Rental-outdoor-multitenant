<div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6">
    <header>
        <p class="text-sm font-semibold uppercase tracking-wide text-amber-800">
            Rental Outdoor
        </p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900">
            Buat pesanan sewa
        </h1>
        <p class="mt-2 text-zinc-600">
            Pilih toko, jadwal sewa, dan unit perlengkapan yang dibutuhkan.
        </p>
    </header>

    @if (session()->has('message'))
        <div role="status" class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-900">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900">
            <p class="font-semibold">Pesanan belum dapat dibuat:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="tenant_id" class="mb-2 block font-semibold text-zinc-800">
                    Toko rental
                </label>
                <select
                    id="tenant_id"
                    wire:model.live="tenant_id"
                    class="min-h-12 w-full rounded-lg border-2 border-zinc-400 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-green-800 focus:outline-none focus:ring-4 focus:ring-green-100"
                >
                    <option value="">Pilih toko</option>
                    @foreach ($tenants as $tenantOption)
                        <option value="{{ $tenantOption->id }}">{{ $tenantOption->name }}</option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_date" class="mb-2 block font-semibold text-zinc-800">
                    Mulai sewa
                </label>
                <input
                    id="start_date"
                    type="datetime-local"
                    wire:model="start_date"
                    class="min-h-12 w-full rounded-lg border-2 border-zinc-400 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-green-800 focus:outline-none focus:ring-4 focus:ring-green-100"
                >
                @error('start_date')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_date" class="mb-2 block font-semibold text-zinc-800">
                    Selesai sewa
                </label>
                <input
                    id="end_date"
                    type="datetime-local"
                    wire:model="end_date"
                    class="min-h-12 w-full rounded-lg border-2 border-zinc-400 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-green-800 focus:outline-none focus:ring-4 focus:ring-green-100"
                >
                @error('end_date')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    @if ($tenant_id)
        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900">Pilih perlengkapan</h2>
                <p class="mt-1 text-sm text-zinc-600">
                    Unit dengan status perawatan tidak bisa dipesan.
                </p>
            </div>

            @forelse ($products as $product)
                <article class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
                    <div>
                        <h3 class="font-semibold text-zinc-900">{{ $product->name }}</h3>
                        <p class="mt-1 text-sm text-zinc-600">
                            Rp {{ number_format((float) $product->rental_price_per_day, 0, ',', '.') }} / hari
                            <span class="px-1">·</span>
                            {{ $product->ready_units_count }} unit berstatus siap
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="addProduct({{ $product->id }})"
                        @disabled($product->ready_units_count < 1)
                        class="min-h-11 rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-zinc-400"
                    >
                        Tambah
                    </button>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-center text-zinc-600">
                    Toko ini belum memiliki produk yang ditampilkan.
                </p>
            @endforelse
        </section>
    @endif

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-7">
        <h2 class="text-xl font-bold text-zinc-900">Daftar pesanan</h2>

        @if (empty($items))
            <p class="mt-3 text-zinc-600">Belum ada produk yang dipilih.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($items as $index => $item)
                    @php
                        $selectedProduct = $selectedProducts->get((int) $item['product_id']);
                    @endphp

                    @if ($selectedProduct)
                        <div
                            wire:key="checkout-item-{{ $item['product_id'] }}"
                            class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 pb-3"
                        >
                            <div>
                                <p class="font-semibold text-zinc-900">
                                    {{ $selectedProduct->name }}
                                </p>
                                <p class="text-sm text-zinc-600">
                                    Rp {{ number_format((float) $selectedProduct->rental_price_per_day, 0, ',', '.') }} / hari
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <label for="quantity-{{ $index }}" class="text-sm text-zinc-700">
                                    Jumlah unit
                                </label>
                                <input
                                    id="quantity-{{ $index }}"
                                    type="number"
                                    min="1"
                                    max="30"
                                    wire:model="items.{{ $index }}.quantity"
                                    class="min-h-11 w-24 rounded-lg border-2 border-zinc-400 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-green-800 focus:outline-none focus:ring-4 focus:ring-green-100"
                                >
                                <button
                                    type="button"
                                    wire:click="removeProduct({{ $item['product_id'] }})"
                                    class="min-h-11 rounded-lg border-2 border-zinc-300 px-3 font-semibold text-zinc-700 hover:bg-zinc-50"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            <button
                type="button"
                wire:click="submitOrder"
                class="min-h-12 rounded-lg bg-green-800 px-5 py-2 font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-200"
            >
                Buat pesanan
            </button>
        </div>
    </section>
</div>