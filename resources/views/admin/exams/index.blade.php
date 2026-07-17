@extends('layouts.admin')

@section('title', 'Gestion des Examens')


@section('page-title')
Gestion des <span style="color:#0066ff;">Examens</span>
@endsection


@section('page-subtitle')
Consultez et gérez le catalogue des examens médicaux.
@endsection



@section('header-actions')

<a href="{{ route('admin.exams.create') }}"
   class="btn-add-exam">

    <svg fill="none"
         stroke="currentColor"
         stroke-width="2.5"
         viewBox="0 0 24 24">

        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M12 4.5v15m7.5-7.5h-15"/>

    </svg>

    Ajouter un examen

</a>

@endsection





@section('content')



@if(session('success'))

<div class="alert-success">

    {{ session('success') }}

</div>

@endif





<div class="data-section anim">


<div class="data-header">


<div class="data-title">

Catalogue des examens

</div>


</div>







<!-- FILTERS -->

<form method="GET"
      action="{{ route('admin.exams.index') }}">


<div class="filters-bar">



<div>


<label class="filter-label">
Catégorie
</label>


<select name="category"
        class="filter-select">


<option value="">
Toutes
</option>


<option value="biochemistry"
{{ request('category') == 'biochemistry' ? 'selected':'' }}>
Biochimie
</option>


<option value="hematology"
{{ request('category') == 'hematology' ? 'selected':'' }}>
Hématologie
</option>


<option value="microbiology"
{{ request('category') == 'microbiology' ? 'selected':'' }}>
Microbiologie
</option>


<option value="immunology"
{{ request('category') == 'immunology' ? 'selected':'' }}>
Immunologie
</option>


<option value="urinalysis"
{{ request('category') == 'urinalysis' ? 'selected':'' }}>
Urinalyse
</option>


<option value="other"
{{ request('category') == 'other' ? 'selected':'' }}>
Autre
</option>


</select>


</div>






<div>


<label class="filter-label">
Recherche
</label>


<input type="text"
       name="search"
       value="{{ request('search') }}"
       class="filter-input"
       placeholder="Nom ou code...">


</div>






<div style="align-self:end">


<label class="filter-checkbox-wrap">


<input type="checkbox"
       name="show_archived"
       value="1"
       {{ request('show_archived') ? 'checked':'' }}>


Afficher les archives


</label>


</div>






<div style="align-self:end">


<button class="btn-filter">

Filtrer

</button>


</div>



</div>


</form>









<!-- TABLE -->

<table class="data-table">


<thead>


<tr>


<th>
Code
</th>


<th>
Examen
</th>


<th>
Catégorie
</th>


<th>
Valeur normale
</th>


<th>
Statut
</th>


<th>
Actions
</th>


</tr>


</thead>





<tbody>


@forelse($exams as $exam)



<tr class="{{ $exam->is_archive ? 'archived':'' }}">





<td>

<span class="exam-code">

{{ $exam->code }}

</span>

</td>







<td>

<a href="{{ route('admin.exams.show',$exam) }}"
   style="text-decoration:none;color:inherit;">

    <div class="exam-name">
        {{ $exam->name }}
    </div>

    @if($exam->description)

        <div class="exam-desc">
            {{ $exam->description }}
        </div>

    @endif

</a>

</td>







<td>


<span class="category-badge cat-{{ $exam->category }}">

{{ ucfirst($exam->category) }}

</span>


</td>







<td>

{{ $exam->default_normal_range ?? '—' }}

</td>







<td>


@if($exam->is_archive)


<span class="status-badge status-archived">

<span class="dot"></span>

Archivé

</span>


@else


<span class="status-badge status-active">

<span class="dot"></span>

Actif

</span>


@endif


</td>








<td>


<div style="
display:flex;
gap:6px;
">


<a href="{{ route('admin.exams.edit',$exam) }}"
   class="table-action-btn"
   title="Modifier">


<svg fill="none"
     stroke="currentColor"
     stroke-width="2"
     viewBox="0 0 24 24">

<path stroke-linecap="round"
stroke-linejoin="round"
d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07"/>

</svg>


</a>







<form method="POST"
      action="{{ route('admin.exams.archive',$exam) }}">


@csrf

@method('PATCH')



<button type="submit"
        class="table-action-btn archive-btn">


<svg fill="none"
     stroke="currentColor"
     stroke-width="2"
     viewBox="0 0 24 24">


<path stroke-linecap="round"
stroke-linejoin="round"
d="M20.25 7.5l-.625 10.632"/>


</svg>


</button>



</form>





</div>


</td>





</tr>





@empty


<tr>


<td colspan="6">


<div class="empty-state">


<h3>
Aucun examen trouvé
</h3>


<p>
Ajoutez votre premier examen médical.
</p>


</div>


</td>


</tr>


@endforelse




</tbody>



</table>






<!-- PAGINATION -->

@if($exams->hasPages())


<div class="pagination-wrap">

{{ $exams->links() }}

</div>


@endif





</div>



@endsection