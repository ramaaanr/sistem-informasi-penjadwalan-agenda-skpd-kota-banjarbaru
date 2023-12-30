<?php


namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Whatsapp;
use Carbon\Carbon;
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

    public function getFormatedEvents(Request $request)
    {

        try {
            // $results = array();
            // foreach ($contacts as $contact) {
            //     $results[] = [
            //         'no_whatsapp' => $contact,
            //         'acara' => $events,
            //     ];
            // }

            $contacts = Whatsapp::pluck('nomor_telepon')->toArray();
            $tanggal = $request->input('tanggal');
            $headerContent = "Assalamualaikum W. W.\nMohon izin Bapak Ibu Asisten, Staf Ahli Wali kota, Pimpinan SKPD, Sekretaris DPRD, Para Kabag, Camat dan Lurah. Izin info giat pimpinan dan arahan Wali Kota Banjarbaru.\n";
            $footerContent = "\nTerima kasih.\nWassalamualaikum W. W.";
            $mainContent = "";
            $events = Event::whereDate('start_event', $tanggal)->get();
            $date = Carbon::parse($events[0]->start_event);
            $date->locale('id');
            $formatedDate = $date->isoFormat('dddd, D MMMM YYYY');
            $mainContent  = "$formatedDate.\n\n";
            foreach ($events as $index => $event) {
                $eventDate = Carbon::parse($event->start_event);
                $eventDate->locale('id');
                $formatedTime = $eventDate->isoFormat('h:m');
                $number = $index + 1;
                $mainContent = $mainContent . "\n$number. Acara *$event->title*\n- Pukul: $formatedTime WITA\n- Dihadiri: *$event->dihadiri*\n- Pakaian: $event->pakaian\n- Keterangan: $event->keterangan\n";
            }

            $dataResults = [
                'wa_numbers' => $contacts,
                'content' => $headerContent . $mainContent . $footerContent,
            ];
            return response()->json($dataResults);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getLine()], 500);
        }
    }
}
