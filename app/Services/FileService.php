<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function moveFiles(array $files, Project $project): void
    {
        foreach ($files as $file) {
            $finalPath = 'uploads/docs/' . $file->getFilename();

            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );

            Document::create([
                'project_id' => $project->id,
                'title' => $file->getClientOriginalName(),
                'filename' => $finalPath,
                'download_count' => 0,
            ]);

            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return;
    }

    public function moveForDraft(array $files): array
    {
        $movedFiles = [];
        foreach ($files as $file) {
            if (is_string($file)) {
                $doc = Document::where("filename", $file)->first();
                $movedFiles[] = [
                    'name' => $doc->title,
                    'path' => $file
                ];
                continue;
            }
            $finalPath = 'uploads/docs/' . $file->getFilename();

            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );
            $name = $file->getClientOriginalName();
            $movedFiles[] = [
                'name' => $name,
                'path' => $finalPath
            ];

            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return $movedFiles;
    }

}
