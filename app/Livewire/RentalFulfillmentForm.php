<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\RentalFulfillment;
use Livewire\Component;

class RentalFulfillmentForm extends Component
{
    public $orderId;
    public $order;
    
    // Form fields untuk fulfillment (pengembalian)
    public $returned_at;
    public $condition_status = 'good';
    public $fine_amount = 0;
    public $notes;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::with('items.product')->findOrFail($orderId);
        
        // Set default waktu pengembalian ke waktu sekarang (format input datetime-local)
        $this->returned_at = now()->format('Y-m-d\TH:i');
    }

    public function saveFulfillment()
    {
        $this->validate([
            'returned_at' => 'required|date',
            'condition_status' => 'required|in:good,damaged,lost',
            'fine_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Simpan data ke tabel rental_fulfillments
        RentalFulfillment::updateOrCreate(
            ['order_id' => $this->orderId],
            [
                'returned_at' => $this->returned_at,
                'condition_status' => $this->condition_status,
                'fine_amount' => $this->fine_amount,
                'notes' => $this->notes,
                'processed_by' => auth()->id(), // Menyimpan ID user/admin yang bertugas
            ]
        );

        // Update status order menjadi completed atau fulfilled
        $this->order->update([
            'status' => 'completed'
        ]);

        session()->flash('success', 'Data pengembalian (fulfillment) berhasil disimpan!');

        return redirect()->route('orders.index'); // Sesuaikan dengan route daftar pesanan kalian
    }

    public function render()
    {
        return view('livewire.rental-fulfillment-form');
    }
}