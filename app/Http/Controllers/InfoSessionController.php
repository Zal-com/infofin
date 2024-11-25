<?php

namespace App\Http\Controllers;

use App\Models\InfoSession;
use Illuminate\Http\Request;

class InfoSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('info_session.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('info_session.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $info_session = InfoSession::find($id);

        return view('info_session.show', ['info_session' => $info_session]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $info_session = InfoSession::find($id);
        return view('info_session.edit', compact('info_session'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InfoSession $infoSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InfoSession $infoSession)
    {
        //
    }
}
