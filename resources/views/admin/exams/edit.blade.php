@extends('layouts.admin')

@section('title', __('admin.exams.edit_title'))


@section('page-title')
{{ __('admin.exams.edit_prefix') }}<span style="color:#0066ff;">{{ __('admin.exams.exam') }}</span>
@endsection


@section('page-subtitle')
{{ __('admin.exams.edit_subtitle') }}
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

{{ __('common.edit') }} : {{ $exam->name }}

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
      action="{{ route('admin.exams.update',$exam) }}">


@csrf

@method('PUT')








<div class="form-row">



<div class="form-group">


<label class="form-label">

{{ __('admin.exams.code_exam') }}

<span class="required-star">*</span>

</label>


<input type="text"
       name="code"
       value="{{ old('code',$exam->code) }}"
       class="form-control"
       required>


</div>







<div class="form-group">


<label class="form-label">

{{ __('admin.exams.exam_name') }}

<span class="required-star">*</span>

</label>


<input type="text"
       name="name"
       value="{{ old('name',$exam->name) }}"
       class="form-control"
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



<option value="biochemistry"
{{ $exam->category=='biochemistry'?'selected':'' }}>
{{ __('admin.exams.category_biochemistry') }}
</option>



<option value="hematology"
{{ $exam->category=='hematology'?'selected':'' }}>
{{ __('admin.exams.category_hematology') }}
</option>



<option value="microbiology"
{{ $exam->category=='microbiology'?'selected':'' }}>
{{ __('admin.exams.category_microbiology') }}
</option>



<option value="immunology"
{{ $exam->category=='immunology'?'selected':'' }}>
{{ __('admin.exams.category_immunology') }}
</option>



<option value="urinalysis"
{{ $exam->category=='urinalysis'?'selected':'' }}>
{{ __('admin.exams.category_urinalysis') }}
</option>



<option value="other"
{{ $exam->category=='other'?'selected':'' }}>
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
       value="{{ old('default_normal_range',$exam->default_normal_range) }}"
       class="form-control">


</div>



</div>









<div class="form-group">


<label class="form-label">

{{ __('admin.exams.description') }}

</label>


<textarea name="description"
          rows="3"
          class="form-control">{{ old('description',$exam->description) }}</textarea>


</div>









<div class="form-group">


<label class="form-label">

{{ __('admin.exams.preparation_instructions') }}

</label>


<textarea name="preparation_instructions"
          rows="3"
          class="form-control">{{ old('preparation_instructions',$exam->preparation_instructions) }}</textarea>


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



@if($exam->parameters && count($exam->parameters))


@foreach($exam->parameters as $index=>$parameter)



<div class="form-row"
     style="margin-top:15px;">



<div class="form-group">


<input type="text"
name="parameters[{{ $index }}][name]"
value="{{ $parameter['name'] ?? '' }}"
class="form-control"
placeholder="@lang('admin.exams.parameter_name_placeholder')">


</div>





<div class="form-group">


<input type="text"
name="parameters[{{ $index }}][unit]"
value="{{ $parameter['unit'] ?? '' }}"
class="form-control"
placeholder="@lang('admin.exams.parameter_unit')">


</div>





<div class="form-group">


<input type="text"
name="parameters[{{ $index }}][normal_range]"
value="{{ $parameter['normal_range'] ?? '' }}"
class="form-control"
placeholder="@lang('admin.exams.normal_range')">


</div>



</div>



@endforeach



@else



<div class="form-row">



<div class="form-group">


<input type="text"
name="parameters[0][name]"
class="form-control"
placeholder="@lang('admin.exams.parameter_name_placeholder')">


</div>



<div class="form-group">


<input type="text"
name="parameters[0][unit]"
class="form-control"
placeholder="@lang('admin.exams.parameter_unit')">


</div>



<div class="form-group">


<input type="text"
name="parameters[0][normal_range]"
class="form-control"
placeholder="@lang('admin.exams.normal_range')">


</div>



</div>



@endif




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

{{ __('common.save') }}

</button>



</div>








</form>



</div>



</div>








<script>


let parameterIndex = {{ $exam->parameters ? count($exam->parameters) : 1 }};



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



</div>


`


);



parameterIndex++;


}



</script>





@endsection