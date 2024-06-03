<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeCSVController extends Controller
{
    public function importCsv(Request $request)
    {
        $file = $request->file('csv_file');
        $file_path = $file->store('csv');
        $file = new \SplFileObject(storage_path('app/' . $file_path));
        $file->setFlags(\SplFileObject::READ_CSV);
        $company_id = $request->company_id;
        $data = [];
        foreach ($file as $row) {
            $data[] = ['company_id' => $company_id, 'email' => $row[0]];
        }
        foreach ($data as $row) {
            $employee = new Employee();
            $employee->fill($row);
            $employee->save();
        }
        return response()->json(['message' => 'CSVのインポートが完了しました']);
    }

    public function downloadCsvTemplate()
    {
        $csv_header = ['email'];
        $csv_data = implode(',', $csv_header) . "\n";
        return response($csv_data, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template.csv"');
    }
}
