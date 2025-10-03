<div class="flex flex-col h-full p-6">
    <!-- Main content -->
    <div class="flex flex-col gap-6 items-start">
        <!-- Title + Result -->
        <div>
            <h1 class="text-3xl md:text-4xl font-semibold mb-2">Test finished!</h1>
            <p class="text-lg md:text-xl">
                Based on this AIT test you are most interested in
                @foreach($mainInterest as $interest)
                    <h1>{{$interest->name}}</h1>
                @endforeach
            </p>
        </div>

        <!-- Other interests -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">Other Interests</h2>
            <p class="text-lg">{{$otherInterests}}</p>
        </div>
    </div>

    <!-- Continue button -->
    <div class="mt-8 flex justify-center">
        <button class="px-6 py-3 bg-teal-600 text-white rounded-md text-lg hover:bg-teal-700">
            Continue
        </button>
    </div>
</div>
