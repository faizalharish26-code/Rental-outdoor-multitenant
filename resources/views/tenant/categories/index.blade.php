<x-layouts::app.sidebar :title="'Kategori produk'">
    <main class="mx-auto max-w-6xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">
                    Kategori produk
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Kelompokkan perlengkapan rental agar mudah ditemukan.
                </p>
            </div>

            <nav aria-label="Navigasi kategori" class="flex flex-wrap gap-3">
                <a
                    href="{{ route('tenant.products.index', $tenant) }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
                >
                    Daftar produk
                </a>

                <a
                    href="{{ route('tenant.categories.create', $tenant) }}"
                    class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700"
                >
                    Tambah kategori
                </a>
            </nav>
        </header>

        @if (session('success'))
            <div
                role="status"
                class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-100"
            >
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                role="alert"
                class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100"
            >
                {{ session('error') }}
            </div>
        @endif

        <p class="text-sm text-zinc-600 dark:text-zinc-300">
            Total {{ $categories->total() }} kategori.
            Kategori yang masih digunakan oleh produk tidak dapat dihapus.
        </p>

        @if ($categories->isEmpty())
            <section class="rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                <h2 class="text-xl font-semibold">Tidak ada kategori pada halaman ini</h2>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Tambahkan kategori seperti Tenda, Tas, atau Alat Masak.
                </p>

                <a
                    href="{{ route('tenant.categories.create', $tenant) }}"
                    class="mt-5 inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700"
                >
                    Tambah kategori
                </a>
            </section>
        @else
            <ul class="space-y-3">
                @foreach ($categories as $category)
                    <li class="flex flex-col gap-4 rounded-xl border border-zinc-300 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700">
                        <div class="min-w-0">
                            <h2 class="break-words text-lg font-semibold">
                                {{ $category->name }}
                            </h2>
                            <p class="mt-1 break-all text-sm text-zinc-600 dark:text-zinc-300">
                                Slug: {{ $category->slug }}
                            </p>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-3">
                            <a
                                href="{{ route('tenant.categories.edit', [$tenant, $category]) }}"
                                aria-label="Edit kategori {{ $category->name }}"
                                class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('tenant.categories.destroy', [$tenant, $category]) }}"
                                onsubmit="return confirm('Hapus kategori ini? Kategori yang masih digunakan produk tidak dapat dihapus.');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    aria-label="Hapus kategori {{ $category->name }}"
                                    class="inline-flex min-h-11 items-center rounded-lg border border-red-300 px-4 py-2 font-semibold text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        {{ $categories->links() }}
    </main>
</x-layouts::app.sidebar>