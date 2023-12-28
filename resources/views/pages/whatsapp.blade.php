@extends('layouts.user')

@section('content')
<main id="main-content" class="bg-gray-100 ml-56 p-4 min-h-screen">
  <div
    class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <form class="space-y-6" id="form-whatsapp" action="#">
      <h5 class="text-xl font-medium text-gray-900 dark:text-white">Masukkan Nomor Whatsapp</h5>
      <div>
        <input type="text" name="nomor_telepon" id="nomor_telepon"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
          placeholder="Masukkan No.Whatsapp" required>
      </div>
      <div class="flex space-x-4">
        <button type="button" onclick="simpanNomor()"
          class="flex-1 w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
        <button type="button" onclick="batalSimpan()"
          class="flex-1 w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Batal</button>
      </div>
    </form>
  </div>

  <div class="card-table-events rounded-md border bg-white shadow-lg my-4 p-8">
    <div class="relative overflow-x-auto border border-gray-300 shadow-md sm:rounded-lg mt-4 ">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              No
            </th>
            <th scope="col" class="px-6 py-3">
              No Telepon
            </th>
            <th scope="col" class="px-6 py-3">
              Aksi
            </th>
          </tr>
        </thead>
        <tbody class="table-body-event">
          @foreach ($nomors as $nomor)
          <tr class="bg-white border-t border-gray-300 hover:bg-gray-100">
            <td class="px-6 py-4">{{ $loop->iteration }}</td>
            <td class="px-6 py-4">{{ $nomor->nomor_telepon }}</td>
            <td class="px-6 py-4">
              <form action="{{ route('whatsapp.destroy', $nomor) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
function appendTableRow(nomor) {
  let tableBody = $('.table-body-event');
  let row = $('<tr>').addClass(
    'bg-white border-t border-gray-300 hover:bg-gray-100'
  );
  $('<td>').addClass('px-6 py-4').text(tableBody.children().length + 1)
    .appendTo(row);
  $('<td>').addClass('px-6 py-4').text(nomor)
    .appendTo(row);
  row.append(createHapusButton());
  tableBody.append(row);
}

function simpanNomor() {
  let nomor = $('#nomor_telepon').val();
  if (nomor.trim() !== '') {
    appendTableRow(nomor);
    // Clear input setelah simpan
    $('#nomor_telepon').val('');
    // Mendapatkan token CSRF secara eksplisit
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Kirim data ke server dengan menyertakan token CSRF
    $.ajax({
      url: '/whatsapp',
      type: 'POST',
      data: {
        nomor_telepon: nomor,
        _token: csrfToken, // Menambahkan token CSRF ke data
      },
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Nomor berhasil disimpan.',
        });

        // Ambil nomor dari server dan tampilkan di tabel
        $.ajax({
          url: '/api/whatsapp',
          type: 'GET',
          success: function(data) {
            $('.table-body-event').empty();
            $.each(data, function(index, item) {
              appendTableRow(index + 1, item.nomor_telepon);
            });
          },
          error: function(xhr, status, error) {
            console.error('Gagal mengambil data nomor: ' + error);
          }
        });

        // Clear input setelah simpan
        $('#nomor_telepon').val('');
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal menyimpan nomor.',
          text: xhr.responseJSON.message,
        });
      },
    });
  }
}

function batalSimpan() {
  // Clear input jika batal
  $('#nomor_telepon').val('');
}

function createHapusButton() {
  let button = $('<button>', {
    text: 'Hapus',
    class: 'text-red-700 mt-2 mr-2 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-1 text-center ',
    click: function() {
      // Hapus baris saat tombol Hapus ditekan
      $(this).closest('tr').remove();
    }
  });
  return button;
}
</script>



@endsection