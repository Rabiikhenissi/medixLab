@extends('layouts.center')


@section('content')


<h1 class="text-2xl font-bold">
Résultat :
{{ $item->exam->name }}
</h1>


<form method="POST"
action="{{route('center.results.store',$item)}}">

@csrf



@foreach($item->exam->parameters as $index=>$parameter)


<div class="bg-white p-4 rounded-xl mb-3">


<h3>
{{$parameter->name}}
</h3>


<input type="hidden"
name="parameters[{{$index}}][name]"
value="{{$parameter->name}}">


<input type="hidden"
name="parameters[{{$index}}][range]"
value="{{$parameter->normal_range}}">


<input
class="border p-2"
placeholder="Valeur"
name="parameters[{{$index}}][value]">


<select
name="parameters[{{$index}}][status]"
class="border p-2">

<option value="normal">
Normal
</option>

<option value="high">
Elevé
</option>

<option value="low">
Bas
</option>


</select>


</div>


@endforeach



<textarea
name="interpretation"
class="border p-3 w-full"
placeholder="Interprétation">
</textarea>


<button
class="bg-purple-600 text-white px-5 py-2 rounded-xl">

Enregistrer résultat

</button>


</form>


@endsection