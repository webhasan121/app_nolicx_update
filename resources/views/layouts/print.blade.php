<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'nolicx') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> --}}

    {{--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.4/b-3.2.5/b-html5-3.2.5/b-print-3.2.5/fh-4.0.4/datatables.min.css"
        rel="stylesheet" integrity="sha384-aKelen8gbywzeVdWLWyaBp/qRkNUydsl79gglSwlp2lwogW2dGBS9DWxgW1eZ7ax"
        crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous">
    </script>
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.4/b-3.2.5/b-html5-3.2.5/b-print-3.2.5/fh-4.0.4/datatables.min.js"
        integrity="sha384-XA15Ika7T33czAD4/Zkh7J3FU0WX8LUo7A86AGyMJNlq8bSJYRMLO913NMbnUC5f" crossorigin="anonymous">
    </script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        /* A4 pagper with landscape orientation */
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        td,
        th {
            white-space: nowrap
        }

        tr:hover {
            background-color: #f3f4f6
        }

        tr:nth-child(even) {
            background-color: #e2e2e2
        }
    </style>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div>
        {{ $slot }}
    </div>
</body>


<script>
    // let table = new DataTable('#myTable', {
        //     autoFill: true,
        //         layout: {
        //             topStart: {
        //                 buttons: [
        //                 'pdf'
        //                 ]
        //             }
        //         }
        //     });

        // $('#myTable').DataTable({
        //     extend: 'pdfHtml5',
        //     customize: function(doc) {
        //         doc.content.table.widths = ['*', '*', '*', '*', '*'];
        //     },
        //     dom: 'Bftip',
        //     buttons: [
        //     'pdf'
        //     ]
        // });

        // var tbl = $('#myTable');
        // var settings={};
        // settings.buttons = [
        // {
        // extend:'pdfHtml5',
        // text:'Export PDF',
        // orientation:'landscape',
        // customize : function(doc){
        // var colCount = new Array();
        // $(tbl).find('tbody tr:first-child td').each(function(){
        // if($(this).attr('colspan')){
        // for(var i=1;i<=$(this).attr('colspan');$i++){ colCount.push('*'); } }else{ colCount.push('*'); } });
        //     doc.content[1].table.widths=colCount; } } ];
            // $('#myTable').ataTable(settings);
</script>



<script>
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
    
    function makepdf()
    {
        console.log('call the function');
        
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
    }


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
            window.close();
            
            console.log('PDF created and opened in new tab.');
        } catch (err) {
            console.error('doPdfFromCanvas failed:', err);
            alert('Failed to create PDF from canvas: ' + err.message);
        }
    };
    
</script>

</html>