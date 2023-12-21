@extends('layouts.user')

@section('content')
<main id="main-content" class="bg-gray-100 ml-56 p-4 min-h-screen">
  <div class="" id="calendar"></div>
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr class>
          <th scope="col" class="px-6 py-3">
            Acara
          </th>
          <th scope="col" class="px-6 py-3">
            Tempat
          </th>
          <th scope="col" class="px-6 py-3">
            Tanggal
          </th>
          <th scope="col" class="px-6 py-3">
            Waktu
          </th>
          <th scope="col" class="px-6 py-3 col-span-3">
            Aksi
          </th>
        </tr>
      </thead>
      <tbody class="table-body-event">

      </tbody>
    </table>
  </div>

</main>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
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

  function createEditButton(eventId) {
    let button = $('<button>', {
      text: 'Edit',
      class: 'text-green-700 mt-2 mr-2 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-2 py-1 text-center ',
      click: function() {
        // Ajax GET request untuk mendapatkan detail event
        window.location.href = "/edit-event?id=" + eventId;
      }
    });
    return button;
  }

  function createDeleteButton(eventId) {
    let button = $('<button>', {
      text: 'Hapus',
      class: 'text-red-700 mt-2 mr-2 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-1 text-center ',
      click: function() {
        // Ajax GET request untuk mendapatkan detail event
        Swal.fire({
          icon: 'warning',
          title: 'Hapus Data',
          text: 'Apakah Anda Yaking Ingin Menghapus Acara Ini?',
          showConfirmButton: true,
          showCancelButton: true,
        }).then((result) => {
          if (result.isConfirmed) {
            // Jika tombol OK diklik, arahkan ke halaman utama
            $.ajax({
              type: 'DELETE',
              url: '/delete-event?id=' + eventId, // Sesuaikan dengan URL dan parameter yang sesuai
              data: {
                _token: '{{ csrf_token() }}', // Sesuaikan dengan cara token CSRF di aplikasi Laravel Anda
              },
              success: function(response) {
                // Handle respons setelah penghapusan berhasil
                Swal.fire({
                  icon: 'success',
                  title: 'Data Dihapus',
                  text: 'Acara telah dihapus.',
                }).then(() => {
                  window.location.href = '/';
                });

              },
              error: function(xhr, status, error) {
                // Handle kesalahan saat melakukan penghapusan
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal Menghapus',
                  text: 'Terjadi kesalahan saat menghapus acara.',
                });
              }
            });
          }
        });
      }
    });
    return button;
  }
  $(document).ready(function() {
    let bookings = @json($events);
    let calendarEl = $('#calendar')[0];
    let calendar = new FullCalendar.Calendar(calendarEl, {
      editable: true,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: bookings,
      dateClick: function(date, jsEvent, view) {
        let clickedDate = date.dateStr;
        $.ajax({
          url: '/events-by-date',
          type: 'GET',
          data: {
            date: clickedDate
          },
          success: function(events) {
            $('.table-body-event').empty();
            $.each(events, function(index, event) {
              let row = $('<tr>').addClass(
                'bg-white border-t border-gray-300 hover:bg-gray-100'
              );
              $('<td>').addClass('px-6 py-4').text(event.title)
                .appendTo(row);
              $('<td>').addClass('px-6 py-4').text(event.tempat)
                .appendTo(row);
              $('<td>').addClass('px-6 py-4').text(event
                .tanggal).appendTo(row);
              $('<td>').addClass('px-6 py-4').text(event
                .waktu).appendTo(row);
              row.append(createDetailButton(event.id));
              row.append(createEditButton(event.id));
              row.append(createDeleteButton(event.id));

              $('.table-body-event').append(row);
            });
            window.scroll({
              top: document.body.scrollHeight,
              behavior: 'smooth'
            });
          },
          error: function(xhr, status, error) {
            alert('Terjadi kesalahan: ' + error);
          }
        });
      }
    });
    calendar.render();
  });
</script>
@endsection