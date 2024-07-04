<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ $title }} | {{ $classSchool->name }}</title>
    <link rel="icon" type="image/png" href="./images/logo-small.png">
    <style>
        /* main */
        body {
            padding: 30px;
            position: relative;
        }

        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        h1,
        .h2,
        h3,
        .s1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
        }

        h1 {
            font-size: 11pt;
        }

        .h2 {
            font-size: 10pt;
        }

        p {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            text-decoration: none;
            font-size: 6pt;
            margin: 0;
        }

        h3,
        .s1,
        .s2,
        .s4,
        .s6,
        .s7 {
            font-size: 8pt;
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 6pt;
        }

        .s5 {
            font-size: 7pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }

        .center-align {
            text-align: center;
        }

        .left-align {
            text-align: left;
        }

        .right-align {
            text-align: right;
        }

        .no-indent {
            text-indent: 0pt;
        }

        .pad-179 {
            padding: 0 20pt;
        }

        .pad-5 {
            padding-top: 5pt;
        }

        .pad-2-1 {
            padding-top: 3pt;
            padding-bottom: 1pt;
        }

        .line-height-123 {
            line-height: 123%;
        }

        .information-container {
            width: 100%;
            border-collapse: collapse;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .h-14 {
            height: 14pt;
        }

        .cell-style {
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
        }

        .w-17 {
            width: 17pt;
        }

        .bg-grey {
            background-color: #999999;
        }

        .s1 {
            text-indent: 0pt;
        }

        .left-text {
            text-align: left;
        }

        .pt-2 {
            padding-top: 2pt;
        }

        .pt-3 {
            padding-top: 3pt;
        }

        .pt-7 {
            padding-top: 7pt;
        }

        .pl-75 {
            padding-left: 75pt;
        }

        .h-25 {
            height: 25pt;
        }

        p.s2 {
            text-indent: 0pt;
        }

        p.s3 {
            text-indent: 0pt;
        }

        p.s2-right {
            text-align: right;
            padding-right: 2pt;
        }

        p.s2-left {
            text-align: left;
            padding-left: 2pt;
        }

        p.s2-center {
            text-align: center;
            padding-left: 1pt;
        }

        p.status-good {
            text-align: center;
            padding-left: 1pt;
        }

        .signature-top {
            display: flex;
            justify-content: space-between;
        }

        .teacher {
            text-align: center;
        }

        .signature-top {
            display: flex;
            justify-content: space-between;
        }

        .parent {
            text-align: left;
        }

        .teacher {
            text-align: right;
        }

        .signature-bottom {
            margin-top: 10px;
            text-align: center;
        }

        .signature-bottom .s7 {
            display: inline-block;
            max-width: 200px;
            border-bottom: 1px solid black;
            text-align: center;
            margin: 0 auto;
        }

        .watermarked {
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ public_path() . '/images/logo-small.png' }}");
            background-size: 40%;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: -1;
        }

        @media print {
            td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .watermark {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url("{{ public_path() . '/images/logo-small.png' }}");
                background-size: 40%;
                background-position: center center;
                background-repeat: no-repeat;
                opacity: 0.1;
                z-index: -1;
            }
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="watermark"></div>
    @foreach ($data_anggota_kelas as $anggota_kelas)
        <div class="raport">
            <table class="header-table" style="width: 100%; border-collapse: collapse">
                <tr>
                    <td class="no-indent left-align" style="vertical-align: middle">
                        <img src="{{ public_path() . '/images/logo.png' }}" alt="" width="140" height="60"
                            style="margin-left: -5px">
                    </td>

                    <td class="title-center">
                        <h1 class="center-align pad-179 pad-5 no-indent">
                            <span style="font-size: 12pt">
                                {{ strtoupper($sekolah->school_name) }}
                            </span>
                            <br>

                            <span style="font-size: 10.5pt">
                                @if ($term == 1)
                                    FIRST TERM REPORT
                                @elseif($term == 2)
                                    SECOND TERM REPORT
                                @elseif($term == 3)
                                    SECOND TERM REPORT
                                @elseif($term == 4)
                                    THIRD TERM REPORT
                                @endif
                            </span>
                            <br>
                            {{ str_replace('-', ' / ', $classSchool->academicYear->year) }}
                        </h1>
                        <p class="center-align pad-126 line-height-123" style="padding: 0 60pt">
                            Address : {{ $sekolah->address }} Phone: {{ $sekolah->number_phone }}
                        </p>
                    </td>

                    <td class="no-indent right-align">
                        <img src="{{ public_path() . '/images/tut-wuri-handayani.png' }}" alt="" width="80px"
                            height="80px" style="margin-right: 10px">
                    </td>
                </tr>
            </table>

            <!-- information name -->
            <table class="information-container pad-2-1">
                <tr>
                    <td style="width: 6%">
                        <h3>Name</h3>
                    </td>
                    <td style="width: 64%">
                        <h3>: {{ $anggota_kelas->student->fullname }}</h3>
                    </td>
                    <td style="width: 12%">
                        <h3>Homeroom</h3>
                    </td>
                    <td style="width: 24%">
                        <h3>: {{ $anggota_kelas->classSchool->name }}</h3>
                    </td>
                </tr>
                <tr>
                    <td style="width: 6%">
                        <h3>NISN</h3>
                    </td>
                    <td style="width: 73%">
                        <h3>: {{ $anggota_kelas->student->nisn }}</h3>
                    </td>
                    <td style="width: 19%">
                        <h3>Homeroom Teacher</h3>
                    </td>
                    <td style="width: 19%">
                        <h3>: {{ $anggota_kelas->classSchool->teacher->employee->fullname }}</h3>
                    </td>
                </tr>
            </table>

            <!-- Nilai raport -->
            <table style="border-collapse:collapse;" cellspacing="0">
                <!-- Heading table -->
                <tr style="height:14pt">
                    <td style="width:647px; border: 1px solid black" bgcolor="#999999">
                        <p class="s1" style="text-align: center; padding: 7px">AREA OF
                            LEARNING & DEVELOPMENT</p>
                    </td>
                    <td style="width:74px;border: 1px solid black" bgcolor="#999999">
                        <p class="s1" style="text-align: center; padding: 7px">
                            Achievements</p>
                    </td>
                </tr>

                {{-- Content Table --}}
                <?php $no = 0; ?>
                <?php $no++; ?>
                @foreach ($dataTkElements as $element)
                    <tr style="height:25pt; background-color: #999999">
                        <td style="width:647px; border: 1px solid black">
                            <p class="s1" style="text-align: center; padding: 4px;">
                                {{ $element->name }}</p>
                        </td>
                        <td style="width:74px; border: 1px solid black">
                        </td>
                    </tr>
                    @foreach ($dataTkTopics->where('tk_element_id', $element->id) as $topic)
                        <tr style="height:25pt; background-color: #dfdfdf">
                            <td style="width:647px; border: 1px solid black">
                                <p class="s1" style="padding: 3px;">
                                    {{ $topic->name }}</p>
                            </td>
                            <td style="width:74px; border: 1px solid black;">
                            </td>
                        </tr>
                        @foreach ($dataTkSubtopics->where('tk_topic_id', $topic->id) as $subtopic)
                            <tr style="height:25pt">
                                <td style="width:647px; border: 1px solid black">
                                    <p class="s1" style="padding: 3px; font-style: italic;">
                                        {{ $subtopic->name }}</p>
                                </td>
                                <td style="width:74px; border: 1px solid black">
                                </td>
                            </tr>
                            @foreach ($dataTkPoints->where('tk_subtopic_id', $subtopic->id) as $point)
                                @php
                                    $achivement = $dataAchivements
                                        ->where('tk_point_id', $point->id)
                                        ->where('member_class_school_id', $anggota_kelas->id)
                                        ->first();
                                @endphp
                                @if ($achivement)
                                    @foreach ($dataAchivements->where('tk_point_id', $point->id)->where('member_class_school_id', $anggota_kelas->id) as $achivement)
                                        <tr style="height:25pt">
                                            <td style="647px;;border: 1px solid black">
                                                <p class="s2" style="padding: 3px; font-style: italic;">
                                                    {{ $point->name }}</p>
                                            </td>
                                            <td style="74px;;border: 1px solid black">
                                                <p class="s2" style="padding: 3px; text-align: center;">
                                                    {{ $achivement->achivement }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr style="height:25pt">
                                        <td style="647px;;border: 1px solid black">
                                            <p class="s2" style="padding: 3px; font-style: italic;">
                                                {{ $point->name }}
                                            </p>
                                        </td>
                                        <td style="74px;;border: 1px solid black">
                                            <p class="s2" style="padding: 3px;">
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach

                        @foreach ($dataTkPoints->where('tk_topic_id', $topic->id)->where('tk_subtopic_id', null) as $point)
                            @if ($point->tk_subtopic_id == null && $point->tk_topic_id == $topic->id)
                                @php
                                    $achivement = $dataAchivements
                                        ->where('tk_point_id', $point->id)
                                        ->where('member_class_school_id', $anggota_kelas->id)
                                        ->first();
                                @endphp
                                @if ($achivement)
                                    @foreach ($dataAchivements->where('tk_point_id', $point->id)->where('member_class_school_id', $anggota_kelas->id) as $achivement)
                                        <tr style="height:25pt">
                                            <td style="width:74px;border: 1px solid black">
                                                <p class="s2" style="padding: 3px;">
                                                    {{ $point->name }}</p>
                                            </td>
                                            <td style="width:74px;border: 1px solid black">
                                                <p class="s2" style="padding: 3px; text-align: center;">
                                                    {{ $achivement->achivement }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @php
                                        $achivement = $dataAchivements
                                            ->where('tk_point_id', $point->id)
                                            ->where('member_class_school_id', $anggota_kelas->id)
                                            ->first();
                                    @endphp
                                    @if ($achivement)
                                        @foreach ($dataAchivements->where('tk_point_id', $point->id)->where('member_class_school_id', $anggota_kelas->id) as $achivement)
                                            <tr style="height:25pt">
                                                <td style="width:74px;border: 1px solid black">
                                                    <p class="s2" style="padding: 3px;">
                                                        {{ $point->name }}</p>
                                                </td>
                                                <td style="width:74px;border: 1px solid black">
                                                    <p class="s2" style="padding: 3px; text-align: center;">
                                                        {{ $achivement->achivement }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr style="height:25pt">
                                            <td style="width:74px;border: 1px solid black">
                                                <p class="s2" style="padding: 3px;">
                                                    {{ $point->name }}
                                                </p>
                                            </td>
                                            <td style="width:74px;border: 1px solid black">
                                                <p class="s2" style="padding: 3px;">
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @elseif ($point->tk_subtopic_id == $subtopic->id)
                                <tr style="height:25pt">
                                    <td style="width:74px;border: 1px solid black">
                                        <p class="s2" style="padding: 3px;">
                                            {{ $point->name }}</p>
                                    </td>
                                    <td style="width:74px;border:  1px solid black">
                                        <p class="s2" style="padding: 3px; text-align: center;">
                                            {{ $achivement->achivement }}</p>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endforeach

                <!-- EVENTS -->
                <tr style="height:25pt; background-color: #999999">
                    <td style="width:647px; border: 1px solid black">
                        <p class="s2" style="text-align: center; padding: 5px;">
                            EVENTS</p>
                    </td>
                    <td style="width:74px; border: 1px solid black">
                    </td>
                </tr>
                @foreach ($dataEvents as $event)
                    <tr style="height:25pt">
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px;">
                                {{ $event->name }}</p>
                        </td>
                        <td style="width:74px;border: 1px solid black">
                            @foreach ($dataAchivementEvents->where('member_class_school_id', $anggota_kelas->id) as $achivementEvent)
                                @if ($achivementEvent->tk_event_id == $event->id)
                                    <p class="s2" style="padding: 3px; text-align: center;">
                                        {{ $achivementEvent->achivement_event }}</p>
                                @else
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach

                <!-- ATTENDANCE -->
                <tr style="height:25pt; background-color: #999999">
                    <td style="width:647px; border: 1px solid black">
                        <p class="s2" style="text-align: center; padding: 5px;">
                            ATTENDANCE</p>
                    </td>
                    <td style="width:74px; border: 1px solid black">
                    </td>
                </tr>
                @foreach ($dataAttendance->where('member_class_school_id', $anggota_kelas->id) as $Attendance)
                    <tr style="height:25pt">
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px;">
                                No. of School Days</p>
                        </td>
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px; text-align: center;">
                                {{ isset($Attendance) && $Attendance->no_school_days ? $Attendance->no_school_days : '' }}
                            </p>
                        </td>
                    </tr>
                    <tr style="height:25pt">
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px;">
                                Days Attended</p>
                        </td>
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px; text-align: center;">
                                {{ isset($Attendance) && $Attendance->days_attended ? $Attendance->days_attended : '' }}
                            </p>
                        </td>
                    </tr>
                    <tr style="height:25pt">
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px; ">
                                Days Absent</p>
                        </td>
                        <td style="width:74px;border: 1px solid black">
                            <p class="s2" style="padding: 3px; text-align: center;">
                                {{ isset($Attendance) && $Attendance->days_absent ? $Attendance->days_absent : '' }}
                            </p>
                        </td>
                    </tr>
                @endforeach
            </table>

            <p>
                <br />
            </p>

            {{-- catatan walikelas --}}
            <table style="border-collapse:collapse;" cellspacing="0">
                <!-- REMARKS -->
                <tr style="height:25pt; background-color: #dddddd" colspan="2">
                    <td style="width:733.5px; border: 1px solid black">
                        <p class="s1" style="text-align: center; padding: 5px;">
                            REMARKS</p>
                    </td>
                </tr>
                @foreach ($dataCatatanWalikelas->where('member_class_school_id', $anggota_kelas->id) as $CatatanWalikelas)
                    <tr style="height:25pt">
                        <td style="width:733.5px;border: 1px solid black">
                            <p class="s2" style="padding: 3px;">
                                {{ isset($CatatanWalikelas) && $CatatanWalikelas->notes ? $CatatanWalikelas->notes : '' }}
                            </p>
                        </td>
                    </tr>
                @endforeach
            </table>

            <p>
                <br />
            </p>

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
                                Teacher not available
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
        {{-- page break --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
