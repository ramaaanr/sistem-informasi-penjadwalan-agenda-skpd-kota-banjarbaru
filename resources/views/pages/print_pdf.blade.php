@extends('layouts.user')

@section('content')
<main id="main-content" class="bg-gray-100 ml-56 p-16 pt-8 min-h-screen">
  <div class="card-date-range rounded-md bg-white shadow-lg my-4 p-8">
    <div class="input-date-container flex">
      <div class="relative mr-4  w-full">
        <input type="date" id="mulaiTanggal"
          class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
          name="mulai_tanggal" required>
        <label for="floating_keterangan"
          class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Mulai
          Tanggal
        </label>
      </div>

      <div class="relative w-full">
        <input type="date" id="sampaiTanggal"
          class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
          name="sampai_tanggal" required>
        <label for="floating_keterangan"
          class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Sampai
          Tanggal
        </label>
      </div>
    </div>
    <div class="button-container mt-4">
      <button type="button" id="lihatAcaraButton"
        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Lihat
        Acara</button>
      <button type="button" id="cetakAcaraButton"
        class="text-white hidden bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak
        Acara</button>
    </div>
  </div>
  <div class="card-table-events rounded-md border  bg-white shadow-lg my-4 p-8">
    <div class="relative overflow-x-auto border border-gray-300 shadow-md sm:rounded-lg mt-4 ">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500  dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr class>
            <th scope="col" class="px-6 py-3">
              Tanggal
            </th>
            <th scope="col" class="px-6 py-3">
              Acara
            </th>
            <th scope="col" class="px-6 py-3">
              Detail
            </th>
          </tr>
        </thead>
        <tbody class="table-body-event">

        </tbody>
      </table>
    </div>
  </div>
</main>
<script>
function createDetailButton(eventId) {
  let button = $('<button>', {
    text: 'Detail',
    class: 'text-blue-700 mt-2 mr-2 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2 py-1 text-center ',
    click: function() {
      // Ajax GET request untuk mendapatkan detail event
      $.ajax({
        url: 'detail-event',
        type: 'GET',
        data: {
          id: eventId
        },
        success: function(event) {
          // Handle data dari detail event
          console.info(event);
          Swal.fire({
            icon: 'info',
            title: 'Event Details',
            html: '<strong>Title:</strong> ' + event.title +
              '<br>' +
              '<strong>Tempat:</strong> ' + event.tempat +
              '<br>' +
              '<strong>Dihadiri:</strong> ' + event
              .dihadiri + '<br>' +
              '<strong>Pakaian:</strong> ' + event
              .pakaian + '<br>' +
              '<strong>Tanggal:</strong> ' + event.tanggal + '<br>' +
              '<strong>Waktu:</strong> ' + event.waktu + '<br>' +
              '<strong>Keterangan:</strong> ' + event.keterangan,
            confirmButtonText: 'OK'
          });
        },
        error: function(xhr, status, error) {
          console.error('Terjadi kesalahan: ' + error);
        }
      });
    }
  });
  return button;
}

$('#lihatAcaraButton').on('click', () => {
  let mulai_tanggal = $('#mulaiTanggal').val();
  let sampai_tanggal = $('#sampaiTanggal').val();
  $('#cetakAcaraButton').addClass('hidden');
  $.ajax({
    url: 'events-by-data-range',
    type: 'GET',
    data: {
      mulai_tanggal: mulai_tanggal,
      sampai_tanggal: sampai_tanggal,
    },
    success: (events) => {
      Swal.fire({
        icon: 'success',
        title: 'Lihat Acara Berdasarkan Tanggal Berhasil',
      });
      $('#cetakAcaraButton').addClass('hidden');
      if (events.length != 0) $('#cetakAcaraButton').removeClass('hidden');

      $('.table-body-event').empty();
      $.each(events, function(index, event) {
        let row = $('<tr>').addClass(
          'bg-white border-t border-gray-300 hover:bg-gray-100'
        );
        $('<td>').addClass('px-6 py-4').text(event.tanggal)
          .appendTo(row);
        $('<td>').addClass('px-6 py-4').text(event.title)
          .appendTo(row);
        row.append(createDetailButton(event.id));

        $('.table-body-event').append(row);
      });
    },
    error: (xhr, status, error) => {
      Swal.fire({
        icon: 'error',
        title: 'Lihat Acara Berdasarkan Tanggal Gagal',
        text: xhr.responseJSON.message,
      });
    },
  });
});
$('#cetakAcaraButton').on('click', () => {
  let mulai_tanggal = $('#mulaiTanggal').val();
  let sampai_tanggal = $('#sampaiTanggal').val();
  $.ajax({
    url: 'events-by-data-range',
    type: 'GET',
    data: {
      mulai_tanggal: mulai_tanggal,
      sampai_tanggal: sampai_tanggal,
    },
    success: (events) => {
      Swal.fire({
        icon: 'success',
        title: 'Cetak Acara Berdasarkan Tanggal Berhasil',
      })
    },
    error: (xhr, status, error) => {
      Swal.fire({
        icon: 'error',
        title: 'Lihat Acara Berdasarkan Tanggal Gagal',
        text: xhr.responseJSON.message,
      });
    },
  });
});
</script>
@endsection