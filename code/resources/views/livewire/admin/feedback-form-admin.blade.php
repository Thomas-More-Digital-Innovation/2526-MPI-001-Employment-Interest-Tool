<div>
    <form wire:submit.prevent="sendMail">
        <label class="block dark:text-black text-white mt-3">{{__('pageFeedback.Category')}}</label>
        <select wire:model="category" class="w-full border p-2 rounded dark:text-black text-white dark:bg-white bg-gray-500 border-white dark:border-black">
            <option value="">-- {{__('pageFeedback.ChooseCategory')}} --</option>
            <option value="Test">{{__('pageFeedback.Test')}}</option>
            <option value="Vraag">{{__('pageFeedback.Question')}}</option>
            <option value="Media">{{__('pageFeedback.Media')}}</option>
            <option value="Andere">{{__('pageFeedback.Other')}}</option>
        </select>
        <p class="dark:text-red-500 text-red-300 {{$ErrorCategory}}">{{__('pageFeedback.ErrorCategory')}}</p>

        <label class="block dark:text-black text-white mt-3">{{__('pageFeedback.Feedbackmessage')}}</label>
        <textarea wire:model="message" class="w-full border p-2 rounded dark:text-black text-white border-white dark:border-black" rows="5"></textarea>
        <p class="dark:text-red-500 text-red-300 {{$ErrorFeedbackMessage}}">{{__('pageFeedback.ErrorFeedbackMessage')}}</p>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">{{__('actions.send')}}</button>
         @if($SendStatus != '')
            <p x-data="{ show: false }"
               x-init="show = true;
               setTimeout(() =>
                    {show = false;
                    $wire.set('SendStatus', '');}, 3000)"
               class="dark:text-black text-white">{{$SendStatus}}</p>
        @endif
    </form>
</div>
