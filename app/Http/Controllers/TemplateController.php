<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class TemplateController extends Controller
{
    public function downloadTemplate()
    {
        $filePath = public_path('assets/template/Template_Excel.xlsx');

        if (!file_exists($filePath)) {
            return abort(404, 'Template file not found.');
        }

        return Response::download($filePath, 'Template_Excel.xlsx');
    }
}

