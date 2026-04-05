<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StudentTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Get departments for example data
        $departments = Department::pluck('name')->toArray();
        
        return [
            [
                'John Doe',
                'john.doe@example.com',
                'REG2024001',
                $departments[0] ?? 'Computer Science and Engineering',
                'B.Tech Computer Science',
                '9876543210',
                '2000-01-15'
            ],
            [
                'Jane Smith',
                'jane.smith@example.com',
                'REG2024002',
                $departments[1] ?? 'Information Technology',
                'B.Tech Information Technology',
                '9876543211',
                '2000-02-20'
            ],
            [
                'Robert Johnson',
                'robert.johnson@example.com',
                'REG2024003',
                $departments[2] ?? 'Electronics and Communication',
                'B.E Electronics',
                '9876543212',
                '2000-03-25'
            ],
            [
                'Sarah Wilson',
                'sarah.wilson@example.com',
                'REG2024004',
                $departments[3] ?? 'Mechanical Engineering',
                'B.E Mechanical',
                '9876543213',
                '2000-04-30'
            ],
            [
                'Michael Brown',
                'michael.brown@example.com',
                'REG2024005',
                $departments[4] ?? 'Business Administration',
                'BBA',
                '9876543214',
                '2000-05-10'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Name *',
            'Email *',
            'Registration Number *',
            'Department *',
            'Programme *',
            'Phone',
            'DOB (YYYY-MM-DD)'
        ];
    }

    public function title(): string
    {
        return 'Student Import Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style for example data rows
        $sheet->getStyle('A2:G6')->applyFromArray([
            'font' => [
                'color' => ['rgb' => '333333'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6F0FA'],
            ],
        ]);

        // Add borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(18);

        // Add instructions sheet
        $spreadsheet = $sheet->getParent();
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        
        $instructionsSheet->setCellValue('A1', 'Student Import Instructions');
        $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $departments = Department::pluck('name')->toArray();
        $row = 3;
        
        $instructionsSheet->setCellValue('A' . $row, 'REQUIRED FIELDS:');
        $instructionsSheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row += 2;
        
        $instructionsSheet->setCellValue('A' . $row, '1. Name - Student\'s full name (max 100 characters)');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '2. Email - Valid email address (must be unique in system)');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '3. Registration Number - Unique registration number (max 50 characters)');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '4. Department - Must exactly match one of the following departments:');
        $row++;
        
        foreach ($departments as $dept) {
            $instructionsSheet->setCellValue('A' . $row, '   • ' . $dept);
            $row++;
        }
        
        $instructionsSheet->setCellValue('A' . $row, '5. Programme - Must belong to the selected department');
        $row += 2;
        
        $instructionsSheet->setCellValue('A' . $row, 'OPTIONAL FIELDS:');
        $instructionsSheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row += 2;
        
        $instructionsSheet->setCellValue('A' . $row, '6. Phone - Contact number (max 20 characters)');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '7. DOB - Date of birth in YYYY-MM-DD format');
        $row += 2;
        
        $instructionsSheet->setCellValue('A' . $row, 'IMPORTANT NOTES:');
        $instructionsSheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row += 2;
        
        $instructionsSheet->setCellValue('A' . $row, '• Do not modify the column headers');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '• Email and Registration Number must be unique across all students');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '• Department names are case-sensitive - use exact names from the list above');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '• You can delete the example rows before uploading');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '• The import will validate all rows before inserting');
        $row++;
        $instructionsSheet->setCellValue('A' . $row, '• Invalid rows will be skipped with error messages');
        
        $instructionsSheet->getColumnDimension('A')->setWidth(60);
        $instructionsSheet->getStyle('A1:A' . $row)->getAlignment()->setWrapText(true);
        
        // Set active sheet back to first sheet
        $spreadsheet->setActiveSheetIndex(0);
        
        return $sheet;
    }
}