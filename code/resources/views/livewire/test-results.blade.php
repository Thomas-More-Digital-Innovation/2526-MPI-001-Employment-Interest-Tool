<div class="flex flex-col h-full p-6">
    <!-- Main content -->
    <div class="flex flex-col gap-6 items-start">
        <!-- Title + Result -->
        <div>
            @if($mainInterest)
                <h3>Main Interest</h3>
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
    </div>

    <!-- Continue button -->
    <div class="mt-8 flex justify-center">
        <button class="px-6 py-3 bg-teal-600 text-white rounded-md text-lg hover:bg-teal-700">
            Continue
        </button>
    </div>
</div>
