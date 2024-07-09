<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeneratePancasilaRaport;

class P5BKPancasilaRaportController extends Controller
{
    public function previewPancasilaRaport(Request $request)
    {
        $livewire = json_decode($request->input('livewire'), true);
        $data = json_decode($request->input('data'), true);

        return GeneratePancasilaRaport::make($livewire, $data);
    }
}
