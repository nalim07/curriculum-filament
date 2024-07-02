<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }} | {{ $student->fullname }} ({{ $student->nis }})</title>
    <link rel="icon" type="image/png" href="./images/logo-small.png">
    <style>
        /* main */
        body {
            padding: 30px;
            font-family: Arial, sans-serif;
            color: black;
        }

        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        .w-100 {
            width: 100%;
        }

        h1,
        .h2,
        h3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
        }

        h1 {
            font-size: 15px;
        }

        h2 {
            font-size: 11px;
        }

        h3 {
            font-size: 10px;
        }

        h4 {
            font-size: 10px;
        }

        p {
            font-style: normal;
            font-size: 10px;
            line-height: 1.5;
        }

        p.deskripsi-project {
            font-size: 10px;
        }

        .header .title {
            font-size: 16px;
        }

        .header .sub-title {
            font-size: 14px;
        }

        .text-align-center {
            text-align: center;
        }

        .text-align-left {
            text-align: left;
        }

        .text-align-right {
            text-align: right;
        }

        .p-0 {
            padding: 0;
        }

        .mt-15 {
            margin-top: 15px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .watermarked {
            position: relative;
        }

        .watermarked:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0px;
            left: 0px;
            background-image: url("{{ public_path() . '/images/logo-small.png' }}");
            background-size: 40%;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0.1;
        }

        @media print {
            td {
                -webkit-print-color-adjust: exact;
                /* For Chrome */
                print-color-adjust: exact;
                /* For other browsers */
            }
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #333;
            padding: 10px 0;
            background-color: #f9f9f9;
            border-top: 1px solid #ccc;
        }

        .pentunjuk-penilaian th,
        .pentunjuk-penilaian td {
            text-align: left;
            width: 70%;
        }

        table.cell-border,
        table.cell-border th,
        table.cell-border td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;
        }
    </style>
</head>

