<?php

namespace Mailmerge\Http\Controllers;

use Mailmerge\Batch;

class BatchSettingsController
{
    public function index()
    {
        $batches = Batch::all();

        return view('settings', compact('batches'));
    }
}