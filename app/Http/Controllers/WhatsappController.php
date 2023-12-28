<?php


namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Whatsapp;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class WhatsappController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listNomor = Whatsapp::all();
        return view('pages.whatsapp', ['listNomor' => $listNomor]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nomor_telepon' => ['required', 'regex:/^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/'],
            ]);
            Whatsapp::create([
                'nomor_telepon' => $request->input('nomor_telepon'),
            ]);
            Session::flash('success', ['title' => 'Tambah Kontak', 'message' => 'Data Berhasil Dimasukkan']);
        } catch (ValidationException $th) {
            Session::flash('error', ['title' => 'Tambah Kontak', 'message' => "Format Nomor Tidak Sesuai"]);
        } catch (QueryException $th) {
            Session::flash('error', ['title' => 'Tambah Kontak', 'message' => 'Data Gagal Dimasukkan']);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->input('nomor_id');
            Whatsapp::find($id)->delete();
            Session::flash('success', ['title' => 'Hapus Kontak', 'message' => 'Data Berhasil Dihapus']);
        } catch (QueryException $th) {
            Session::flash('error', ['title' => 'Hapus Kontak', 'message' => 'Data Gagal Dihapus']);
        }

        return redirect()->back();
    }

    public function sendEventToWhatsapp(Request $request)
    {
        $tanggal = $request->input('tanggal');
        try {
            $events = Event::whereDate('start_event', $tanggal)->get();
            $contacts = Whatsapp::all();
            $results = array();
            foreach ($contacts as $contact) {
                $results[] = [
                    'no_whatsapp' => $contact,
                    'acara' => $events,
                ];
            }

            return response()->json($results);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getMessage()], 500);
        }
    }
}