<body>
    <div class="raport watermarked">
        @php
            $school = App\Models\School::where('id', $student->classSchool->level->school_id)->first();
        @endphp
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <img src="{{ public_path() . '/images/logo.png' }}" alt="" width="145" height="60"
                        style="margin-left: -5px">
                </td>
                <td>
                    <h1 class="text-align-center header">
                        <span class="title">{{ strtoupper($school->school_name) }}</span>
                        <br>
                        <span class="sub-title">
                            RAPOR PROJEK PENGUATAN PROFIL PELAJAR PANCASILA
                            <br>
                            {{ str_replace('-', ' / ', $student->classSchool->academicYear->year) }}
                        </span>
                    </h1>
                    <p class="text-align-center pad-126 line-height-123" style="padding: 0 60pt">
                        Address: {{ $school->address }} Phone: {{ $school->phone }}
                    </p>
                </td>
                <td>
                    <img src="{{ public_path() . '/images/tut-wuri-handayani.png' }}" alt="" width="80px"
                        height="80px">
                </td>
            </tr>
        </table>

        <!-- Information Name -->
        <table class="w-100">
            <tr>
                <td style="width: 6%">
                    <h3>Name</h3>
                </td>
                <td style="width: 64%">
                    <h3>: {{ $student->fullname }}</h3>
                </td>
                <td style="width: 12%">
                    <h3>Homeroom</h3>
                </td>
                <td style="width: 24%">
                    <h3>: {{ $student->classSchool->name }}</h3>
                </td>
            </tr>
            <tr>
                <td style="width: 6%">
                    <h3>NIS</h3>
                </td>
                <td style="width: 73%">
                    <h3>: {{ $student->nis }}</h3>
                </td>
                <td style="width: 19%">
                    <h3>Homeroom Teacher</h3>
                </td>
                <td style="width: 19%">
                    <h3>: {{ $student->classSchool->teacher->employee->fullname }}</h3>
                </td>
            </tr>
        </table>

        @foreach ($PancasilaRaportProject->where('pancasila_raport_group_id', 1) as $tema)
            @if ($loop->first)
                <div style="margin: 15px 2px">
                    <div class="mb-5">
                        <h3>Tema Projek {{ $loop->iteration }} | {{ $tema->title }} |
                            @if ($tema->children->count())
                                @foreach ($tema->children as $subTema)
                                    {{ $subTema->title }}
                                @endforeach
                            @endif
                        </h3>
                        <p class="deskripsi-project" style="margin-top: 5px">
                            Project ini diharapkan dapat membangun dimensi pelajar pancasila yaitu:
                            @if ($tema->children->count())
                                @foreach ($tema->children as $subTema)
                                    <ol
                                        style="margin-left: 21px; padding-left: 10px; font-size: 10px; line-height: 1.5;">
                                        @foreach ($subTema->children as $dimension)
                                            <li>{{ $dimension->title }}</li>
                                        @endforeach
                                    </ol>
                                @endforeach
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            @if ($loop->first)
                <table class="pentunjuk-penilaian" style="padding: 0; margin: 15px 0px">
                    <tr>
                        <th>
                            <h3>BB. Belum Berkembang</h3>
                        </th>
                        <th>
                            <h3>MB. Mulai Berkembang</h3>
                        </th>
                        <th>
                            <h3>BSH. Berkembang Sesuai Harapan</h3>
                        </th>
                        <th>
                            <h3>SB. Sangat Berkembang</h3>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <p style="line-height: 1.2" class="deskripsi-project">Siswa masih membutuhkan bimbingan
                                dalam mengembangkan kemampuan</p>
                        </td>
                        <td>
                            <p style="line-height: 1.2" class="deskripsi-project">Siswa mulai mengembangkan kemampuan
                                namun masih belum ajek</p>
                        </td>
                        <td>
                            <p style="line-height: 1.2" class="deskripsi-project">Siswa telah mengembangkan kemampuan
                                hingga berada dalam tahap ajek</p>
                        </td>
                        <td>
                            <p style="line-height: 1.2" class="deskripsi-project">Siswa mengembangkan kemampuannya
                                melampaui harapan</p>
                        </td>
                    </tr>
                </table>
            @endif

            <h2 style="margin: 5px 0 0 2px;">{{ $loop->iteration }}. {{ $tema->title }}</h2>
            <table class="w-100" style="border-top: none">
                <tr>
                    <td style="width: 60%; border: none; text-align:center;"></td>
                    <td style="width: 10%; border: none; text-align:center;">
                        <h4>BB</h4>
                    </td>
                    <td style="width: 10%; border: none; text-align:center;">
                        <h4>MB</h4>
                    </td>
                    <td style="width: 10%; border: none; text-align:center;">
                        <h4>BSH</h4>
                    </td>
                    <td style="width: 10%; border: none; text-align:center;">
                        <h4>SB</h4>
                    </td>
                </tr>
            </table>
            <table class="w-100 cell-border mb-15" style="border-top: none">
                @php
                    $items = App\Models\PancasilaRaportProject::where('pancasila_raport_group_id', 3)->get();
                    $parentIdToCheck = $tema->id;
                @endphp
                @foreach ($items as $subProject)
                    @if ($subProject->hasParentId($parentIdToCheck))
                        <tr>
                            <td colspan="5" style="background-color: #f0f0f0">
                                <h4>{{ $subProject->name }}</h4>
                            </td>
                        </tr>
                        @if ($subProject->children->count())
                            @foreach ($subProject->children as $child)
                                <tr>
                                    <td style="width: 60%;">
                                        <ul style="margin: 0; padding-left: 16px; list-style: none;">
                                            <li style="list-style-type: disc; font-size: 10px;">
                                                <h4>{{ $child->name }}</h4>
                                            </li>
                                        </ul>
                                    </td>
                                    @foreach ($pancasilaRaportValueDescription as $value)
                                        <td style="width: 10%; text-align: center">
                                            @if ($student->pancasilaRaport()->where('pancasila_raport_project_id', $child->id)->where('prv_description_id', $value['id'])->count())
                                                <div style="font-family: DejaVu Sans, sans-serif;">✔</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <h4>Catatan Proses</h4>
                                        <p>{{ $student->pancasilaRaport()->where('pancasila_raport_project_id', $child->id)->first()->notes ?? '' }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                @endforeach
            </table>
        @endforeach


        <!-- Signature Table -->
        <table class="signature" style="width: 100%;">
            <!-- Top Section -->
            <tr>
                <!-- Parent's Section -->
                <td style="width: 50%; text-align: center;">
                    <p class="s6" style="padding-top: 10pt; text-align: center;">Parent's / Guardian's
                        Signature
                    </p>
                    <p class="s7"
                        style="padding-top: 48pt; text-align: center; border-bottom: 1px solid black; display: inline-block; max-width: 200px; width: 120px; margin: 0 auto; ">
                    </p>
                </td>
                <!-- Teacher's Section -->
                <td style="width: 50%; text-align: center;">
                    <p class="s6" style="text-align: center;">

                        Serang,
                        {{ $km_tgl_raport }}
                        <br>Homeroom Teacher
                    </p>
                    @if (Storage::disk('public')->exists(
                            'ttd/employee/signature/' . $anggota_kelas->classSchool->teacher->employee->employee_code . '.jpg'))
                        <div>
                            <img src="{{ public_path() . '/storage/employee/signature/' . $anggota_kelas->classSchool->teacher->employee->employee_code . '.jpg' }}"
                                alt="{{ $anggota_kelas->classSchool->teacher->employee->employee_code }}"
                                width="120px" class="text-align: center; ">
                        </div>
                    @else
                        <p style="padding-top: 38pt;"></p>
                    @endif
                    <p class="s7"
                        style="text-align: center; border-bottom: 1px solid black; display: inline-block; width: auto;">
                        @if ($anggota_kelas->classSchool->teacher)
                            {{ $anggota_kelas->classSchool->teacher->employee->fullname }}
                        @else
                            Guru not available
                        @endif
                    </p>
                </td>
            </tr>
            <!-- Bottom Section -->
            <tr>
                <td colspan="2" style="text-align: center; margin-top: 15px;">
                    <p class="s6" style="padding-top: 6pt; text-align: center;">Principal's Signature</p>
                    @if (Storage::disk('public')->exists('schools/signature_principal/' . $sekolah->nip_principal . '.jpg'))
                        <div>
                            <img src="{{ public_path() . '/storage/schools/signature_principal/' . $sekolah->nip_principal . '.jpg' }}"
                                alt="{{ $sekolah->nip_principal }}" width="120px" class="text-align: center; ">
                        </div>
                    @else
                        <p style="padding-top: 48pt;"></p>
                    @endif
                    <p class="s7"
                        style="text-align: center; border-bottom: 1px solid black; display: inline-block; width: auto;">
                        {{ $sekolah->principal }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
