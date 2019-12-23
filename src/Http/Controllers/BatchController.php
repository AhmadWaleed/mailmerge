<?php

namespace MailMerge\Http\Controllers;

use MailMerge\Batch;

class BatchController
{
	public function index()
    {
        $batches = Batch::all();

        return view('mailmerge::batches', compact('batches'));
    }
}
