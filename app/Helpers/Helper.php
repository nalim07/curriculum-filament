<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use App\Models\Level;
use App\Models\AcademicYear;

class Helper
{

    public static function formatDate(?string $date, string $format = 'Y-m-d'): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            // Check if the date is already in the desired format
            $parsedDate = \Carbon\Carbon::createFromFormat($format, $date);
            if ($parsedDate && $parsedDate->format($format) === $date) {
                return $date;
            }

            // Try to parse as m/d/Y
            $parsedDate = \Carbon\Carbon::createFromFormat('m/d/Y', $date);
            if ($parsedDate) {
                return $parsedDate->format($format);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public static function getSex($id)
    {
        if ($id === null || $id === '') {
            return 'Unknown';
        }

        return $id == 1 ? 'MALE' : 'FEMALE';
    }

    public static function getReligion($id)
    {
        if ($id == 0) {
            return 'Islam';
        } elseif ($id == 1) {
            return 'Kristen';
        } elseif ($id == 2) {
            return 'Katolik';
        } elseif ($id == 3) {
            return 'Hindu';
        } elseif ($id == 4) {
            return 'Budha';
        } elseif ($id == 5) {
            return 'Konghucu';
        } else {
            return 'Other';
        }
    }

    public static function getMaritalStatus($id)
    {
        if ($id == 1) {
            return 'Married';
        } elseif ($id == 2) {
            return 'Single';
        } elseif ($id == 3) {
            return 'Widow';
        } elseif ($id == 4) {
            return 'Widower';
        } else {
            return 'Other';
        }
    }

    // Helper methods for converting names to IDs
    public static function getSexByName($name)
    {
        if ($name == 'MALE') {
            return '1';
        } elseif ($name == 'FEMALE') {
            return '2';
        } else {
            return null;
        }
    }

    public static function getReligionByName($name)
    {
        if ($name == 'Islam') {
            return '1';
        } elseif ($name == 'Kristen') {
            return '2';
        } elseif ($name == 'Katolik') {
            return '3';
        } elseif ($name == 'Hindu') {
            return '4';
        } elseif ($name == 'Budha') {
            return '5';
        } elseif ($name == 'Konghucu') {
            return '6';
        } else {
            return null;
        }
    }

    public static function getMaritalStatusByName($name)
    {
        if ($name == 'Married') {
            return '1';
        } elseif ($name == 'Single') {
            return '2';
        } elseif ($name == 'Widow') {
            return '3';
        } elseif ($name == 'Widower') {
            return '4';
        } else {
            return '5';
        }
    }

    public static function getDate($date)
    {
        return date('d F Y', strtotime($date));
    }

    public static function getRegistrationType($name)
    {
        if ($name == '1') {
            return 'New Student';
        } elseif ($name == '2') {
            return 'Transfer Student';
        }
    }

    public static function getRegistrationTypeByName($name)
    {
        if ($name == 'New Student') {
            return '1';
        } elseif ($name == 'Transfer Student') {
            return '2';
        }
    }


    public static function getRegistrationTypeMemberClassSchoolByName($id)
    {
        if ($id == 1) {
            return 'New Student';
        } elseif ($id == 2) {
            return 'Transfer Student';
        } elseif ($id == 3) {
            return 'Promotion';
        } elseif ($id == 4) {
            return 'Continuation';
        } elseif ($id == 5) {
            return 'Re-Enrollment';
        }
    }




    public static function getSlotType($type)
    {
        if ($type == 1) {
            return 'Lesson Hours';
        } elseif ($type == 2) {
            return 'Recess';
        } elseif ($type == 3) {
            return 'Mealtime';
        }
    }

    // get academicYear Active
    public static function getActiveAcademicYearId()
    {
        $activeYear = AcademicYear::where('status', true)->first();
        return $activeYear ? $activeYear->id : null;
    }

    public static function getActiveAcademicYearName()
    {
        $activeYear = AcademicYear::where('status', true)->first();
        return $activeYear ? $activeYear->year : null;
    }

    public static function getActiveTermPg()
    {
        $level = Level::where('school_id', 1)->first();
        $term = $level->term_id;
        return $term ? $term : null;
    }

    public static function getActiveSemesterIdPrimarySchool()
    {
        $level = Level::where('id', 4)->first();
        $semester = $level->semester_id;

        return $semester ? $semester : null;
    }

    public static function getActiveTermIdPrimarySchool()
    {
        $level = Level::where('id', 4)->first();
        $term = $level->term_id;

        return $term ? $term : null;
    }

    public static function getPlanFormatifTechnique($id)
    {
        if ($id == 1) {
            return 'Quiz/Chapter Test ';
        } elseif ($id == 2) {
            return 'Projek';
        } elseif ($id == 3) {
            return 'Portfolio';
        } elseif ($id == 4) {
            return 'Classwork';
        } elseif ($id == 5) {
            return 'End Semester';
        } else {
            return 'Other';
        }
    }

    public static function getPlanSumatifTechnique($id)
    {
        if ($id == 1) {
            return 'Tes Tulis';
        } elseif ($id == 2) {
            return 'Praktical Test';
        } else {
            return 'Other';
        }
    }

    // student achievement
    public static function getTypeOfAchievement($id)
    {
        if ($id == 1) {
            return 'Academic';
        } elseif ($id == 2) {
            return 'Non Academic';
        }
    }

    public static function getLevelOfAchievement($id)
    {
        if ($id == 1) {
            return 'International';
        } elseif ($id == 2) {
            return 'National';
        } elseif ($id == 3) {
            return 'Province';
        } elseif ($id == 4) {
            return 'City';
        } elseif ($id == 5) {
            return 'District';
        } elseif ($id == 6) {
            return 'Inter School';
        }
    }
}
