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
            if (is_string($file)) {
                $doc = Document::where("path", $file)->where("is_draft", 1)->first();
                $doc->project_id = $project->id;
                $doc->is_draft = 0;
                $doc->save();
                continue;
            }
            $finalPath = 'uploads/docs/' . $file->getFilename();

            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );

            Document::create([
                'project_id' => $project->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $finalPath,
                'download_count' => 0,
            ]);

            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return;
    }

    private function handleDocumentUpdates(array $newDocuments, Project $project): void
    {
        $existingDocuments = $project->documents->pluck('filename')->toArray();

        $deletedDocuments = array_diff($existingDocuments, $newDocuments);

        foreach ($deletedDocuments as $deletedDocument) {
            Storage::disk('public')->delete($deletedDocument);
            Document::where('filename', $deletedDocument)->where('project_id', $project->id)->delete();
        }

        $this->moveFiles($newDocuments, $project);
    }


    public function moveForDraft(array $files, array $old_docs): array
    {
        if ($old_docs != null) {
            $deletedDocuments = array_diff($old_docs, $files);

            foreach ($deletedDocuments as $deletedDocument) {
                Storage::disk('public')->delete($deletedDocument);
                Document::where('filename', $deletedDocument)->delete();
            }
        }

        $movedFiles = [];
        foreach ($files as $file) {
            if (is_string($file)) {
                $doc = Document::where("path", $file)->first();
                if ($doc->is_draft == 0) {
                    $newPath = $this->generateNewPath($doc->path);
                    Document::create([
                        'project_id' => null,
                        'filename' => $doc->filename,
                        'download_count' => 0,
                        'is_draft' => 1,
                        'path' => $newPath,
                    ]);

                    Storage::copy($doc->path, $newPath);
                    $movedFiles[] = $newPath;
                } else {
                    $movedFiles[] = $doc->path;
                }
                continue;
            }
            $finalPath = 'uploads/docs/' . $file->getFilename();

            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );

            Document::create([
                'project_id' => null,
                'filename' => $file->getClientOriginalName(),
                'path' => $finalPath,
                'download_count' => 0,
                'is_draft' => 1
            ]);

            $movedFiles[] = $finalPath;

            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return $movedFiles;
    }

    private function generateNewPath($originalPath)
    {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_duplicate_' . time() . '.' . $pathInfo['extension'];
    }

}
