<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Whatsapp;

class WhatsappController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nomors = Whatsapp::all();
        return view('pages.whatsapp', compact('nomors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_telepon' => 'required',
        ]);

        Whatsapp::create([
            'nomor_telepon' => $request->nomor_telepon,
        ]);

        return redirect()->route('whatsapp.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Whatsapp $whatsapp)
    {
        $whatsapp->delete();
        return redirect()->route('whatsapp.index');
    }
}
