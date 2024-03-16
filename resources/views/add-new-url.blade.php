<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Urls') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form id="add-url-form" class="space-y-4">
                    <!-- csrf -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <label for="original_url" class="block text-sm font-medium text-gray-700">URL:</label>
                        <input type="text" id="original_url" name="original_url" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Enter long link here" required>
                        <span class="text-red-500 text-sm" id="original_url_error"></span>
                    </div>

                    <div>
                        <button type="button" id="shorten-url-button" class="app-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md  bg-grey hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Shorten URL
                        </button>
                    </div>

                    <div id="shortened-url" class="hidden mt-4">
                        <label for="short_url" class="block text-sm font-medium text-gray-700">Shortened URL:</label>
                        <a href="" id="short_url" target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline"></a>
                    </div>
                </form>
            </div>
        </div>
        <div id="shortened-url-table">
            {!! $data['urls_table'] !!}
        </div>
    </div>

    <!-- resources/views/popup.blade.php -->

    <div id="popup" class="px-4 py-2 fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center hidden" style="background-color: #33333382;">
        <div class="bg-white p-8 rounded shadow-lg px-4 py-2" style="width: 500px">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Popup Title</h2>
                <button id="closePopup" class="closePopup text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="h-3 w-3 text-lime-600"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />  <line x1="16" y1="5" x2="19" y2="8" /></svg>
                </button>
            </div>
            <div class="text-gray-700  px-4 py-2">
               <!-- Edit URL -->
               <input type="hidden" name="edit_url_id" id="edit_url_id">
                <input type="text" id="edit_original_url" name="edit_original_url" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                <p class="text-red-500 text-sm" id="edit_original_url_error"></p>
                <button type="button" id="edit-url-button" class="app-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md  bg-grey hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save URL
                </button>

            </div>
        </div>
    </div>


    @section('scripts')
        <script>
            $(document).ready(function () {

                $(document).on('click', '.openPopup', function() {
                    $('#popup').removeClass('hidden');
                    var originalUrl = $(this).data('original_url');
                    var urlId = $(this).data('url-id');

                    $('#edit_original_url').val(originalUrl);
                    $('#edit_url_id').val(urlId);
                });

                $(document).on('click', '#edit-url-button', function() {

                    var originalUrl = $('#edit_original_url').val();
                    var urlId = $('#edit_url_id').val();
                    $('#edit_original_url_error').hide();
                    $.ajax({
                        url: '{{ route("edit-url") }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            original_url: originalUrl,
                            url_id: urlId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#shortened-url-table').html(response.urls_table);
                                $('#popup').addClass('hidden');

                            } else {
                                $('#edit_original_url_error').show();
                                $('#edit_original_url_error').text(response.message);
                            }
                        }
                    });
                });


                
                // Close popup when "Close" button or outside the popup is clicked
                $('#closePopup, #popup').click(function(e) {
                    if (e.target === this || $(e.target).is('#closePopup')) {
                        $('#popup').addClass('hidden');
                    }
                });

                $('#shorten-url-button').click(function() {
                    var originalUrl = $('#original_url').val().trim();
                    $.ajax({
                        url: '{{ route("shorten-url") }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            original_url: originalUrl
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#short_url').attr('href', response.shortened_url);
                                $('#short_url').text(response.shortened_url);
                                $('#shortened-url-table').html(response.urls_table);
                                $('#shortened-url').removeClass('hidden');
                            } else {
                                $('#original_url_error').html(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                });

                $(document).on('click', '#deactivate-url-button', function() {
                    var urlId = $(this).data('url-id');

                    $.ajax({
                        url : '{{ route("deactivate-url" ) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            url_id: urlId
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                $('#shortened-url-table').html(response.urls_table);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                });

                
                $(document).on('click', '#activate-url-button', function() {
                    // fetch data-url-id 
                    var urlId = $(this).data('url-id');

                    $.ajax({
                        // pass route with parameter id 
                        url : '{{ route("activate-url" ) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            url_id: urlId
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                console.log(response.urls_table);
                                $('#shortened-url-table').html(response.urls_table);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                });

                
                $(document).on('click', '#delete-url', function() {
                    var urlId = $(this).data('url-id');

                    $.ajax({
                        url : '{{ route("delete-url" ) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            url_id: urlId
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                $('#shortened-url-table').html(response.urls_table);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                });

            });
        </script>
    @endsection
</x-app-layout>
