<!DOCTYPE html>
<html>
<head>
    <title>Billboard List</title>
    <style>
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: -80px;
        }

        /* ✅ Logo top right (fixed) */
        .logo {
            position: fixed;
            top: -20px;
            right: 20px;
            width: 180px;
        }

        /* ✅ Fixed footer: remarks + confirmation */
        .footer {
            position: fixed;
            bottom: 130px;
            left: 20px;
            right: 20px;
            width: calc(100% - 40px);
            font-size: 11px;
            page-break-inside: avoid;
        }

        .footer td {
            vertical-align: top;
            padding: 5px;
        }

        .remarks-col {
            width: 60%;
            font-style: italic;
            color: #555;
        }

        .confirmation-box {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
            line-height: 1;
            width: 95%;
        }


       .sitetype-box {
            position: fixed;       /* fixed so it stays at the bottom */
            right: 33px;           /* match footer's right padding */
            bottom: 79px;         /* adjust so it's just above the footer (footer bottom: 130px + gap) */
            border: 1px solid #000;
            padding: 0;
            font-size: 13px;
            line-height: 1;
            width: 100px;          /* adjust as needed */
            background: #fff;      /* optional: make sure it doesn’t overlap other content */
            text-align: center;     /* optional: align content to the right */
        }


        /* ✅ Main content area (with spacing for header + footer) */
        .content {
            margin: 40px 20px 20px 20px; /* top, right, bottom, left */
            position: relative;
        }

        .section {
            page-break-inside: avoid;
            page-break-after: always;
            margin-bottom: 30px;
        }

        .section:last-child {
            page-break-after: auto;
        }

        .info-container {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 130px;
        }

        .image-grid {
            border-collapse: collapse;
            margin-top: 20px;
        }

        .image-grid td {
            padding: 5px;
        }

        .image-grid img {
            max-width: 100%;
            max-height: 360px;
            border: 1px solid #ccc;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <!-- ✅ Logo -->
    <img src="{{ public_path('images/bluedalemedia.jpg') }}" class="logo" alt="Company Logo">

    <div class="header">Billboard List</div>

    <div class="content">
    @foreach($billboards as $billboard)
        <div class="section">
            <div class="info-container">
                <!-- LEFT COLUMN -->
                <div class="info-column">
                    <table class="info-table">
                        <tr><td>Site Number:</td><td>{{ $billboard->site_number }}</td></tr>
                        <tr><td>Type:</td><td>{{ $billboard->type }}</td></tr>
                        <tr><td>Size:</td><td>{{ $billboard->size }}</td></tr>
                    </table>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="info-column">
                    <table class="info-table">
                        <tr><td>Location:</td><td>{{ $billboard->location->name ?? '-' }}</td></tr>
                        <tr><td>District:</td><td>{{ $billboard->location->district->name ?? '-' }}</td></tr>
                        <tr>
                            <td>GPS Coordinates:</td>
                            <td>
                                @php
                                    $mapUrl = !empty($billboard->gps_url)
                                        ? $billboard->gps_url
                                        : "https://www.google.com/maps/search/?api=1&query={$billboard->gps_latitude},{$billboard->gps_longitude}";
                                @endphp

                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">
                                    {{ $billboard->gps_latitude }}, {{ $billboard->gps_longitude }}
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ✅ Billboard Images -->
            @if(!empty($billboard->images))
                <div class="image-section">
                    <table class="image-grid" width="100%">
                        <tr>
                            @foreach ($billboard->images as $img)
                                <td width="{{ 100 / count($billboard->images) }}%" align="center" valign="middle" style="text-align:center; vertical-align:middle;">
                                    <img src="{{ $img }}"
                                         alt="Billboard Image"
                                         style="max-width:100%; max-height:360px; border:1px solid #ccc; display:block; margin:auto;">
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            @endif



            <!-- New box: Site Number -->
            <div class="sitetype-box">
                <p><strong>{{ strtoupper($billboard->site_type ?? '-') }}</strong></p>
            </div>


            <!-- ✅ Footer for this section -->
            <table class="footer">
                <tr>
                    <td class="remarks-col">
                        <strong>REMARK:</strong><br>
                        The site is dependent on availability and council approval and safety regulations. If the proposed sites are unavailable 
                        on the installation day due to reasons such as changes in local council regulations, upgrades to the site to a protocol road, 
                        existing boards from other parties, or safety regulations issues. Bluedale will install the board approximately at the original 
                        location or suggest an alternative site. Once the bunting has been installed, no replacements will be made if it goes missing. 
                        Photos will be provided as proof of installation.
                    </td>
                    <td style="width: 40%;">
                        <!-- Existing Confirmation box -->
                        <div class="confirmation-box">
                            <p><strong>Confirmation / Accepted by</strong></p>
                            <p>Name:</p>
                            <p>Address:</p>
                            <p>Tel No:</p>
                            <p>Company Cop & Sign:</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div> <!-- /.section -->
    @endforeach
</div>


</body>
</html>