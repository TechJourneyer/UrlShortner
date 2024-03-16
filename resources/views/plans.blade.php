<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Loop through plans and show plans show upgrade or downgrade options based on url limit and current plan -->
            @foreach ($data['plans'] as $plan)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $plan->name }}</h2>
                            <p class="text-gray-500">{{ $plan->urls_limit }} URLs</p>
                        </div>
                        <div>
                            @if (isset($data['subscription']->plan_id) && $plan->id == $data['subscription']->plan_id)
                                <p class="text-gray-500">Current Plan</p>
                            @else
                                @if (!isset($data['subscription']->plan_id))
                                    <button data-action='upgrade' class="app-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md  bg-grey hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-green-500" data-plan-id="{{ $plan->id }}">Upgrade</button>
                                @else

                                    @if($data['subscription']->urls_limit == -1)
                                        <p class="text-gray-500">Unlimited</p>
                                    @else 
                                        @if (($plan->urls_limit == -1 && $data['subscription']->plan_id != $plan->id) || $plan->urls_limit > $data['subscription']->urls_limit)
                                            <button data-action='upgrade' class="app-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md  bg-grey hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-green-500" data-plan-id="{{ $plan->id }}">Upgrade</button>
                                        @else
                                            <button data-action='downgrade' class="app-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md  bg-grey hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-green-500" data-plan-id="{{ $plan->id }}">Downgrade</button>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            

        </div>
    </div>

    @section('scripts')
        <script>

            $(document).ready(function () {

                $(document).on('click', '.app-button', function() {
                    var action = $(this).data('action');
                    var plan_id = $(this).data('plan-id');

                    if (action == 'upgrade') {
                        $.ajax({
                            url: '{{ route("change-plan") }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: {
                                plan_id: plan_id,
                                action: action
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message);
                                    window.location.reload();
                                }
                                else{
                                    alert('Something went wrong');
                                }
                            }
                        })
                    }
                });
            });
        </script>

    @endsection


</x-app-layout>