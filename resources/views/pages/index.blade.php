@extends('layouts.index')

@section('content')
@include('components.navbar')
@include('components.sidebar')
<main id="main-content" class="bg-gray-100 ml-56  min-h-screen">
  Test
</main>
<script>
  $(document).ready(function() {
    // Toggle sidebar on button click
    $('#sidebarToggle').click(function() {
      $('#sidebar').toggleClass('-translate-x-56');
    });

    // Hide sidebar on tablet and smaller screens
    $(window).resize(function() {
      if ($(window).width() <= 768) {
        $('#main-content').removeClass('ml-56')
        $('#sidebar').addClass('-translate-x-56');
        $('#sidebarToggle').removeClass('hidden');
      } else {
        $('#main-content').addClass('ml-56')
        $('#sidebar').removeClass('-translate-x-56');
        $('#sidebarToggle').addClass('hidden');
      }
    });


    $(document).on('click', function(event) {
      // Cek apakah elemen yang diklik bukan bagian dari sidebar atau toggleButton
      if (!$(event.target).closest('#sidebar, #sidebarToggle').length) {
        // Tutup sidebar jika diklik di luar

        if ($(window).width() <= 768) {
          $('#sidebar').addClass('-translate-x-56');
        }
      }
    });



    // Trigger resize event on page load
    $(window).resize();
  });
</script>
@endsection