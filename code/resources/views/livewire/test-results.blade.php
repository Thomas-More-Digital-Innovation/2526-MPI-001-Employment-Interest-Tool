<div class="flex flex-col h-full p-6">
    <!-- Main content -->
    <div class="flex flex-col gap-6 items-start">

        <!-- Title + Result -->
        <div>
            @if($mainInterest)
                <h3>Main Interest</h3>
                <p>
                    <!-- Delete the ID after development -->
                    ID: <strong>{{ $mainInterest['interest_field_id'] }}</strong><br>
                </p>

                <h2 class="text-2xl font-semibold mb-2">
                    Interest Field: <strong>{{ $mainInterest['interest_field_name'] }}</strong><br>
                </h2>

                <p class="text-2xl">
                    Times Selected: {{ $mainInterest['total'] }}
                </p>
            @else
                <p>No interests found for this attempt.</p>
            @endif
        </div>
        @php
            // Hotlink an image for each category ID
            $images = [
                1 => 'https://images.pexels.com/photos/4974915/pexels-photo-4974915.jpeg?_gl=1*dvfhxi*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgyMDckajQ2JGwwJGgw',
                2 => 'https://images.pexels.com/photos/7659567/pexels-photo-7659567.jpeg?_gl=1*18neo03*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgxNDUkajQzJGwwJGgw',
                3 => 'https://images.pexels.com/photos/8613089/pexels-photo-8613089.jpeg?_gl=1*1v23w7f*_ga*OTk5Mjc1NDgwLjE3NTg4MDMyMTI.*_ga_8JE65Q40S6*czE3NTk1ODcxMTgkbzIkZzEkdDE3NTk1ODgwNTUkajQ2JGwwJGgw',
            ];

            $id  = (int)($mainInterest['interest_field_id'] ?? 0);
            $url = $images[$id] ?? null;
        @endphp


        @if($mainInterest && $url)
            <!-- Display an image that matches the category ID -->
            <img
                src="{{ $url }}"
                alt="Interest Field Image"
                class="rounded-lg shadow-md float-right ml-4"
                style="width:300px;height:auto;object-fit:cover;"
            >
        @endif

    </div>

    <!-- WIP Continue button that leads to test results -->
    <div class="mt-8 flex justify-center">
        <a href="{{ route('client.taketest') }}">
            <button class="px-6 py-3 bg-teal-600 text-white rounded-md text-lg hover:bg-teal-700">
            Continue
            </button>
        </a>
    </div>
</div>
