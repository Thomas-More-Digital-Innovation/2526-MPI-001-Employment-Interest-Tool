<x-layouts.app.sidebar>
    <flux:main>
        <div class="space-y-6">
            <div class="dark:bg-white bg-gray-500 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold tracking-tight dark:text-black text-white">{{ __('pagesresearcher.dashboard_researcher') }}</h2>
                </div>
            </div>
            <div class="flex flex-wrap dark:bg-white bg-gray-500 dark:text-black text-white rounded-lg px-4 py-5">
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">{{__('pagesresearcher.totalOfUsers')}}</h2>
                    <p>{{$totalUsers}}</p>
                </div>
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">{{__('pagesresearcher.totalOfTests')}}</h2>
                    <p>{{$totalTests}}</p>
                </div>
            </div>
            <div class="flex flex-wrap dark:bg-white bg-gray-500 dark:text-black text-white rounded-lg px-4 py-5">
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">{{__('pagesresearcher.Organisations')}}</h2>
                    <p>{{$totalOrganisations}}</p>
                </div>
                <div class="w-1/2 px-2">
                    <h2 class="text-xl font-bold">{{__('pagesresearcher.CompletionScore')}}</h2>
                    <p>{{$completionScore}}</p>
                </div>
            </div>
            <div class="flex flex-wrap dark:bg-white bg-gray-500 dark:text-black text-white rounded-lg px-4 py-5">
                <h2 class="text-center w-full text-xl font-bold">{{__('pagesresearcher.timesIntrestfieldChosen')}}</h2>
                <div class="sm:w-full h-auto xl:w-3/4 xl:px-2 bg-white rounded-md">
                    <livewire:chart
                        :labels="$mostChosenIntrestFields->pluck('interest_field_name')"
                        :data="$mostChosenIntrestFields->pluck('total_chosen')"
                        class="max-w-1"
                    />
                </div>
                <div class="sm:w-full xl:w-1/4 xl:px-2 mt-10">
                    @if(!empty($mostChosenIntrestFields))
                        <ol class="list-decimal list-inside ms-3">
                            @foreach($mostChosenIntrestFields as $field)
                                <li>{{$field->interest_field_name}} : {{$field->total_chosen}}</li>
                            @endforeach
                        </ol>
                    @else
                        <p>{{__('pagesresearcher.NoData')}}</p>
                    @endif
                </div>
            </div>
            <livewire:download-csv-button></livewire:download-csv-button>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
