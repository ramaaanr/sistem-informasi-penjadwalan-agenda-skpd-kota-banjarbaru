<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public function showCalendar(Request $request)
    {
        $events = Event::select(['title', 'start_event', 'end_event'])->get();
        $results = array();
        foreach ($events as $event) {
            $results[] = [
                'title' => $event->title,
                'start' => $event->start_event,
                'end' => $event->end_event,
            ];
        }
        return view('pages.index', ['events' => $results]);
    }

    public function getEventsByDate(Request $request)
    {
        $date = $request->input('date');

        // Query database untuk mendapatkan acara berdasarkan tanggal
        $events = Event::whereDate('start_event', $date)->get();
        $results = array();
        foreach ($events as $event) {
            $tanggal = Carbon::parse($event->start_event);
            $tanggal->locale('id');
            $results[] = [
                'id' => $event->id,
                'title' => $event->title,
                'tempat' => $event->tempat,
                'tanggal' => $tanggal->isoFormat('dddd, D MMMM YYYY'),
                'waktu' => $tanggal->isoFormat('h:m'),
            ];
        }

        // Kembalikan data acara dalam format JSON
        return response()->json($results);
    }

    public function getDetailEvent(Request $request)
    {
        $id = $request->input('id');
        // Lakukan logika untuk mengambil detail event dari database atau sumber lainnya
        // Contoh sederhana: hanya mengembalikan ID event
        $detailEvent = Event::find($id);
        $tanggal = Carbon::parse($detailEvent->start_event);
        $tanggal->locale('id');
        return response()->json([
            'id' => $detailEvent->id,
            'title' => $detailEvent->title,
            'tempat' => $detailEvent->tempat,
            'dihadiri' => $detailEvent->dihadiri,
            'pakaian' => $detailEvent->pakaian,
            'keterangan' => $detailEvent->keterangan,
            'tanggal' => $tanggal->isoFormat('dddd, D MMMM YYYY'),
            'waktu' => $tanggal->isoFormat('h:m'),
        ]);
    }

    public function showFormAddEvent(Request $request)
    {
        return view('pages.store_event');
    }

    public function storeEvent(Request $request)
    {
        try {
            Event::create([
                'title' => $request->input('title'),
                'tempat' => $request->input('tempat'),
                'dihadiri' => $request->input('dihadiri'),
                'pakaian' => $request->input('pakaian'),
                'keterangan' => $request->input('keterangan'),
                'start_event' => $request->input('start_event'),
            ]);
            Session::flash('success', 'Data Berhasil Dimasukkan');
        } catch (QueryException $th) {
            Session::flash('error', 'Data Gagal Dimasukkan: ' + $th);
        }

        return redirect()->back();
    }
    public function showFormEditEvent(Request $request)
    {
        $id = $request->query('id');

        try {
            $event = Event::find($id);
            if ($event) {
                return view('pages.edit_event', [
                    'id' => $id,
                    'title' => $event->title,
                    'tempat' => $event->tempat,
                    'dihadiri' => $event->dihadiri,
                    'pakaian' => $event->pakaian,
                    'keterangan' => $event->keterangan,
                    'start_event' => $event->start_event,
                ]);
            } else {
                abort(404);
            }
        } catch (QueryException $th) {
            abort(404);
        }
    }

    public function editEvent(Request $request)
    {
        $id = $request->query('id');

        try {
            Event::where('id', $id)->update([
                'title' => $request->input('title'),
                'tempat' => $request->input('tempat'),
                'dihadiri' => $request->input('dihadiri'),
                'pakaian' => $request->input('pakaian'),
                'keterangan' => $request->input('keterangan'),
                'start_event' => $request->input('start_event'),
            ]);
            Session::flash('success', 'Data Berhasil Diupdate');
        } catch (QueryException $th) {
            Session::flash('error', 'Data Gagal Diupdate: ' + $th);
        }

        return redirect()->back();
    }
    public function deleteEvent(Request $request)
    {
        $id = $request->query('id');

        Event::find($id)->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function showPrintPdf(Request $request)
    {
        return view('pages.print_pdf');
    }

    public function getEventByDateRange(Request $request)
    {
        try {
            // Mengonversi string tanggal menjadi objek Carbon
            $mulaiTanggal = Carbon::createFromFormat('Y-m-d', $request->input('mulai_tanggal'));
            $sampaiTanggal = Carbon::createFromFormat('Y-m-d', $request->input('sampai_tanggal'));

            // Memeriksa apakah format tanggal valid
            if ($mulaiTanggal === false || $sampaiTanggal === false) {
                return response()->json(['error' => true, 'message' => "Input Tanggal Belum Lengkap"], 400); // Format tanggal tidak valid
            }

            // Memeriksa apakah mulaiTanggal lebih dari sampaiTanggal
            if ($mulaiTanggal->gt($sampaiTanggal)) {
                // Tanggal awal lebih dari tanggal akhir
                return response()->json(['error' => true, 'message' => "Input Sampai Tanggal harus sebelum Input Mulai Tanggal"], 400); // Format tanggal tidak valid
            }

            $events = Event::whereBetween('start_event', [$mulaiTanggal, $sampaiTanggal])->get();
            $result = array();
            foreach ($events as $event) {
                $tanggal = Carbon::parse($event->start_event);
                $tanggal->locale('id');
                $result[] = [
                    'id' => $event->id,
                    'tanggal' => $event->tanggal,
                    'tanggal' => $tanggal->isoFormat('dddd, D MMMM YYYY'),
                    'title' => $event->title,
                ];
            }
            return response()->json($result); // Tanggal valid dan urutan benar

        } catch (InvalidFormatException $th) {
            return response()->json(['error' => true, 'message' => "Input Tanggal Belum Lengkap"], 400); // Format tanggal tidak valid

        }
    }

    public function downloadPdf(Request $request)
    {
    }
}
