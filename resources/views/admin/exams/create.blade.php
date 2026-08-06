@extends('layouts.admin')

@section('title', __('admin.exams.add_title'))


@section('page-title')
{{ __('admin.exams.add_prefix') }}<span style="color:#0066ff;">{{ __('admin.exams.exam') }}</span>
@endsection


@section('page-subtitle')
{{ __('admin.exams.create_subtitle') }}
@endsection



@section('header-actions')

<a href="{{ route('admin.exams.index') }}"
   class="btn-cancel">

    {{ __('admin.exams.return_exams') }}

</a>

@endsection






@section('content')


<div class="data-section anim">


<div class="data-header">

<div class="data-title">
{{ __('admin.exams.exam_information') }}
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

{{ __('admin.exams.code_exam') }}

<span class="required-star">*</span>

</label>


<input type="text"
       name="code"
       value="{{ old('code') }}"
       class="form-control"
       placeholder="{{ __('admin.exams.code_placeholder') }}"
       required>


</div>







<div class="form-group">


<label class="form-label">

{{ __('admin.exams.exam_name') }}

<span class="required-star">*</span>

</label>


<input type="text"
       name="name"
       value="{{ old('name') }}"
       class="form-control"
       placeholder="{{ __('admin.exams.name_placeholder') }}"
       required>


</div>



</div>









<div class="form-row">



<div class="form-group">


<label class="form-label">

{{ __('admin.exams.category') }}

<span class="required-star">*</span>

</label>




<select name="category"
        class="form-control"
        required>


<option value="">
{{ __('admin.exams.select') }}
</option>


<option value="biochemistry">
{{ __('admin.exams.category_biochemistry') }}
</option>


<option value="hematology">
{{ __('admin.exams.category_hematology') }}
</option>


<option value="microbiology">
{{ __('admin.exams.category_microbiology') }}
</option>


<option value="immunology">
{{ __('admin.exams.category_immunology') }}
</option>


<option value="urinalysis">
{{ __('admin.exams.category_urinalysis') }}
</option>


<option value="other">
{{ __('admin.exams.category_other') }}
</option>



</select>


</div>







<div class="form-group">


<label class="form-label">

{{ __('admin.exams.normal_range') }}

</label>



<input type="text"
       name="default_normal_range"
       value="{{ old('default_normal_range') }}"
       class="form-control"
       placeholder="{{ __('admin.exams.normal_range_placeholder') }}">


</div>



</div>








<div class="form-group">


<label class="form-label">

{{ __('admin.exams.description') }}

</label>


<textarea name="description"
          rows="3"
          class="form-control"
          placeholder="{{ __('admin.exams.description_placeholder') }}">{{ old('description') }}</textarea>


</div>








<div class="form-group">


<label class="form-label">

{{ __('admin.exams.preparation_instructions') }}

</label>


<textarea name="preparation_instructions"
          rows="3"
          class="form-control"
          placeholder="{{ __('admin.exams.preparation_placeholder') }}">{{ old('preparation_instructions') }}</textarea>


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

{{ __('admin.exams.parameters') }}

</div>



<button type="button"
        onclick="addParameter()"
        class="btn-add-exam">

+ {{ __('admin.exams.parameter_add') }}

</button>


</div>








<div id="parameters-container">



<div class="form-row">



<div class="form-group">


<label class="form-label">
{{ __('common.name') }}
</label>


<input type="text"
       name="parameters[0][name]"
       class="form-control"
       placeholder="{{ __('admin.exams.parameter_name_placeholder') }}">


</div>





<div class="form-group">


<label class="form-label">
{{ __('admin.exams.parameter_unit') }}
</label>


<input type="text"
       name="parameters[0][unit]"
       class="form-control"
       placeholder="g/dL">


</div>





<div class="form-group">


<label class="form-label">
{{ __('admin.exams.normal_range') }}
</label>


<input type="text"
       name="parameters[0][normal_range]"
       class="form-control"
       placeholder="13 - 17">

<label class="form-label" style="margin-top:15px;">
{{ __('admin.exams.parameter_critical_low') }}
</label>

<input type="number"
       step="any"
       name="parameters[0][critical_low]"
       class="form-control"
       placeholder="7.0">

<label class="form-label" style="margin-top:15px;">
{{ __('admin.exams.parameter_critical_high') }}
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

{{ __('common.cancel') }}

</a>



<button type="submit"
        class="btn-submit">

{{ __('admin.exams.create_button') }}

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
placeholder="@lang('admin.exams.parameter_name_placeholder')">

</div>




<div class="form-group">

<input type="text"
name="parameters[${parameterIndex}][unit]"
class="form-control"
placeholder="@lang('admin.exams.parameter_unit')">

</div>




<div class="form-group">

<input type="text"
name="parameters[${parameterIndex}][normal_range]"
class="form-control"
placeholder="@lang('admin.exams.normal_range')">

</div>



<div class="form-group">

<input type="number"
step="any"
name="parameters[${parameterIndex}][critical_low]"
class="form-control"
placeholder="@lang('admin.exams.parameter_critical_low')">

</div>



<div class="form-group">

<input type="number"
step="any"
name="parameters[${parameterIndex}][critical_high]"
class="form-control"
placeholder="@lang('admin.exams.parameter_critical_high')">

</div>

</div>



</div>


`


);


parameterIndex++;


}



</script>




@endsection