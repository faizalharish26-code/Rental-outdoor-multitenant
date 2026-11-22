<x-layouts::app :title="'Detail Pesanan'">
    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-8">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    {{ $tenant->name }}
                </p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">
                    Detail pesanan
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-300">
                    {{ $order->order_number }}
                </p>
            </div>

            <a
                href="{{ route('tenant.orders.index', $tenant) }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-zinc-300 px-4 py-2 font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
            >
                Kembali ke daftar pesanan
            </a>
        </header>

        @if (session('success'))
            <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @error('status')
            <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                {{ $message }}
            </div>
        @enderror

        @error('returns')
            <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                {{ $message }}
            </div>
        @enderror

        <section class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Pelanggan</h2>
                <p class="mt-2">{{ $order->user?->name ?? 'Pelanggan' }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Status pesanan</h2>
                <p class="mt-2">{{ ucfirst($order->status) }}</p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Mulai sewa</h2>
                <p class="mt-2">
                    {{ \Illuminate\Support\Carbon::parse($order->start_date)->format('d/m/Y H:i') }}
                </p>
            </article>

            <article class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Selesai sewa</h2>
                <p class="mt-2">
                    {{ \Illuminate\Support\Carbon::parse($order->end_date)->format('d/m/Y H:i') }}
                </p>
            </article>
        </section>

        @if ($order->status === 'pending')
            <section class="rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950">
                <h2 class="font-semibold">Tindakan pesanan</h2>
                <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">
                    Status DP: {{ $order->payment_status === 'paid' ? 'sudah dibayar' : 'menunggu pembayaran pelanggan' }}.
                    Pembayaran dicatat melalui alur pembayaran pelanggan.
                </p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <form
                        method="POST"
                        action="{{ route('tenant.orders.status', [$tenant, $order]) }}"
                        onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')"
                    >
                        @csrf
                        <input type="hidden" name="status" value="cancelled">

                        <button
                            type="submit"
                            class="inline-flex min-h-11 items-center rounded-lg border border-red-300 px-4 py-2 font-semibold text-red-800 hover:bg-red-100 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950"
                        >
                            Batalkan pesanan
                        </button>
                    </form>
                </div>
            </section>
        @elseif ($order->status === 'paid')
            <section class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Tindakan pesanan</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Tandai pesanan sudah diserahkan saat barang diberikan kepada pelanggan.
                </p>

                <form
                    class="mt-4"
                    method="POST"
                    action="{{ route('tenant.orders.status', [$tenant, $order]) }}"
                >
                    @csrf
                    <input type="hidden" name="status" value="fulfilled">

                    <button
                        type="submit"
                        class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700"
                    >
                        Tandai sudah diserahkan
                    </button>
                </form>
            </section>
        @elseif ($order->status === 'fulfilled')
            <section class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold">Pemeriksaan barang kembali</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Catat kondisi setiap unit. Unit rusak masuk ke daftar perawatan dan wajib diberi catatan.
                </p>

                @php
                    $returnItemsReady = $order->items->isNotEmpty()
                        && $order->items->every(fn ($item) => $item->inventoryUnit !== null);
                @endphp

                @if (! $returnItemsReady)
                    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-200">
                        Pesanan ini belum memiliki unit inventaris untuk semua barang, jadi pengembalian belum dapat dicatat.
                    </p>
                @else
                    <form
                        class="mt-4 space-y-4"
                        method="POST"
                        action="{{ route('tenant.orders.status', [$tenant, $order]) }}"
                    >
                        @csrf
                        <input type="hidden" name="status" value="completed">

                        @foreach ($order->items as $item)
                            @php
                                $conditionField = "returns.{$item->id}.condition";
                                $noteField = "returns.{$item->id}.note";
                            @endphp

                            <fieldset class="grid gap-4 rounded-lg border border-zinc-200 p-4 sm:grid-cols-2 dark:border-zinc-700">
                                <legend class="px-2 font-semibold">
                                    {{ $item->product?->name ?? 'Produk' }}
                                    ({{ $item->inventoryUnit->unit_code }})
                                </legend>

                                <div>
                                    <label for="condition-{{ $item->id }}" class="block text-sm font-medium">
                                        Kondisi barang
                                    </label>
                                    <select
                                        id="condition-{{ $item->id }}"
                                        name="returns[{{ $item->id }}][condition]"
                                        required
                                        class="mt-1 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 dark:border-zinc-600 dark:bg-zinc-900"
                                    >
                                        <option value="good" @selected(old($conditionField, $item->inventoryUnit->condition) === 'good')>
                                            Baik
                                        </option>
                                        <option value="fair" @selected(old($conditionField, $item->inventoryUnit->condition) === 'fair')>
                                            Cukup
                                        </option>
                                        <option value="damaged" @selected(old($conditionField, $item->inventoryUnit->condition) === 'damaged')>
                                            Rusak
                                        </option>
                                    </select>
                                    @error($conditionField)
                                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="note-{{ $item->id }}" class="block text-sm font-medium">
                                        Catatan kerusakan <span class="font-normal text-zinc-500">(wajib jika rusak)</span>
                                    </label>
                                    <textarea
                                        id="note-{{ $item->id }}"
                                        name="returns[{{ $item->id }}][note]"
                                        rows="2"
                                        maxlength="2000"
                                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-600 dark:bg-zinc-900"
                                    >{{ old($noteField) }}</textarea>
                                    @error($noteField)
                                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </fieldset>
                        @endforeach



                        <button
                            type="submit"
                            class="inline-flex min-h-11 items-center rounded-lg bg-green-800 px-4 py-2 font-semibold text-white hover:bg-green-700"
                        >
                            Simpan pemeriksaan dan selesaikan pesanan
                        </button>
                    </form>
                @endif
            </section>
        @endif

        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-bold">Barang dalam pesanan</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                    Rincian produk, unit, dan biaya pada pesanan ini.
                </p>
            </div>

            @if ($order->items->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                    Belum ada rincian barang pada pesanan ini.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-[760px] w-full text-left text-sm">
                        <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            <tr>
                                <th scope="col" class="p-4">Produk</th>
                                <th scope="col" class="p-4">Kode unit</th>
                                <th scope="col" class="p-4">Jumlah</th>
                                <th scope="col" class="p-4">Harga per hari</th>
                                <th scope="col" class="p-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="p-4 font-semibold">
                                        {{ $item->product?->name ?? 'Produk sudah tidak tersedia' }}
                                    </td>
                                    <td class="p-4">
                                        {{ $item->inventoryUnit?->unit_code ?? 'Belum ditetapkan' }}
                                    </td>
                                    <td class="p-4">{{ $item->quantity }}</td>
                                    <td class="p-4">
                                        Rp {{ number_format((float) $item->price_per_day, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th colspan="4" scope="row" class="p-4 text-right">
                                    Total pesanan
                                </th>
                                <td class="p-4 font-bold">
                                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>