@extends('layouts.admin')

@section('title', 'Ajouter un Examen Disponible')

@section('page-title')
Ajouter un <span style="color:#0066ff;">Examen Disponible</span>
@endsection

@section('content')

@if ($errors->any())
    <div class="form-errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="data-section anim anim-1" style="max-width:600px;">
    <div style="padding:28px;">
        <form action="{{ route('admin.available-exams.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Laboratoire <span class="required-star">*</span></label>
                <select name="labo_id" class="form-control" required>
                    <option value="">Sélectionner...</option>
                    @foreach($labos as $labo)
                        <option value="{{ $labo->id }}" {{ old('labo_id') == $labo->id ? 'selected' : '' }}>{{ $labo->name }} — {{ $labo->city }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Examen <span class="required-star">*</span></label>
                <select name="exam_id" class="form-control" required>
                    <option value="">Sélectionner...</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>[{{ $exam->code }}] {{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Prix (DT) <span class="required-star">*</span></label>
                <input type="number" name="price" class="form-control" value="{{ old('price', '0.00') }}" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="accent-color:#0066ff; width:16px; height:16px;">
                    <span class="form-label" style="margin:0;">Actif</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; margin-top:24px;">
                <button type="submit" class="btn-submit">Enregistrer</button>
                <a href="{{ route('admin.available-exams.index') }}" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
