<x-layouts::app :title="'Pembayaran DP'">
    <div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-8">
        <header class="space-y-2">
            <p class="text-sm font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-300">{{ $order->tenant->name }}</p>
            <h1 class="text-3xl font-bold tracking-tight">Pembayaran uang muka</h1>
            <p class="text-zinc-600 dark:text-zinc-300">Pesanan {{ $order->order_number }}</p>
        </header>

        @if (session('success'))
            <div role="status" class="rounded-xl border border-green-300 bg-green-50 p-4 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-300 bg-red-50 p-4 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-7">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Total sewa</p>
                    <p class="mt-1 text-2xl font-bold">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border-2 border-green-800 bg-green-50 p-4 dark:border-green-600 dark:bg-green-950">
                    <p class="text-sm font-semibold text-green-900 dark:text-green-100">DP 50% yang harus dibayar</p>
                    <p class="mt-1 text-2xl font-bold text-green-950 dark:text-white">Rp {{ number_format((float) $order->deposit_amount, 0, ',', '.') }}</p>
                </div>
            </div>

            <dl class="mt-6 grid gap-3 border-t border-zinc-200 pt-5 text-sm dark:border-zinc-700 sm:grid-cols-2">
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Status pembayaran</dt>
                    <dd class="mt-1 font-semibold">{{ match ($order->payment_status) { 'paid' => 'DP sudah dibayar', 'cancelled' => 'Pesanan dibatalkan', default => 'Menunggu pembayaran' } }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500 dark:text-zinc-400">Referensi</dt>
                    <dd class="mt-1 font-semibold">{{ $order->payment->reference }}</dd>
                </div>
            </dl>

            <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                @if ($order->payment_status === 'paid')
                    <p class="font-semibold text-green-800 dark:text-green-300">Pembayaran tercatat pada {{ $order->payment->paid_at?->format('d/m/Y H:i') }}.</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Toko akan meninjau pesananmu sebelum perlengkapan disiapkan.</p>
                @elseif ($order->payment_status === 'cancelled')
                    <p class="font-semibold text-red-800 dark:text-red-300">Pesanan ini sudah dibatalkan, jadi pembayaran simulasi ditutup.</p>
                @elseif (app()->environment(['local', 'testing']) && $order->payment->provider === 'simulation')
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Mode simulasi untuk pengembangan lokal. Tombol ini tidak melakukan transaksi uang sungguhan.</p>
                    <form method="POST" action="{{ route('payments.simulate', $order) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex min-h-12 items-center rounded-xl bg-green-800 px-5 py-3 font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-200 dark:focus:ring-green-900">
                            Simulasikan pembayaran DP
                        </button>
                    </form>
                @else
                    <p class="text-sm text-amber-800 dark:text-amber-300">Instruksi pembayaran gateway belum tersedia.</p>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 sm:p-7">
            <h2 class="text-lg font-bold">Rincian perlengkapan</h2>
            <ul class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3">
                        <span>{{ $item->product->name }} <span class="text-zinc-500">× {{ $item->quantity }}</span></span>
                        <span class="font-semibold">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-layouts::app>