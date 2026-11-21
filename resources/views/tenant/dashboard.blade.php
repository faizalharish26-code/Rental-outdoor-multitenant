<x-layouts::app :title="'Dashboard Admin Toko'">
    <div class="mx-auto max-w-6xl space-y-6 p-4 sm:p-8">
        <header>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                {{ $tenant->name }}
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight">
                Dashboard Admin Toko
            </h1>

            <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                Ringkasan katalog dan pesanan untuk toko ini.
            </p>
        </header>

        <section
            aria-label="Ringkasan toko"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                    Produk katalog
                </h2>
                <p class="mt-2 text-3xl font-bold">{{ $productCount }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                    Produk ditampilkan
                </h2>
                <p class="mt-2 text-3xl font-bold">{{ $availableProductCount }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                    Kategori
                </h2>
                <p class="mt-2 text-3xl font-bold">{{ $categoryCount }}</p>
            </article>

            <article class="rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950">
                <h2 class="text-sm font-medium text-amber-900 dark:text-amber-200">
                    Pesanan menunggu
                </h2>
                <p class="mt-2 text-3xl font-bold text-amber-950 dark:text-amber-100">
                    {{ $pendingOrderCount }}
                </p>
            </article>
        </section>

        <nav aria-label="Pintasan pengelolaan toko" class="flex flex-wrap gap-3">
            <a
                href="{{ route('tenant.products.index', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
            >
                Kelola produk
            </a>

            <a
                href="{{ route('tenant.categories.index', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700 dark:border-zinc-700 dark:hover:bg-zinc-800"
            >
                Kelola kategori
            </a>
        </nav>

        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-bold">Aktivitas terbaru</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Produk terakhir yang ditambahkan ke katalog toko ini.
                </p>
            </div>

            @if ($recentProducts->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                    Belum ada produk untuk ditampilkan.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-[650px] w-full text-left text-sm">
                        <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            <tr>
                                <th scope="col" class="p-4">Produk</th>
                                <th scope="col" class="p-4">Kategori</th>
                                <th scope="col" class="p-4">Status</th>
                                <th scope="col" class="p-4">Ditambahkan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($recentProducts as $product)
                                <tr>
                                    <td class="p-4 font-semibold">{{ $product->name }}</td>
                                    <td class="p-4">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="p-4">
                                        @if ($product->is_available)
                                            <span class="rounded-full bg-green-100 px-3 py-1 text-green-900 dark:bg-green-950 dark:text-green-200">
                                                Ditampilkan
                                            </span>
                                        @else
                                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                                Disembunyikan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        {{ $product->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>