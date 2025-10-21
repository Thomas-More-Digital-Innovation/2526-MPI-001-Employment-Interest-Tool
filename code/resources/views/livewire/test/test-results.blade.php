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
                <x-text variant="small">
                    {{ $mainInterest['interest_field_description'] }}
                </x-text>

                @if($firstUrl)
                    <x-question-image 
                        :imageUrl="$firstUrl"
                        alt="Main Field Image"
                        class="rounded-lg mt-3"
                        style="width:400px;height:auto;object-fit:cover;"
                    />
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
                <x-text variant="small">
                    {{ $secondInterest['interest_field_description'] }}
                </x-text>

                @if($secondUrl)
                    <x-question-image 
                        :imageUrl="$secondUrl"
                        alt="Second Field Image"
                        class="rounded-lg mt-3"
                        style="width:350px;height:auto;object-fit:cover;"
                    />
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
                <x-text variant="small">
                    {{ $lastInterest['interest_field_description'] }}
                </x-text>

                @if($lastUrl)
                    <x-question-image 
                        :imageUrl="$lastUrl"
                        alt="Last Field Image"
                        class="rounded-lg mt-3"
                        style="width:300px;height:auto;object-fit:cover;"
                    />
                @endif
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-center">
        <flux:button variant="primary" href="{{ route('dashboard') }}" color="green" size="4xl" class="ease-in-out duration-200 rounded! hover:rounded-3xl! px-5 py-3 text-white">
            {{ __('testresults.continue') }}
        </flux:button>
    </div>

</div>
