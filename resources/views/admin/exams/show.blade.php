@extends('layouts.admin')


@section('title','Détails Examen')


@section('page-title')

Détails de <span style="color:#0066ff;">
{{ $exam->name }}
</span>

@endsection



@section('page-subtitle')

Informations complètes de l'examen médical.

@endsection




@section('content')


<div class="data-section">


<div class="data-header">

<div class="data-title">
Informations générales
</div>


</div>




<div style="padding:25px;">



<p>
<strong>Code :</strong>
{{ $exam->code }}
</p>



<p>
<strong>Nom :</strong>
{{ $exam->name }}
</p>




<p>
<strong>Catégorie :</strong>
{{ ucfirst($exam->category) }}
</p>




<p>
<strong>Valeur normale :</strong>
{{ $exam->default_normal_range ?? '-' }}
</p>




<p>
<strong>Description :</strong>
</p>

<p>
{{ $exam->description ?? 'Aucune description' }}
</p>




<p>
<strong>Instructions :</strong>
</p>


<p>
{{ $exam->preparation_instructions ?? 'Aucune instruction' }}
</p>



</div>



</div>






<div class="data-section">


<div class="data-header">

<div class="data-title">
Paramètres de l'examen
</div>


</div>



<table class="data-table">


<thead>

<tr>

<th>
Nom
</th>

<th>
Unité
</th>

<th>
Valeur normale
</th>


</tr>

</thead>



<tbody>


@forelse($exam->parameters as $parameter)


<tr>

<td>
{{ $parameter->name }}
</td>


<td>
{{ $parameter->unit ?? '-' }}
</td>


<td>
{{ $parameter->normal_range ?? '-' }}
</td>


</tr>



@empty


<tr>

<td colspan="3">

Aucun paramètre ajouté.

</td>

</tr>


@endforelse



</tbody>


</table>


</div>




<a href="{{ route('admin.exams.index') }}"
class="btn-filter">

← Retour aux examens

</a>



@endsection