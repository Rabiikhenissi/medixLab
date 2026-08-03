@extends('layouts.admin')

@section('title', 'Ajouter un Examen')


@section('page-title')
Ajouter un <span style="color:#0066ff;">Examen</span>
@endsection


@section('page-subtitle')
Créer un nouvel examen médical dans le catalogue.
@endsection



@section('header-actions')

<a href="{{ route('admin.exams.index') }}"
   class="btn-cancel">

    Retour aux examens

</a>

@endsection






@section('content')


<div class="data-section anim">


<div class="data-header">

<div class="data-title">
Informations de l'examen
</div>

</div>





<div style="padding:30px;">





@if($errors->any())


<div class="form-errors">


<ul>

@foreach($errors->all() as $error)

<li>
{{ $error }}
</li>

@endforeach


</ul>


</div>


@endif







<form method="POST"
      action="{{ route('admin.exams.store') }}">


@csrf







<div class="form-row">



<div class="form-group">


<label class="form-label">

Code examen

<span class="required-star">*</span>

</label>


<input type="text"
       name="code"
       value="{{ old('code') }}"
       class="form-control"
       placeholder="Ex: HBA1C"
       required>


</div>







<div class="form-group">


<label class="form-label">

Nom examen

<span class="required-star">*</span>

</label>


<input type="text"
       name="name"
       value="{{ old('name') }}"
       class="form-control"
       placeholder="Ex: Glycémie"
       required>


</div>



</div>









<div class="form-row">



<div class="form-group">


<label class="form-label">

Catégorie

<span class="required-star">*</span>

</label>




<select name="category"
        class="form-control"
        required>


<option value="">
Sélectionner
</option>


<option value="biochemistry">
Biochimie
</option>


<option value="hematology">
Hématologie
</option>


<option value="microbiology">
Microbiologie
</option>


<option value="immunology">
Immunologie
</option>


<option value="urinalysis">
Urinalyse
</option>


<option value="other">
Autre
</option>



</select>


</div>







<div class="form-group">


<label class="form-label">

Valeur normale

</label>



<input type="text"
       name="default_normal_range"
       value="{{ old('default_normal_range') }}"
       class="form-control"
       placeholder="Ex: 4 - 6 mmol/L">


</div>



</div>








<div class="form-group">


<label class="form-label">

Description

</label>


<textarea name="description"
          rows="3"
          class="form-control"
          placeholder="Description de l'examen">{{ old('description') }}</textarea>


</div>








<div class="form-group">


<label class="form-label">

Instructions de préparation

</label>


<textarea name="preparation_instructions"
          rows="3"
          class="form-control"
          placeholder="Ex: Être à jeun 12h">{{ old('preparation_instructions') }}</textarea>


</div>








<hr style="
border:none;
border-top:1px solid #e8eef4;
margin:25px 0;
">







<div style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
">


<div class="data-title">

Paramètres

</div>



<button type="button"
        onclick="addParameter()"
        class="btn-add-exam">

+ Ajouter un paramètre

</button>


</div>








<div id="parameters-container">



<div class="form-row">



<div class="form-group">


<label class="form-label">
Nom
</label>


<input type="text"
       name="parameters[0][name]"
       class="form-control"
       placeholder="Ex: Hémoglobine">


</div>





<div class="form-group">


<label class="form-label">
Unité
</label>


<input type="text"
       name="parameters[0][unit]"
       class="form-control"
       placeholder="g/dL">


</div>





<div class="form-group">


<label class="form-label">
Valeur normale
</label>


<input type="text"
       name="parameters[0][normal_range]"
       class="form-control"
       placeholder="13 - 17">

<label class="form-label" style="margin-top:15px;">
Valeur critique basse
</label>

<input type="number"
       step="any"
       name="parameters[0][critical_low]"
       class="form-control"
       placeholder="7.0">

<label class="form-label" style="margin-top:15px;">
Valeur critique haute
</label>

<input type="number"
       step="any"
       name="parameters[0][critical_high]"
       class="form-control"
       placeholder="23.0">


</div>



</div>



</div>







<div style="
margin-top:30px;
display:flex;
justify-content:flex-end;
gap:10px;
">


<a href="{{ route('admin.exams.index') }}"
   class="btn-cancel">

Annuler

</a>



<button type="submit"
        class="btn-submit">

Créer l'examen

</button>



</div>






</form>



</div>


</div>









<script>


let parameterIndex = 1;



function addParameter()
{


let container =
document.getElementById('parameters-container');



container.insertAdjacentHTML(

'beforeend',


`

<div class="form-row"
     style="margin-top:15px;">



<div class="form-group">

<input type="text"
name="parameters[${parameterIndex}][name]"
class="form-control"
placeholder="Nom du paramètre">

</div>




<div class="form-group">

<input type="text"
name="parameters[${parameterIndex}][unit]"
class="form-control"
placeholder="Unité">

</div>




<div class="form-group">

<input type="text"
name="parameters[${parameterIndex}][normal_range]"
class="form-control"
placeholder="Valeur normale">

</div>



<div class="form-group">

<input type="number"
step="any"
name="parameters[${parameterIndex}][critical_low]"
class="form-control"
placeholder="Valeur critique basse">

</div>



<div class="form-group">

<input type="number"
step="any"
name="parameters[${parameterIndex}][critical_high]"
class="form-control"
placeholder="Valeur critique haute">

</div>

</div>



</div>


`


);


parameterIndex++;


}



</script>




@endsection