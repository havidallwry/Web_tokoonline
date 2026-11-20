@extends('layouts.storefront')

@section('title', 'Pesanan Berhasil - Toko Tri Bhakti At Taqwa')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-10 text-center fade-in">
            {{-- Success Icon --}}
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Berhasil Dibuat!</h1>
            <p class="text-gray-500 mb-6">Terima kasih telah berbelanja di Toko Tri Bhakti At Taqwa</p>

            {{-- Order Number --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-emerald-600 mb-1">Nomor Pesanan Anda</p>
                <p class="text-2xl font-extrabold text-emerald-700 tracking-wider">{{ $order->order_number }}</p>
            </div>

            {{-- Order Details --}}
            <div class="text-left space-y-4 mb-8">
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Status</span>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_color }}">{{ $order->status_label }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Metode Pembayaran</span>
                        <span class="font-medium text-gray-800">{{ $order->payment_method_label }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tanggal</span>
                        <span class="font-medium text-gray-800">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-3">
                        <span class="font-bold text-gray-800">Total Pembayaran</span>
                        <span class="font-extrabold text-emerald-600 text-lg">{{ $order->formatted_total }}</span>
                    </div>
                </div>

                @if($order->payment_method === 'transfer_bank')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <div class="flex items-start gap-3 mb-4">
                            <svg class="w-6 h-6 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-bold text-yellow-800">Informasi Pembayaran</p>
                                <p class="text-sm text-yellow-700 mt-1">Silakan transfer ke DANA berikut:</p>
                                <div class="mt-3 text-base text-yellow-900 bg-white/50 p-3 rounded-lg border border-yellow-100">
                                    <p class="font-extrabold">DANA 082260202047</p>
                                    <p class="font-medium text-sm">a.n. Ahmad Syaiful Huda</p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Upload (Tampil jika belum ada bukti) --}}
                        @if(!$order->payment_receipt)
                            <div class="pt-4 border-t border-yellow-200">
                                <p class="text-sm font-bold text-yellow-800 mb-3">Upload Bukti Transfer</p>
                                <form action="{{ route('orders.upload-receipt', $order) }}" method="POST" enctype="multipart/form-data" 
                                      x-data="{ fileName: null }">
                                    @csrf
                                    <div class="space-y-4">
                                        <div class="relative">
                                            <input type="file" name="payment_receipt" id="payment_receipt_success" required accept="image/*"
                                                class="hidden" @change="fileName = $event.target.files[0].name">
                                            <label for="payment_receipt_success" 
                                                class="flex flex-col items-center justify-center w-full h-32 px-4 transition bg-white border-2 border-dashed border-yellow-300 rounded-2xl cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/30 group">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-3 text-yellow-500 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <p class="mb-1 text-sm text-gray-600">
                                                        <span class="font-bold text-emerald-600">Klik untuk upload</span> bukti transfer
                                                    </p>
                                                    <p class="text-xs text-gray-400">PNG, JPG, JPEG (Max. 2MB)</p>
                                                </div>
                                                <template x-if="fileName">
                                                    <div class="absolute inset-0 flex items-center justify-center bg-emerald-50 rounded-2xl border-2 border-emerald-500">
                                                        <div class="text-center px-4">
                                                            <svg class="w-8 h-8 mx-auto mb-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                            <p class="text-sm font-bold text-emerald-800 truncate max-w-[200px]" x-text="fileName"></p>
                                                            <p class="text-xs text-emerald-600 mt-1">Siap dikirim!</p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </label>
                                        </div>

                                        <button type="submit"
                                            class="w-full py-4 bg-emerald-600 text-white font-extrabold rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 active:scale-[0.98]">
                                            Kirim Bukti Pembayaran
                                        </button>
                                    </div>
                                    @error('payment_receipt') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </form>
                            </div>
                        @else
                            {{-- Preview if already uploaded --}}
                            <div class="pt-4 border-t border-yellow-200">
                                <div class="flex items-center justify-between p-3 rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $order->payment_receipt_url }}" alt="Bukti"
                                            class="w-12 h-12 object-cover rounded-lg border border-white shadow-sm">
                                        <div>
                                            <p class="text-sm font-bold text-emerald-800">Bukti Berhasil Diunggah</p>
                                            <p class="text-xs text-emerald-600">Admin akan segera memverifikasi</p>
                                        </div>
                                    </div>
                                    <a href="{{ $order->payment_receipt_url }}" target="_blank"
                                        class="px-4 py-2 bg-white text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('orders.show', $order) }}"
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors">
                    Lihat Detail Pesanan
                </a>
                <a href="{{ route('products.index') }}"
                    class="px-6 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
@endsection