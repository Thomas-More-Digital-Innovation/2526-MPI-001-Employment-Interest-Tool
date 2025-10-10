<div class="flex flex-col h-full p-6">

        @php
            $firstUrl = $mainInterestImg ?? null;
            $secondUrl = $secondInterestImg ?? null;
            $lastUrl = $lastInterestImg ?? null;
        @endphp

    <div class="flex justify-center items-center gap-x-12 mb-12">

        @if($noSelections)
            <div class="flex justify-center items-center h-full">
                <x-text variant="large">
                    {{ __('testresults.no_questions_selected') }}
                </x-text>
            </div>
        @endif

        @if($mainInterest)
            <div class="flex flex-col items-start max-w-md p-4 bg-white rounded-lg">
                <x-text variant="large">
                    {{ __('testresults.most_interested_in') }}:
                </x-text>
                <x-text variant="large">
                    {{ $mainInterest['interest_field_name'] }}
                </x-text>

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
                <x-text variant="large">
                    {{ __('testresults.second_most_interested_in') }}:
                </x-text>
                <x-text variant="large">
                    {{ $secondInterest['interest_field_name'] }}
                </x-text>

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
                <x-text variant="large">
                    {{ __('testresults.least_interested_in') }}:
                </x-text>
                <x-text variant="large">
                    {{ $lastInterest['interest_field_name'] }}
                </x-text>

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
        <x-btn variant="primary-user" href="{{ route('dashboard') }}">
            {{ __('Doorgaan') }}
        </x-btn>
    </div>

</div>
