<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Example row with comments
        return [
            [
                'John', // first_name
                'Doe',  // last_name
                'Smith', // other_name
                'john.doe@example.com', // email
                '1234567890', // phone
                '1990-01-01', // date_of_birth
                'Male',  // gender
                'Lagos', // state_of_origin
                'Nigerian', // nationality
                '2023', // year_of_admission
                'UTME', // mode_of_entry
                '100', // current_level
                'NIG/2023/123456' // jamb_reg_no
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'First Name*',
            'Last Name*',
            'Other Name',
            'Email*',
            'Phone Number',
            'Date of Birth* (YYYY-MM-DD)',
            'Gender* (Male/Female/Other)',
            'State of Origin*',
            'Nationality*',
            'Year of Admission* (YYYY)',
            'Mode of Entry* (UTME/Direct Entry/Transfer)',
            'Current Level*',
            'JAMB Registration Number'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 15,
            'D' => 25,
            'E' => 15,
            'F' => 20,
            'G' => 25,
            'H' => 20,
            'I' => 15,
            'J' => 20,
            'K' => 30,
            'L' => 15,
            'M' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add validation rules in a new row
        $validationRules = [
            'Required',
            'Required',
            'Optional',
            'Required, Valid Email',
            'Optional, Numbers Only',
            'Required, Format: YYYY-MM-DD',
            'Required, Choose: Male/Female/Other',
            'Required',
            'Required',
            'Required, 4 digits',
            'Required, Choose from list',
            'Required, Numbers Only',
            'Optional'
        ];
        
        $sheet->insertNewRowBefore(2);
        $sheet->fromArray([$validationRules], null, 'A2');

        // Add data validation for specific columns
        $lastRow = $sheet->getHighestRow();
        
        // Gender validation
        for ($row = 3; $row <= $lastRow; $row++) {
            $validation = $sheet->getCell("G{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Gender');
            $validation->setError('Please select from: Male, Female, or Other');
            $validation->setFormula1('"Male,Female,Other"');
        }

        // Mode of Entry validation
        for ($row = 3; $row <= $lastRow; $row++) {
            $validation = $sheet->getCell("K{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Mode of Entry');
            $validation->setError('Please select from: UTME, Direct Entry, or Transfer');
            $validation->setFormula1('"UTME,Direct Entry,Transfer"');
        }

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4B5563']]
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']]
            ],
            'A3:M3' => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]
            ],
        ];
    }
}