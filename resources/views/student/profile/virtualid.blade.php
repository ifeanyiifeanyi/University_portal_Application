@extends('student.layouts.student')

@section('title', 'Student Virtual Id Card')
@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    $student_url = route('student.view.profile');
@endphp
@section('css')
    <style>
        :root {
            --primary-green: #7cac6c;
            --light-green: #e8eed6;
            --dark-green: #778b34;
            --off-white: #f2f3f2;
            --dark: #28222c;
        }


        .print-button {
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: var(--dark-green);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            /* z-index: 1000; */
            animation: pulseAndBounce 2s infinite;
            transition: transform 0.2s ease;
            margin-bottom: 39px
        }

        @keyframes pulseAndBounce {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(124, 172, 108, 0.7);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(124, 172, 108, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(124, 172, 108, 0);
            }
        }

        .print-button:hover {
            animation-play-state: paused;
            transform: scale(1.1);
            background: var(--primary-green);
        }

        .id-card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 20px;
        }

        .id-card-front,
        .id-card-back {
            width: 54mm;
            height: 86mm;
            background: white;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-strip {
            height: 18mm;
            /* Slightly reduced */
            background: var(--primary-green);
            position: relative;
            overflow: hidden;
            text-align: center;
            padding-top: 1.5mm;
            /* Slightly reduced */
        }

        .header-strip::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 40mm;
            height: 20mm;
            background: var(--dark-green);
            border-radius: 50%;
            opacity: 0.3;
        }

        .logo-section {
            display: inline-block;
            margin-bottom: 1mm;
        }

        .school-name {
            color: var(--dark);
            font-size: 10px;
            text-align: center;
            font-weight: 900;
            padding: 0 2mm;
            margin-top: 1mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
        }


        .logo-placeholder {
            width: 14mm;
            /* Slightly reduced */
            height: 14mm;
            /* Slightly reduced */
            background: var(--off-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--dark);
            margin: 0 auto;
        }

        .photo-section {
            width: 30mm;
            /* Slightly reduced */
            height: 29mm;
            /* Slightly reduced */
            background: var(--light-green);
            border: 2px solid var(--primary-green);
            margin: 2mm auto;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
        }

        .details-section {
            padding: 1mm;
            text-align: center;
        }

        .student-name {
            font-size: 10px;
            font-weight: bolder;
            color: var(--dark);
            margin-bottom: 1mm;
        }

        .id-number {
            font-size: 12px;
            color: var(--dark);
            font-weight: 800;
            margin-bottom: 1.5mm;
            padding: 1mm 4mm;
            background: var(--light-green);
            display: inline-block;
            border-radius: 3mm;
        }

        .department {
            font-size: 11px;
            color: var(--dark-green);
            margin-bottom: 1.5mm;
            font-weight: 700;
        }

        .designation {
            font-size: 12px;
            color: var(--dark-green);
            margin-top: 1.5mm;
        }

        /* Back side styling */
        .id-card-back {
            background: var(--light-green);
        }

        .back-header {
            background: var(--primary-green);
            color: white;
            padding: 2mm;
            text-align: center;
            font-size: 12px;
        }

        .back-content {
            padding: 3mm;
            display: flex;
            flex-direction: column;
            gap: 3mm;
            height: calc(100% - 7mm);
        }


        .qr-and-barcode {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* gap: 1mm; */
            background: white;
            padding: 1mm;
            border-radius: 2mm;
        }

        .qr-code {
            width: 18mm;
            /* Slightly reduced */
            height: 18mm;
            /* Slightly reduced */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-section {
            width: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1mm;
        }

        .barcode-text {
            font-size: 9px;
            margin-top: 0.5mm;
            text-align: center;
        }

        .signature-section {
            text-align: center;
            padding: 1.5mm;
            background: white;
            border-radius: 2mm;
        }

        .signature-placeholder {
            width: 20mm;
            height: 5mm;
            /* Slightly reduced */
            margin: 0 auto 1mm;
            border-bottom: 1px solid var(--dark);
        }

        .signature-title {
            font-size: 8px;
            color: var(--dark);
            font-weight: 500;
        }

        .info-section {
            background: white;
            border-radius: 2mm;
            padding: 2mm;
            font-size: 9px;
            color: var(--dark);
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 0.5mm 1mm;
        }

        .info-section td:first-child {
            font-weight: bold;
            width: 25%;
        }

        .terms {
            font-size: 8px;
            color: var(--dark);
            text-align: center;
            padding: 1mm;
            background: white;
            border-radius: 1mm;
            margin-top: auto;
        }

        .details-list {
            font-size: 10px;
            color: var(--dark);
            text-align: left;
            padding: 1mm;
            background: white;
            border-radius: 1mm;
            margin-top: auto;
        }
        @page {
            size: 53.98mm 85.6mm;
            margin: 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .id-card-container {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
            }

            .id-card-container .id-card-front,
            .id-card-container .id-card-back {
                visibility: visible;
                position: relative;
                padding: 0;
                margin: 0;
                width: 53.98mm;
                height: 85.6mm;
                page-break-after: always;
                border: none;
                box-shadow: none;
            }

            .print-buttons {
                display: none;
            }
        }

        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-button {
            padding: 10px 20px;
            background: var(--dark-green);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            animation: pulseAndBounce 2s infinite;
            transition: transform 0.2s ease;
        }

    </style>
@endsection
@section('student')
@if ($student)
<div class="container-xxl mt-3">
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">STUDENT ID CARD</h4>
    </div>

   
</div>





  <div class="id-card-container">

    <div class="id-card-front">
        <div class="header-strip">
            <div class="logo-section">
                <div class="logo-placeholder">
                    <img src="{{ asset('nursinglogo.webp') }}" alt="logo"
                        style="border-radius: 50%;width: 100%; height: 100%;">
                </div>
            </div>

        </div>
        <div class="school-name">
            {{ env('APP_NAME_TITLE') }}
        </div>
        <div class="photo-section">
            @if ($student->user->profile_photo)
                <img src="{{ asset($student->user->profile_photo) }}" alt="Student Photo"
                    style="width: 100%; height: 100%; object-fit: cover;">
            @else
                Student Photo
            @endif
        </div>

        <div class="details-section">
            <div class="student-name">
                {{ $student->user->fullName() }}
            </div>
            <div class="id-number">{{ $student->matric_number }}</div>
            <div class="department">Department of {{ $student->department->name }}</div>
        </div>
    </div>

    <!-- Back Side -->
    <div class="id-card-back">
        <div class="back-header">
            STUDENT INFORMATION
        </div>

        <div class="back-content">
            <div class="qr-and-barcode">


                <div class="qr-code">
                    {!! QrCode::size(70)->generate($student_url) !!}
                </div>
            </div>
            <div class="details-list">
                <small>Gender: <b>{{ $student->gender }}</b></small> <br>
                <small>Faculty: <b>{{ $student->department->faculty->name }}</b></small> <br>
                <small>Blood Group: <b>{{ $student->blood_group }}</b>, Genotype:
                    <b>{{ $student->genotype }}</b></small> <br>
                <small>Expiration Date:
                    <b>{{ $student->created_at->addYear($student->department->duration)->format('d-m-Y') }}</b></small>
            </div>

            <div class="signature-section">
                <div class="signature-placeholder">
                    @if (isset($vc_signature))
                        <img src="{{ asset($vc_signature) }}" alt="VC Signature"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    @endif
                </div>
                <div class="signature-title">Vice Chancellor</div>
            </div>

            <div class="terms">
                If found, please return to: <b>{{ config('app.name') }}</b>
            </div>
        </div>
    </div>
</div>
</div>
<center>
<button onclick="downloadIDCard()" class="print-button" style="right: 100px;">
    <i class="fas fa-download"></i> Download ID Card
</button>
<br>
<br>
<br>
<br>
</center>
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        // Initialize jsPDF
        window.jsPDF = window.jspdf.jsPDF;

        async function downloadIDCard() {
            // Create loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.style.position = 'fixed';
            loadingDiv.style.zIndex = "1000";
            loadingDiv.style.top = '50%';
            loadingDiv.style.left = '50%';
            loadingDiv.style.transform = 'translate(-50%, -50%)';
            loadingDiv.style.background = 'rgba(255, 255, 255, 0.9)';
            loadingDiv.style.padding = '20px';
            loadingDiv.style.borderRadius = '10px';
            loadingDiv.style.boxShadow = '0 0 10px rgba(0,0,0,0.6)';
            loadingDiv.innerHTML = '<h3><i class="fas fa-fan fa-2x fa-spin fa-fw"></i>Generating ID Card...</h3>';
            document.body.appendChild(loadingDiv);

            // Add delay of 5 seconds
            await new Promise(resolve => setTimeout(resolve, 5000));


            try {
                // Capture front of ID card
                const frontCard = document.querySelector('.id-card-front');
                const frontCanvas = await html2canvas(frontCard, {
                    scale: 4, // Higher scale for better quality
                    useCORS: true,
                    logging: false,
                    backgroundColor: null
                });

                // Capture back of ID card
                const backCard = document.querySelector('.id-card-back');
                const backCanvas = await html2canvas(backCard, {
                    scale: 4,
                    useCORS: true,
                    logging: false,
                    backgroundColor: null
                });

                // Create PDF with correct ID card dimensions for portrait
                // CR80 card size in mm: 53.98 × 85.6
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [53.98, 85.6]
                });

                // Add front image
                pdf.addImage(
                    frontCanvas.toDataURL('image/jpeg', 1.0),
                    'JPEG',
                    0,
                    0,
                    53.98,
                    85.6,
                    undefined,
                    'FAST'
                );

                // Add new page for back
                pdf.addPage([53.98, 85.6], 'portrait');

                // Add back image
                pdf.addImage(
                    backCanvas.toDataURL('image/jpeg', 1.0),
                    'JPEG',
                    0,
                    0,
                    53.98,
                    85.6,
                    undefined,
                    'FAST'
                );

                // Download the PDF
                pdf.save(`ID_Card_${document.querySelector('.id-number').textContent.trim()}.pdf`);

            } catch (error) {
                console.error('Error generating ID card:', error);
                alert('There was an error generating the ID card. Please try again.');
            } finally {
                // Remove loading indicator
                document.body.removeChild(loadingDiv);
            }
        }

        // Initialize barcode if it exists
        if (document.getElementById('barcode')) {
            JsBarcode("#barcode", "{{ $student->matric_number }}", {
                format: "CODE128",
                width: 0.91,
                height: 25,
                displayValue: false,
                margin: 0
            });
        }
    </script>
@endsection

    