<!DOCTYPE html>
<html>

<head>
    <title>Billboard Detail</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 15px;
            line-height: 1.1;
            margin: 0;
            padding: 0;
        }

        /* ✅ Logo top left (fixed) */
        .logo {
            position: fixed;
            top: 5px;
            left: 10px;
            width: 120px;
            z-index: 1000;
        }

        /* ✅ Header: centered */
        .header {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 5px 0 10px 0;
            color: red;
            font-size: 20px;
        }

        /* ✅ Main content area */
        .content {
            margin: 20px 10px 80px 10px;
        }

        /* Side-by-side using table */
        .tables-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            /* Reduced from 10px */
        }

        .tables-container td {
            vertical-align: top;
            padding: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 130px;
        }

        /* Landmarks Table */
        .landmarks-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            /* Full border around table */
            font-size: 15px;
        }

        .landmarks-table th {
            background-color: #ffff00;
            /* Yellow header */
            padding: 3px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            /* Border on header cells */
        }

        /* Style for the first column (left) - Yellow background */
        .landmarks-table td:first-child {
            background-color: #ffff00;
            /* Yellow */
            padding: 3px;
            font-weight: bold;
            border: 1px solid #000;
            /* Full border on each cell */
        }

        /* Style for the second column (right) - White background */
        .landmarks-table td:not(:first-child) {
            background-color: #ffffff;
            /* White */
            padding: 3px;
            border: 1px solid #000;
            /* Full border on each cell */
        }

        /* Image Section: photo + map side by side */
        .image-section {
            margin-top: 25px;
            /* Reduced from 10px → brings images closer to tables */
            text-align: center;
        }

        .image-section img {
            width: 46%;
            /* Slightly narrower → better ratio */
            max-height: 500px;
            /* Taller → matches screenshot */
            object-fit: contain;
            border: 1px solid #ccc;
            vertical-align: top;
            display: inline-block;
            margin: 0 2px;
        }

        /* Contact Box - Fixed at bottom right */
        .contact-box {
            position: fixed;
            right: 20px;
            bottom: 50px;
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
            line-height: 1;
            width: 160px;
            background: #022a52ff;
            /* Dark Blue Background */
            color: #ffffff;
            /* White Font Color */
            text-align: center;
            z-index: 999;
        }

        .contact-box p {
            margin: 2px 0;
        }

        /* Footer: Remark at bottom (non-fixed, flows after content) */
        .footer {
            position: fixed;
            bottom: 50px;
            /* 👈 Increased from 15px → gives more space above footer */
            left: 10px;
            right: 10px;
            width: calc(100% - 20px);
            font-size: 9px;
            padding: 8px;
            /* 👈 Increased padding → more readable */
            background: #022a52ff;
            border-top: 1px solid #000;
            word-wrap: break-word;
            /* Ensure long text wraps */
        }

        .footer td {
            vertical-align: top;
            padding: 4px;
            /* 👈 Slightly increased */
        }

        .remarks-col {
            width: 100%;
            font-style: italic;
            color: #ffffff;
            line-height: 1;
            /* Improved readability */
        }
    </style>
</head>

<body>

    <!-- ✅ Logo -->
    <img src="{{ public_path('images/bluedalemedia.jpg') }}" class="logo" alt="Company Logo">

    <!-- ✅ Header -->
    @foreach ($billboards as $billboard)
        <div class="header">{{ $billboard->type }}</div>

        <div class="content">
            <!-- CONTAINER FOR SIDE-BY-SIDE TABLES USING TABLE LAYOUT -->
            <table class="tables-container">
                <tr>
                    <!-- LEFT COLUMN: Site Info -->
                    <td style="width: 50%; padding-right: 10px;">
                        <table class="info-table">
                            <tr>
                                <td>Site Number:</td>
                                <td>{{ $billboard->site_number }}
                                    (<strong>{{ strtoupper($billboard->site_type ?? '-') }}</strong>)</td>
                            </tr>
                            <tr>
                                <td>Size:</td>
                                <td>{{ $billboard->size }}</td>
                            </tr>
                            <tr>
                                <td>Location:</td>
                                <td style="color: red;"><strong>{{ $billboard->location->name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td>State & City:</td>
                                <td>{{ $billboard->location->district->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Coordinate:</td>
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
                    </td>

                    <!-- RIGHT COLUMN: Landmarks Table -->
                    <td style="width: 50%; padding-left: 10px;">
                        <table class="landmarks-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Nearest Landmarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 40%;">Exhibition Center</td>
                                    <td style="border: 1px solid #000; padding-left: 5px; width: 60%;"></td>
                                </tr>
                                <tr>
                                    <td style="width: 40%;">Shopping Mall</td>
                                    <td style="border: 1px solid #000; padding-left: 5px; width: 60%;"></td>
                                </tr>
                                <tr>
                                    <td style="width: 40%;">International School</td>
                                    <td style="border: 1px solid #000; padding-left: 5px; width: 60%;"></td>
                                </tr>
                                <tr>
                                    <td style="width: 40%;">Hosp/ Medical Center</td>
                                    <td style="border: 1px solid #000; padding-left: 5px; width: 60%;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- 🖼️ IMAGES BELOW INFO: Photo + Map Side by Side -->
            <div class="image-section">
                <div class="image-grid">
                    @foreach ($billboard->images as $img)
                        <img src="{{ $img }}" alt="Billboard Image">
                    @endforeach
                </div>
            </div>

            <!-- ✅ Contact Box - Bottom Right -->
            <div class="contact-box">
                <p><strong>Contact:</strong></p>
                <p><strong>Asyiqin: 014-9027253</strong></p>
                <p><strong>Annie: 012-2200622</strong></p>
            </div>

            <!-- ✅ Footer: Remark at Bottom -->
            <table class="footer">
                <tr>
                    <td class="remarks-col">
                        <strong>REMARK: The site is subject to availability, authority approval, and safety regulations.
                            In the event that the proposed sites are unavailable on the installation day - whether due
                            to changes in local council regulations,
                            site upgrades to a protocol road, the presence of existing boards from other parties, or
                            safety-related issues - Bluedale will proceed to install the board at a nearby location as
                            close as possible to the original site, or
                            suggest an alternative. Photo evidence will be provided as proof of installation.
                            Replacement of missing boards is only applicable at no extra charge for clients who purchase
                            the current promotion with a minimum 3-month contract or longer.
                            If a new skin is required, an additional fee of RM500 will apply.</strong>
                    </td>
                </tr>
            </table>
    @endforeach
    </div>

</body>

</html>
