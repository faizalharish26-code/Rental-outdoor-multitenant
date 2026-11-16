<x-layouts::app.sidebar :title="$category->exists ? 'Edit kategori' : 'Tambah kategori'">
    <main class="mx-auto max-w-3xl space-y-6 p-4 sm:p-8">
        <header>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                {{ $tenant->name }}
            </p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">
                {{ $category->exists ? 'Edit kategori' : 'Tambah kategori' }}
            </h1>
            <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                Isi nama dan slug kategori untuk tenant ini.
            </p>
        </header>

        @if ($errors->any())
            <div
                role="alert"
                class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100"
            >
                Ada data yang perlu diperbaiki. Periksa pesan pada kolom form.
            </div>
        @endif

        <form
            method="POST"
            action="{{ $category->exists
                ? route('tenant.categories.update', [$tenant, $category])
                : route('tenant.categories.store', $tenant) }}"
            class="space-y-6 rounded-xl border border-zinc-300 p-5 sm:p-6 dark:border-zinc-700"
        >
            @csrf

            @if ($category->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="block font-semibold">
                    Nama kategori
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $category->name) }}"
                    required
                    maxlength="200"
                    @error('name')
                        aria-invalid="true"
                        aria-describedby="name-error"
                    @enderror
                    class="mt-2 block min-h-11 w-full rounded-lg border border-zinc-400 bg-white p-3 text-zinc-900 focus:border-green-700 focus:ring-2 focus:ring-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >

                @error('name')
                    <p id="name-error" class="mt-2 text-sm text-red-700 dark:text-red-300">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block font-semibold">
                    Slug kategori
                </label>

                <input
                    id="slug"
                    name="slug"
                    type="text"
                    value="{{ old('slug', $category->slug) }}"
                    required
                    maxlength="200"
                    spellcheck="false"
                    autocapitalize="none"
                    aria-describedby="slug-help{{ $errors->has('slug') ? ' slug-error' : '' }}"
                    @error('slug') aria-invalid="true" @enderror
                    class="mt-2 block min-h-11 w-full rounded-lg border border-zinc-400 bg-white p-3 text-zinc-900 focus:border-green-700 focus:ring-2 focus:ring-green-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                >

                <p id="slug-help" class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    Gunakan huruf kecil, angka, dan tanda hubung.
                    Contoh: alat-masak. Slug harus unik di tenant ini.
                </p>

                @error('slug')
                    <p id="slug-error" class="mt-2 text-sm text-red-700 dark:text-red-300">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="submit"
                    class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-5 py-2 font-semibold text-white hover:bg-green-700"
                >
                    {{ $category->exists ? 'Simpan perubahan' : 'Simpan kategori' }}
                </button>

                <a
                    href="{{ route('tenant.categories.index', $tenant) }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-5 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
                >
                    Batal
                </a>
            </div>
        </form>
    </main>
</x-layouts::app.sidebar>