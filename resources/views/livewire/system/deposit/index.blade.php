<div>

    {{-- Nothing in the world is as soft and yielding as water. --}}



    <x-dashboard.page-header>
        Deposit
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">

                </x-slot>
                <x-slot name="content">
                    <div class="lg:flex justify-between items-center space-x-1">
                        <select wire:model.live="status" id=""
                            class="py-1 mb-1 rounded-md border border-gray-300 focus:border-blue-500 focus:ring-blue-500 focus:ring-1">
                            <option value="0">Pending</option>
                            <option value="1">Confirmed</option>
                        </select>

                        <div class="flex items-center space-x-2">
                            <x-primary-button wire:click='print'>
                                <i class="fas fa-print"></i>
                            </x-primary-button>
                            <x-text-input type="date" id="sdate" wire:model.live='sdate' class=" py-1" />
                            <x-text-input type="date" id="edate" wire:model.live='edate' class=" py-1" />
                        </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
            {{$history->links()}}
            <br>

            <div id="pdf-content">
                <hr clas="my-1" />
                <x-dashboard.table :data="$history">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Trx ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>A/C</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <x-nav-link-btn href="{{route('system.users.edit', ['id' => $item->user?->id ?? ''])}}">
                                    {{$item->user?->name ?? 'N/A'}}
                                </x-nav-link-btn>

                            </td>
                            <td>{{ $item->amount ?? 0 }}</td>
                            <td>
                                <div class="flex items-center">

                                    {{ $item->senderAccountNumber }} <i class="fas fa-caret-right px-2"></i>
                                    {{$item->paymentMethod}} <i class="fas fa-caret-right px-2"></i>
                                    {{$item->receiverAccountNumber}}
                                </div>
                            </td>
                            <td>
                                {{ $item->transactionId ?? 'N/A' }}
                            </td>
                            <td>{{ $item->confirmed ? 'Confirmed' : 'Pending' }}</td>
                            <td>{{ $item->created_at->diffForHumans() }} </td>
                            <td>
                                <div class="flex">
                                    <x-primary-button wire:click="confirmDeposit({{$item->id}})">
                                        <i class="fas fa-check"></i>
                                    </x-primary-button>
                                    <x-danger-button wire:click.prevent="denayDeposit({{$item->id}})">
                                        <i class="fas fa-times"></i>
                                    </x-danger-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right font-bold">Total</td>
                            <td class="font-bold">{{$history?->sum('amount')}}</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </x-dashboard.table>

            </div>

        </x-dashboard.section>
    </x-dashboard.container>


    @push('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> --}}

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     Livewire.on('open-printable', (data) => {
        //         // Use an async IIFE to safely use await inside a non-module environment
        //         (async () => {
        //         try {
        //         // --- Basic checks ---
        //         console.log('PDF generation started');
        //         if (typeof html2canvas === 'undefined') {
        //         console.error('html2canvas is not loaded (undefined).');
        //         alert('html2canvas not loaded. Make sure the CDN script tag is present.');
        //         return;
        //         }
        //         if (typeof window.jspdf === 'undefined') {
        //         console.error('jsPDF is not loaded (window.jspdf undefined).');
        //         alert('jsPDF not loaded. Make sure the CDN script tag is present.');
        //         return;
        //         }
                
        //         // Get jsPDF constructor
        //         const { jsPDF } = window.jspdf || {};
        //         if (typeof jsPDF !== 'function') {
        //         console.error('jsPDF import failed, window.jspdf:', window.jspdf);
        //         alert('jsPDF not available. Check console for window.jspdf object.');
        //         return;
        //         }
                
        //         // Find the element to convert
        //         const element = document.querySelector('#pdf-content');
        //         if (!element) {
        //         console.error('No element found with id="pdf-content"');
        //         alert('No #pdf-content element found. Please add id="pdf-content" to the container you want to export.');
        //         return;
        //         }
                
        //         console.log('Calling html2canvas on element:', element);
                
        //         // --- Call html2canvas and await the promise ---
        //         const canvas = await html2canvas(element, { scale: 2, useCORS: true });
                
        //         // Debug: what did we get?
        //         console.log('html2canvas resolved value:', canvas);
        //         if (!canvas) {
        //         throw new Error('html2canvas returned null/undefined.');
        //         }
                
        //         // Validate it's a canvas and has toDataURL
        //         if (typeof canvas.toDataURL !== 'function') {
        //         console.error('Returned object is not a canvas or has no toDataURL. Constructor/type:', canvas.constructor?.name ||
        //         typeof canvas);
        //         // Extra attempt: if html2canvas returned an array or object, try to get .canvas property
        //         if (canvas?.canvas && typeof canvas.canvas.toDataURL === 'function') {
        //         console.warn('Using canvas.canvas.toDataURL fallback');
        //         doPdfFromCanvas(canvas.canvas, jsPDF);
        //         return;
        //         }
        //         throw new Error('The object returned by html2canvas does not support toDataURL.');
        //         }
                
        //         // All good -> create PDF
        //         doPdfFromCanvas(canvas, jsPDF);
                
        //         } catch (err) {
        //         console.error('PDF generation failed:', err);
        //         alert('PDF generation error — see console for details: ' + (err && err.message ? err.message : err));
        //         }
        //         })();
        //     });
            
        // });
        
        // Helper that takes a valid HTMLCanvasElement and opens PDF
        // function doPdfFromCanvas(canvas, jsPDF) {
        //     try {
        //         const imgData = canvas.toDataURL('image/png');
                
        //         // Create ajsPDF instance sized to A4
        //         const pdf = new jsPDF({
        //         orientation: 'p',
        //         unit: 'mm',
        //         format: 'a4'
        //         });
                
        //         const pageWidth = pdf.internal.pageSize.getWidth();
        //         const pageHeight = pdf.internal.pageSize.getHeight();
                
        //         const imgWidth = pageWidth;
        //         const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
        //         // If image is taller than page, we add it and let PDF client handle multiple pages
        //         pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                
        //         const blob = pdf.output('blob');
        //         const url = URL.createObjectURL(blob);
        //         window.open(url, '_blank');
                
        //         console.log('PDF created and opened in new tab.');
        //     } catch (err) {
        //         console.error('doPdfFromCanvas failed:', err);
        //         alert('Failed to create PDF from canvas: ' + err.message);
        //     }
        // };
            
                
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
           window.addEventListener('open-printable', (e) => {
            // console.log(e.detail[0].url);
            window.open(e.detail[0].url, '_blank');
            
            });
        });
    </script>
    @endpush
</div>