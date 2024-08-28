<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function moveFiles(array $files, Project $project): void
    {
        foreach ($files as $file) {
            // Si le fichier est déjà un chemin (donc une chaîne de caractères)
            if (is_string($file)) {
                $doc = Document::where("path", $file)->where("is_draft", 1)->first();
                if ($doc) {
                    $doc->project_id = $project->id;
                    $doc->is_draft = 0;
                    $doc->save();
                }
                continue;
            }

            // Générer une chaîne aléatoire pour le sous-dossier
            $randomDir = Str::random(10);
            $finalPath = "uploads/docs/{$randomDir}/" . $file->getClientOriginalName();

            // Enregistrer le fichier
            Storage::disk('public')->putFileAs(
                "uploads/docs/{$randomDir}",
                $file,
                $file->getClientOriginalName()
            );

            // Créer l'enregistrement du document
            Document::create([
                'project_id' => $project->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $finalPath,
                'download_count' => 0,
            ]);

            // Supprimer le fichier temporaire
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }
    }

    public function handleDocumentUpdates(array $newDocuments, Project $project): void
    {
        $existingDocuments = $project->documents->pluck('path')->toArray();

        $deletedDocuments = array_diff($existingDocuments, $newDocuments);

        foreach ($deletedDocuments as $deletedDocument) {
            Storage::disk('public')->delete($deletedDocument);
            Document::where('path', $deletedDocument)->where('project_id', $project->id)->delete();
            $directory = dirname($deletedDocument);
            if (Storage::disk('public')->exists($directory)) {
                $filesInDirectory = Storage::disk('public')->files($directory);
                if (empty($filesInDirectory)) {
                    Storage::disk('public')->deleteDirectory($directory);
                }
            }
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
        
                $directory = dirname($deletedDocument);
        
                if (Storage::disk('public')->exists($directory)) {
                    $filesInDirectory = Storage::disk('public')->files($directory);
        
                    if (empty($filesInDirectory)) {
                        Storage::disk('public')->deleteDirectory($directory);
                    }
                }
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

            // Générer une chaîne aléatoire pour le sous-dossier
            $randomDir = Str::random(10);
            $finalPath = "uploads/docs/{$randomDir}/" . $file->getClientOriginalName();

            // Enregistrer le fichier
            Storage::disk('public')->putFileAs(
                "uploads/docs/{$randomDir}",
                $file,
                $file->getClientOriginalName()
            );

            Document::create([
                'project_id' => null,
                'filename' => $file->getClientOriginalName(),
                'path' => $finalPath,
                'download_count' => 0,
                'is_draft' => 1
            ]);

            $movedFiles[] = $finalPath;

            // Supprimer le fichier temporaire
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return $movedFiles;
    }

    private function generateNewPath($originalPath)
    {
        $pathInfo = pathinfo($originalPath);
        $randomDir = Str::random(10); // Générer un nouveau dossier aléatoire
        return $pathInfo['dirname'] . '/' . $randomDir . '/' . $pathInfo['basename'];
    }
}
