<x-layouts.app.sidebar>
    <flux:main>
        <div class="space-y-6 p-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('researcher.dashboardResearcher') }}</h2>
                </div>
            </div>
            <div class="flex flex-wrap bg-white text-black rounded-lg px-4 py-5">
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">Total of users</h2>
                    <p>{{$totalUsers}}</p>
                </div>
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">Total of Tests</h2>
                    <p>{{$totalTests}}</p>
                </div>
            </div>
            <div class="flex flex-wrap bg-white text-black rounded-lg px-4 py-5">
                <h2 class="text-center w-full text-xl font-bold">Times Intrestfield chosen</h2>
                <div class="sm:w-full h-auto xl:w-3/4 xl:px-2">
                    <livewire:chart
                        :labels="['A','B','C', 'D', 'E', 'F', 'A','B','C', 'D', 'E', 'F']"
                        :data="[100,2,3,1,100,2558,100,2,3,1,100,2558]"
                        class="max-w-1"
                    />
                </div>
                <div class="sm:w-full xl:w-1/4 xl:px-2 mt-10">
                    <ol class="list-decimal list-inside ms-3">
                        @foreach($mostChosenIntrestFields as $field)
                            <li>{{$field->interest_field_name}} : {{$field->total_chosen}}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
