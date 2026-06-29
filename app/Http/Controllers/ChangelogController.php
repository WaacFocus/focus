<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use League\CommonMark\CommonMarkConverter;

class ChangelogController extends Controller
{
    private function markdown(): string
    {
        $local = base_path('CHANGELOG.md');

        if (file_exists($local)) {
            return file_get_contents($local);
        }

        $response = \Illuminate\Support\Facades\Http::timeout(5)
            ->get('https://raw.githubusercontent.com/WaacFocus/focus/master/CHANGELOG.md');

        return $response->successful() ? $response->body() : '# Changelog unavailable';
    }

    private function toHtml(): string
    {
        $converter = new CommonMarkConverter([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
        return $converter->convert($this->markdown())->getContent();
    }

    public function index()
    {
        $html = $this->toHtml();
        return view('admin.changelog', compact('html'));
    }

    public function pdf()
    {
        $html     = $this->toHtml();
        $filename = 'focus-changelog-' . now()->format('Y-m-d') . '.pdf';
        $pdf      = Pdf::loadView('admin.changelog-pdf', compact('html'))->setPaper('A4', 'portrait');
        return $pdf->download($filename);
    }

    public function download()
    {
        $content = $this->markdown();

        return response($content, 200, [
            'Content-Type'        => 'text/markdown',
            'Content-Disposition' => 'attachment; filename="focus-changelog.md"',
        ]);
    }
}
