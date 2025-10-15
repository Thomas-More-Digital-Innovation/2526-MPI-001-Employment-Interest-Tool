@props(['datetime' => null, 'formatOptions' => null, 'id' => null])
@php
use Illuminate\Support\Str;
use Carbon\Carbon;

// compute iso and id
$iso = null;
if ($datetime) {
    try {
        if (method_exists($datetime, 'toIsoString')) {
            $iso = $datetime->toIsoString();
        } else {
            $iso = Carbon::parse($datetime)->toIsoString();
        }
    } catch (\Exception $e) {
        $iso = null;
    }
}

$id = $id ?? 'localized-time-'.Str::random(8);

$formatOptions = $formatOptions ?? ['day' => '2-digit', 'month' => 'short', 'year' => 'numeric', 'hour' => '2-digit', 'minute' => '2-digit'];
$formatJson = json_encode($formatOptions);
@endphp

@if(!$iso)
    —
@else
    <time id="{{ $id }}" datetime="{{ $iso }}">{{ $iso }}</time>
    <script>(function(){
        try{
            var el = document.getElementById({{ Illuminate\Support\Js::from($id) }});
            if(!el) return;
            var iso = {{ Illuminate\Support\Js::from($iso) }};
            var opts = {{ Illuminate\Support\Js::from($formatOptions) }};
            var dt = new Date(iso);
            if (isNaN(dt.getTime())) return;
            var formatted = new Intl.DateTimeFormat(undefined, opts).format(dt);
            el.textContent = formatted;
        }catch(e){
            console && console.error && console.error(e);
        }
    })();</script>
@endif
