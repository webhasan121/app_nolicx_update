<div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <x-dashboard.page-header>
        Withdraws
    </x-dashboard.page-header>


    <x-dashboard.container x-init="$wire.getWithdraw()">
        <x-dashboard.overview.section x-loading.disabled>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Amount
                </x-slot>
                <x-slot name="content">
                    {{$withdraw?->sum('amount')}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Payable
                </x-slot>
                <x-slot name="content">
                    {{$withdraw?->sum('payable_amount')}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Comission
                </x-slot>
                <x-slot name="content">
                    {{$withdraw?->sum('server_fee')}} | {{$withdraw?->sum('maintenance_fee')}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Paid
                </x-slot>
                <x-slot name="content">
                    {{$paid}}
                </x-slot>
            </x-dashboard.overview.div>
        </x-dashboard.overview.section>


        <x-dashboard.section>

            <x-dashboard.section.header>

                <x-slot name="title">
                    {{-- Pending Withdraw --}}

                </x-slot>
                <x-slot name="content">
                    <div class="flex justify-between items-center overflow-x-scroll" style="scroll-behavior: smooth">

                        <div>
                            <select wire:model.live="fst" class="rounded border py-1 mb-2" id="filter_status">
                                <option value="Pending">Pending {{$pending}}</option>
                                <option value="Accept">Accepted {{$paid}}</option>
                                <option value="Reject">Rejected {{$reject}} </option>
                            </select>
                        </div>



                        <div class="flex space-x-2">

                            <x-secondary-button @click="$dispatch('open-modal','withdraw_filter_modal')">
                                <i class="fas fa-filter"></i>
                            </x-secondary-button>
                            <x-primary-button wire:click='print'>
                                <i class="fas fa-print"></i>
                            </x-primary-button>
                        </div>

                    </div>
                </x-slot>

            </x-dashboard.section.header>
            <br>

            {{$withdraw->links()}}

            <x-dashboard.table :data="$withdraw">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>A/C</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($withdraw as $item)
                    <tr @class(["bg-gray-200 font-bold"=> !$item->seen_by_admin])>
                        <td> {{$loop->iteration}} </td>
                        <td> {{$item->id}} </td>
                        <td>
                            <div>
                                <div class="flex">
                                    {{$item->user?->name}}
                                    @if ($item->user?->subscription)
                                    <span class="bg-indigo-900 text-white ms-1 px-1 rounded">
                                        vip
                                    </span>
                                    @endif
                                    <span class="bg-gray-900 text-white ms-1 px-1 rounded-full">
                                        U
                                    </span>
                                </div>

                                {{$item->user?->email}}
                            </div>
                        </td>
                        <td>
                            {{$item->amount ?? '0'}} TK
                        </td>
                        <td>
                            @if (!$item->is_rejected)
                            {{$item->status ? "Accept" : 'Pending'}}
                            @else
                            <div class="p-1">Reject</div>
                            @endif
                        </td>
                        <td>
                            {{$item->created_at?->toFormattedDateString() }}
                        </td>
                        <td>
                            <div class="flex">
                                <x-nav-link href="{{route('system.withdraw.view', ['id' => $item->id])}}">Details
                                </x-nav-link>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td colspan="3" class="text-right font-bold">Total</td>
                        <td class="font-bold">{{$withdraw?->sum('amount')}}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </x-dashboard.table>

            {{-- <div class="" id="pdf-content">

                @livewire('system.withdraw.pdf', ['from' => $sdate, 'to' => $edate, 'where' => $where, 'q' => $this->q,
                'fst' => $fst])
            </div> --}}

            {{-- <x-dashboard.section.inner>
                <div class="p-3">
                    Filter
                </div>
                <hr />
                <div class="p-3">
                    <div>
                        <p>
                            Search Criteria
                        </p>
                        <div class="flex">
                            <select wire:model.live="fst" class="rounded border py-1 mb-2" id="filter_status">
                                <option value="Pending">Pending {{$pending}}</option>
                                <option value="Accept">Accepted {{$paid}}</option>
                                <option value="Reject">Rejected {{$reject}} </option>
                            </select>
                            <select wire:model.live="where" id="search_where"
                                class="border-0 rounded-md py-1 shadow-none">
                                <option value="find"> ID </option>
                                <option value="query"> User </option>
                            </select>
                        </div>
                        <br>
                        <x-text-input type="text" class="w-full" wire:model.live="q"
                            placeholder="Search by User Name or ID" />
                    </div>
                    <hr class="my-2" />
                    <div class="flex items-center justify-between">
                        <x-text-input type="date" wire:model.live="sdate" placeholder="From Date" />
                        <x-text-input type="date" wire:model.live="edate" placeholder="To Date" />
                    </div>

                </div>
                <hr class="my-2">
                <div class="p-3">
                    <x-secondary-button @click="$dispatch('close-modal', 'withdraw_filter_modal')">
                        Close
                    </x-secondary-button>
                    <x-primary-button wire:click='filter'>
                        Filter
                    </x-primary-button>
                    <x-primary-button wire:click='print'>
                        <i class="fas fa-print"></i>
                    </x-primary-button>
                </div>
            </x-dashboard.section.inner> --}}
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="withdraw_filter_modal" maxWidth="sm">
        <div class="p-3">
            Filter
        </div>
        <hr />
        <div class="p-3">
            <div>
                <p>
                    Search Criteria
                </p>
                <select wire:model.live="where" id="search_where" class="border-0 rounded-md py-1 shadow-none">
                    <option value="find"> ID </option>
                    <option value="query"> User </option>
                </select>
                <br>
                <x-text-input type="text" class="w-full" wire:model.live="q" placeholder="Search by User Name or ID" />
            </div>
            <hr class="my-2" />
            <div class="flex items-center justify-between">
                <x-text-input type="date" wire:model.live="sdate" placeholder="From Date" />
                <x-text-input type="date" wire:model.live="edate" placeholder="To Date" />
            </div>

        </div>
        <hr class="my-2">
        <div class="p-3">
            <x-secondary-button @click="$dispatch('close-modal', 'withdraw_filter_modal')">
                Close
            </x-secondary-button>
            <x-primary-button wire:click='filter'>
                Filter
            </x-primary-button>
        </div>
    </x-modal>

    <script>
        window.addEventListener('open-printable', (e) => {
            // console.log(e.detail[0].url);
            window.open(e.detail[0].url, '_blank');
        });
        
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('open-printable', (data) => {
                // Use an async IIFE to safely use await inside a non-module environment
                (async () => {
                try {
                // --- Basic checks ---
                console.log('PDF generation started');
                if (typeof html2canvas === 'undefined') {
                console.error('html2canvas is not loaded (undefined).');
                alert('html2canvas not loaded. Make sure the CDN script tag is present.');
                return;
                }
                if (typeof window.jspdf === 'undefined') {
                console.error('jsPDF is not loaded (window.jspdf undefined).');
                alert('jsPDF not loaded. Make sure the CDN script tag is present.');
                return;
                }
                
                // Get jsPDF constructor
                const { jsPDF } = window.jspdf || {};
                if (typeof jsPDF !== 'function') {
                console.error('jsPDF import failed, window.jspdf:', window.jspdf);
                alert('jsPDF not available. Check console for window.jspdf object.');
                return;
                }
                
                // Find the element to convert
                const element = document.querySelector('#pdf-content');
                if (!element) {
                console.error('No element found with id="pdf-content"');
                alert('No #pdf-content element found. Please add id="pdf-content" to the container you want to export.');
                return;
                }
                
                console.log('Calling html2canvas on element:', element);
                
                // --- Call html2canvas and await the promise ---
                const canvas = await html2canvas(element, { scale: 2, useCORS: true });
                
                // Debug: what did we get?
                console.log('html2canvas resolved value:', canvas);
                if (!canvas) {
                throw new Error('html2canvas returned null/undefined.');
                }
                
                // Validate it's a canvas and has toDataURL
                if (typeof canvas.toDataURL !== 'function') {
                console.error('Returned object is not a canvas or has no toDataURL. Constructor/type:', canvas.constructor?.name ||
                typeof canvas);
                // Extra attempt: if html2canvas returned an array or object, try to get .canvas property
                if (canvas?.canvas && typeof canvas.canvas.toDataURL === 'function') {
                console.warn('Using canvas.canvas.toDataURL fallback');
                doPdfFromCanvas(canvas.canvas, jsPDF);
                return;
                }
                throw new Error('The object returned by html2canvas does not support toDataURL.');
                }
                
                // All good -> create PDF
                doPdfFromCanvas(canvas, jsPDF);
                
                } catch (err) {
                console.error('PDF generation failed:', err);
                alert('PDF generation error — see console for details: ' + (err && err.message ? err.message : err));
                }
                })();
            });
            
        });
    
        // Helper that takes a valid HTMLCanvasElement and opens PDF
        function doPdfFromCanvas(canvas, jsPDF) {
            try {
                const imgData = canvas.toDataURL('image/png');
                
                // Create ajsPDF instance sized to A4
                const pdf = new jsPDF({
                orientation: 'p',
                unit: 'mm',
                format: 'a4'
                });
                
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                const imgWidth = pageWidth;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // If image is taller than page, we add it and let PDF client handle multiple pages
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                
                const blob = pdf.output('blob');
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank');
                
                console.log('PDF created and opened in new tab.');
            } catch (err) {
                console.error('doPdfFromCanvas failed:', err);
                alert('Failed to create PDF from canvas: ' + err.message);
            }
        };
        
            
    </script> --}}
</div>