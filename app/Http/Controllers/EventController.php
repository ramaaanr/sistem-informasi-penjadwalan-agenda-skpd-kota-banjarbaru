<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
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
                'end_event' => "",
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
                'end_event' => "",
            ]);
            Session::flash('success', 'Data Berhasil Diupdate');
        } catch (QueryException $th) {
            Session::flash('error', 'Data Gagal Diupdate: ' + $th);
        }

        return redirect()->back();
    }
}
