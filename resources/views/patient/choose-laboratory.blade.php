<x-layouts.auth>

<x-slot:title>
Choisir un laboratoire
</x-slot:title>


<div class="max-w-5xl mx-auto py-8">


<h1 class="text-2xl font-bold mb-6">
    Choisir un laboratoire
</h1>


<div class="grid md:grid-cols-3 gap-5">


@foreach($laboratories as $lab)

<div class="bg-white rounded-xl shadow border p-6">


<h2 class="font-bold text-lg">
    {{ $lab->name }}
</h2>


<p class="text-gray-600 mt-2">
    📍 {{ $lab->address }}
</p>


<p class="text-gray-600">
    🏙 {{ $lab->city }}
</p>


<p class="text-gray-600">
    ☎ {{ $lab->phone }}
</p>


<p class="text-gray-600">
    ✉ {{ $lab->email }}
</p>



<div class="mt-4">

<h3 class="font-bold">
    Horaires
</h3>


@foreach($lab->workingHours as $hours)

<p class="text-sm text-gray-500">

{{ $hours->day }}

:

{{ $hours->opening_time }}

-

{{ $hours->closing_time }}

</p>


@endforeach

</div>



<form 
method="POST"
class="mt-5"
action="{{ route('patient.assign-laboratory', $examRequest) }}">


@csrf


<input 
type="hidden"
name="labo_id"
value="{{ $lab->id }}">


<button
type="submit"
class="
w-full
bg-purple-600
hover:bg-purple-700
text-white
rounded-lg
py-2
font-bold">

Choisir ce laboratoire

</button>


</form>


</div>


@endforeach


</div>


</div>


</x-layouts.auth>