@extends('layouts.admin')


@section('title', __('admin.exams.details_title'))


@section('page-title')

{{ __('admin.exams.show_details_prefix') }} <span style="color:#0066ff;">
{{ $exam->name }}
</span>

@endsection



@section('page-subtitle')

{{ __('admin.exams.show_subtitle') }}

@endsection




@section('content')


<div class="data-section">


<div class="data-header">

<div class="data-title">
{{ __('admin.exams.general_information') }}
</div>


</div>




<div style="padding:25px;">



<p>
<strong>{{ __('admin.exams.code') }} :</strong>
{{ $exam->code }}
</p>



<p>
<strong>{{ __('common.name') }} :</strong>
{{ $exam->name }}
</p>




<p>
<strong>{{ __('admin.exams.category') }} :</strong>
{{ ucfirst($exam->category) }}
</p>




<p>
<strong>{{ __('admin.exams.normal_range') }} :</strong>
{{ $exam->default_normal_range ?? '-' }}
</p>




<p>
<strong>{{ __('admin.exams.description') }} :</strong>
</p>

<p>
{{ $exam->description ?? __('admin.exams.no_description') }}
</p>




<p>
<strong>{{ __('admin.exams.instructions') }}</strong>
</p>


<p>
{{ $exam->preparation_instructions ?? __('admin.exams.no_instruction') }}
</p>



</div>



</div>






<div class="data-section">


<div class="data-header">

<div class="data-title">
{{ __('admin.exams.parameters_title') }}
</div>


</div>



<table class="data-table">


<thead>

<tr>

<th>
{{ __('common.name') }}
</th>

<th>
{{ __('admin.exams.parameter_unit') }}
</th>

<th>
{{ __('admin.exams.normal_range') }}
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

{{ __('admin.exams.no_parameters') }}

</td>

</tr>


@endforelse



</tbody>


</table>


</div>




<a href="{{ route('admin.exams.index') }}"
class="btn-filter">

← {{ __('admin.exams.return_exams') }}

</a>



@endsection