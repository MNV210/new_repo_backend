<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Question;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExcelController extends Controller
{
    public function import(Request $request)
    {
        // Kiểm tra xem file có được upload không
        if ($request->hasFile('files')) {
            $file = $request->file('files');

            // Đọc file Excel
            $spreadsheet = IOFactory::load($file->getRealPath());

            // Lấy sheet đầu tiên
            $sheet = $spreadsheet->getActiveSheet();

            // Bỏ qua dòng tiêu đề (nếu có)
            $rows = $sheet->toArray();
            array_shift($rows); // Bỏ qua dòng đầu tiên (tiêu đề)

            // Lặp qua các dòng trong sheet
            foreach ($rows as $row) {
                // Tạo một bản ghi mới trong model Question
                Question::create([
                    'question' => $row[0], // Thay 'column1' bằng tên cột trong bảng của bạn
                    'option1' => $row[1],
                    'option2' => $row[2], // Thay 'column2' bằng tên cột trong bảng của bạn
                    'option3' => $row[3],
                    'option4' => $row[4],
                    'correct_answer' => $row[5],
                    'quiz_id' => $request->quiz_id
                ]);
            }

            return response()->json(['success' => 'Import thành công!'], 200);
        }

        return response()->json(['error' => 'Vui lòng chọn file để import.'], 400);
    }

    public function export($exercise_id)
    {
        try {
            // Fetch questions from the database
            $questions = Question::where('exercise_id', $exercise_id)->get();
            $exercise = Exercise::findOrFail($exercise_id);

            // Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Merge cells for the title in row 1 (from column A to column G)
            // $sheet->mergeCells('A1:F1');
            // // Set the title "Danh sách câu hỏi" in the merged cell
            // $sheet->setCellValue('A1', "Danh sách câu hỏi bài kiêm tra: $exercise->title" );

            // // Style the title (optional)
            // $sheet->getStyle('A1')->getFont()->setBold(true); // Make the title bold
            // $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center align the title

            // Add header row
            $sheet->setCellValue('A1', 'Câu hỏi');
            $sheet->setCellValue('B1', 'Đáp án 1');
            $sheet->setCellValue('C1', 'Đáp án 2');
            $sheet->setCellValue('D1', 'Đáp án 3');
            $sheet->setCellValue('E1', 'Đáp án 4');
            $sheet->setCellValue('F1', 'Đáp án đúng');
            // $sheet->setCellValue('G1', 'Exercise ID');

            // Add data rows
            $rowNumber = 2;
            foreach ($questions as $question) {
                $sheet->setCellValue('A' . $rowNumber, $question->question_text);
                $sheet->setCellValue('B' . $rowNumber, $question->option_1);
                $sheet->setCellValue('C' . $rowNumber, $question->option_2);
                $sheet->setCellValue('D' . $rowNumber, $question->option_3);
                $sheet->setCellValue('E' . $rowNumber, $question->option_4);
                $sheet->setCellValue('F' . $rowNumber, $question->is_correct);
                // $sheet->setCellValue('G' . $rowNumber, $question->exercise_id);
                $rowNumber++;
            }

            // Create a writer object
            $writer = new Xlsx($spreadsheet);

            // Create a StreamedResponse to stream the file to the client
            $response = new StreamedResponse(
                function() use ($writer) {
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="exercise_' . $exercise_id . '_questions.xlsx"',
                ]
            );

            return $response;

        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                'error' => 'An error occurred while exporting the data: ' . $e->getMessage()
            ], 500);
        }
    }
}
