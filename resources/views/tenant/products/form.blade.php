<x-layouts::app.sidebar :title="$product->exists ? 'Edit produk' : 'Tambah produk'">
    <main class="mx-auto max-w-3xl space-y-6 p-4 sm:p-8">
        <a
            class="inline-flex min-h-11 items-center font-semibold underline focus-visible:outline focus-visible:outline-2"
            href="{{ route('tenant.products.index', $tenant) }}"
        >
            Kembali ke daftar produk
        </a>

        <header>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                {{ $tenant->name }}
            </p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">
                {{ $product->exists ? 'Edit produk' : 'Tambah produk' }}
            </h1>
        </header>

        @if ($categories->isEmpty())
            <div role="alert" class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-amber-950 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100">
                Tenant ini belum memiliki kategori. Buat kategori terlebih dahulu sebelum menyimpan produk.
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-950 dark:border-red-800 dark:bg-red-950 dark:text-red-100">
                <p class="font-semibold">Periksa kembali isian berikut.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            class="grid gap-5 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700"
            method="POST"
            action="{{ $product->exists
                ? route('tenant.products.update', [$tenant, $product])
                : route('tenant.products.store', $tenant) }}"
        >
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="font-medium">Nama produk</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    maxlength="200"
                    required
                    value="{{ old('name', $product->name) }}"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                @error('name')
                    <p id="name-error" role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="font-medium">Slug URL</label>
                <input
                    id="slug"
                    name="slug"
                    type="text"
                    maxlength="200"
                    required
                    value="{{ old('slug', $product->slug) }}"
                    @error('slug') aria-invalid="true" aria-describedby="slug-error" @enderror
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Spasi akan diubah menjadi tanda hubung.
                </p>
                @error('slug')
                    <p id="slug-error" role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="font-medium">Kategori</label>
                <select
                    id="category_id"
                    name="category_id"
                    required
                    @error('category_id') aria-invalid="true" aria-describedby="category-error" @enderror
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected((string) old('category_id', $product->category_id) === (string) $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p id="category-error" role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="font-medium">Deskripsi</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    maxlength="5000"
                    class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="rental_price_per_day" class="font-medium">Harga sewa per hari (Rp)</label>
                <input
                    id="rental_price_per_day"
                    name="rental_price_per_day"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    value="{{ old('rental_price_per_day', $product->rental_price_per_day) }}"
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                @error('rental_price_per_day')
                    <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="replacement_cost" class="font-medium">Biaya penggantian (Rp)</label>
                <input
                    id="replacement_cost"
                    name="replacement_cost"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    value="{{ old('replacement_cost', $product->replacement_cost ?? 0) }}"
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                @error('replacement_cost')
                    <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deposit_fee" class="font-medium">Deposit (Rp)</label>
                <input
                    id="deposit_fee"
                    name="deposit_fee"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    value="{{ old('deposit_fee', $product->deposit_fee ?? 0) }}"
                    class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 text-zinc-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >
                @error('deposit_fee')
                    <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input type="hidden" name="is_available" value="0">
                <label for="is_available" class="inline-flex min-h-11 cursor-pointer items-center gap-3">
                    <input
                        id="is_available"
                        type="checkbox"
                        name="is_available"
                        value="1"
                        @checked((bool) old('is_available', $product->is_available ?? true))
                        class="size-5 rounded border-zinc-400 text-green-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                    >
                    <span>Tampilkan produk di katalog pelanggan</span>
                </label>
            </div>

            <button
                type="submit"
                @disabled($categories->isEmpty())
                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-green-800 px-5 py-2 font-semibold text-white transition hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Simpan produk
            </button>
        </form>
    </main>
</x-layouts::app.sidebar>