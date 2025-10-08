<div class="flex flex-col h-full p-6">

        @php
            $firstUrl = $mainInterestImg ?? null;
            $secondUrl = $secondInterestImg ?? null;
            $lastUrl = $lastInterestImg ?? null;
        @endphp

    <div class="flex justify-center items-center gap-x-12 mb-12">

        @if($noSelections)
            <div class="flex justify-center items-center h-full">
                <p class="text-large">
                    {{ __('testresults.no_questions_selected') }}
                </p>
            </div>
        @endif

        @if($mainInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <p class="text-large">
                    {{ __('testresults.most_interested_in') }}:
                </p>
                <p class="text-large-bold">
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
                <p class="text-large">
                    {{ __('testresults.second_most_interested_in') }}:
                </p>
                <p class="text-large-bold">
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
                <p class="text-large">
                    {{ __('testresults.least_interested_in') }}:
                </p>
                <p class="text-large-bold">
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
            <button class="btn-primary">
            {{ __('Continue') }}
            </button>
        </a>
    </div>

</div>
