<div class="flex flex-col h-full p-6">

        @php
            $firstUrl = $mainInterestImg ?? null;
            $secondUrl = $secondInterestImg ?? null;
            $lastUrl = $lastInterestImg ?? null;
        @endphp

    <div class="flex justify-items-start items-center gap-x-12">
        @if($mainInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <p class="text-xl font-medium text-gray-800 mb-2">
                    You are most interested in:
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
                    Your second interest is:
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
                    You are least interested in:
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


    <div class="mt-8 flex justify-center">
        <a href="{{ route('dashboard') }}">
            <button class="px-6 py-3 bg-teal-600 text-white rounded-md text-lg hover:bg-teal-700">
            Continue
            </button>
        </a>
    </div>

</div>
