<x-layouts::app.sidebar :title="'Produk rental'">
    <main class="mx-auto max-w-6xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">Produk rental</h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Kelola produk yang ditawarkan oleh tenant ini.
                </p>
            </div>

            <a
                href="{{ route('tenant.products.create', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white transition hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
            >
                Tambah produk
            </a>
        </header>

        @if (session('success'))
            <div role="status" class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($products->isEmpty())
            <section class="rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                <h2 class="text-xl font-semibold">Belum ada produk</h2>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    Tambahkan produk pertama untuk tenant ini.
                </p>
                <a
                    href="{{ route('tenant.products.create', $tenant) }}"
                    class="mt-5 inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                >
                    Tambah produk
                </a>
            </section>
        @else
            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        <tr>
                            <th scope="col" class="p-4">Nama</th>
                            <th scope="col" class="p-4">Kategori</th>
                            <th scope="col" class="p-4">Harga per hari</th>
                            <th scope="col" class="p-4">Status katalog</th>
                            <th scope="col" class="p-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($products as $product)
                            <tr>
                                <td class="p-4 font-semibold">{{ $product->name }}</td>
                                <td class="p-4">{{ $product->category->name }}</td>
                                <td class="p-4">
                                    Rp {{ number_format((float) $product->rental_price_per_day, 0, ',', '.') }}
                                </td>
                                <td class="p-4">
                                    @if ($product->is_available)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-green-900 dark:bg-green-950 dark:text-green-200">
                                            Tampil
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-900 dark:bg-amber-950 dark:text-amber-200">
                                            Disembunyikan
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-4">
                                        <a
                                            class="min-h-11 content-center font-semibold underline focus-visible:outline focus-visible:outline-2"
                                            href="{{ route('tenant.products.edit', [$tenant, $product]) }}"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('tenant.products.destroy', [$tenant, $product]) }}"
                                            onsubmit="return confirm('Hapus produk ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="min-h-11 font-semibold text-red-700 underline focus-visible:outline focus-visible:outline-2 dark:text-red-300"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $products->links() }}</div>
        @endif
    </main>
</x-layouts::app.sidebar>