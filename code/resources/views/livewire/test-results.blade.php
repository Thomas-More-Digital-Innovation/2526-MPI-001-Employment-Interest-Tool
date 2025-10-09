<div class="flex flex-col h-full p-6">

        @php
            $firstUrl = $mainInterestImg ?? null;
            $secondUrl = $secondInterestImg ?? null;
            $lastUrl = $lastInterestImg ?? null;
        @endphp

    <div class="flex justify-center items-center gap-x-12 mb-12">

        @if($noSelections)
            <div class="flex justify-center items-center h-full">
                <p class="text-xl text-gray-700 font-medium dark:text-white">
                    {{ __('testresults.no_questions_selected') }}
                </p>
            </div>
        @endif

        @if($mainInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <p class="text-xl font-medium text-gray-800 mb-2">
                    {{ __('testresults.most_interested_in') }}:
                </p>
                <p class="text-xl text-gray-800 mb-4">
                    {{ $mainInterest['interest_field_name'] }}
                </p>

                @if($firstUrl)
                    <img
                        src="{{ $firstUrl }}"
                        alt="Main Field Image"
                        class="rounded-lg mt-3"
                        style="width:400px;height:auto;object-fit:cover;"
                    >
                @endif
            </div>
        @endif

        @if($secondInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <p class="text-xl font-medium text-gray-800 mb-2">
                    {{ __('testresults.second_most_interested_in') }}:
                </p>
                <p class="text-xl text-gray-800 mb-4">
                    {{ $secondInterest['interest_field_name'] }}
                </p>

                @if($secondUrl)
                    <img
                        src="{{ $secondUrl }}"
                        alt="Second Field Image"
                        class="rounded-lg mt-3"
                        style="width:350px;height:auto;object-fit:cover;"
                    >
                @endif
            </div>
        @endif

        @if($lastInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <p class="text-xl font-medium text-gray-800 mb-2">
                    {{ __('testresults.least_interested_in') }}:
                </p>
                <p class="text-xl text-gray-800 mb-4">
                    {{ $lastInterest['interest_field_name'] }}
                </p>

                @if($lastUrl)
                    <img
                        src="{{ $lastUrl }}"
                        alt="Last Field Image"
                        class="rounded-lg mt-3"
                        style="width:300px;height:auto;object-fit:cover;"
                    >
                @endif
            </div>
        @endif
    </div>
    <!-- WIP Continue button that leads to test results -->
    <div class="mt-8 flex justify-center">
        <a href="{{ route('dashboard') }}">
            <button class="px-6 py-3 bg-teal-600 text-white rounded-md text-lg hover:bg-teal-700">

            {{ __('Continue') }}
            </button>
        </a>
    </div>

</div>
