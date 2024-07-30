<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    /**
     * Fetch the image from the public directory and save it to storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImage()
    {
        // Define the path to the image in the public directory
        $publicPath = public_path('img/ulb_logo.png');

        // Check if the file exists in the public directory
        if (!File::exists($publicPath)) {
            abort(404, 'Image not found.');
        }

        // Get the file's content
        $fileContent = File::get($publicPath);

        // Define the path to save the image in the storage directory
        $storagePath = 'public/ulb_logo.png';

        // Save the file to the storage directory
        Storage::put($storagePath, $fileContent);

        // Return a success message
        return response()->json(['message' => 'Image successfully saved to storage.'], 200);
    }
}
