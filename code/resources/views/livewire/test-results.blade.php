<div class="flex flex-col h-full p-6">

        @php
            $images = [
                1 => 'https://images.pexels.com/photos/4974915/pexels-photo-4974915.jpeg?_gl=1*dvfhxi*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgyMDckajQ2JGwwJGgw',
                2 => 'https://images.pexels.com/photos/7659567/pexels-photo-7659567.jpeg?_gl=1*18neo03*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgxNDUkajQzJGwwJGgw',
                3 => 'https://images.pexels.com/photos/8613089/pexels-photo-8613089.jpeg?_gl=1*1v23w7f*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgwNTUkajQ2JGwwJGgw',
                4 => 'https://images.pexels.com/photos/7988079/pexels-photo-7988079.jpeg?_gl=1*1s4wdjs*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk3NDY4MTckbzMkZzEkdDE3NTk3NDY4MjgkajQ5JGwwJGgw',
                5 => 'https://images.pexels.com/photos/6779716/pexels-photo-6779716.jpeg?_gl=1*i2oa1p*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk3NDY4MTckbzMkZzEkdDE3NTk3NDY4NzIkajUkbDAkaDA.',
                6 => 'https://images.pexels.com/photos/6169027/pexels-photo-6169027.jpeg?_gl=1*e3sz2h*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk3NDY4MTckbzMkZzEkdDE3NTk3NDY5MzAkajI4JGwwJGgw',
            ];

            $firstInterestId  = (int)($mainInterest['interest_field_id'] ?? 0);
            $firstUrl = $images[$firstInterestId] ?? null;

            $secondInterestId = (int)($secondInterest['interest_field_id'] ?? 0);
            $secondUrl = $images[$secondInterestId] ?? null;

            $lastInterestId = (int)($lastInterest['interest_field_id'] ?? 0);
            $lastUrl = $images[$lastInterestId] ?? null;
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
