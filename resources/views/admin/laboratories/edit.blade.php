@extends('layouts.admin')

@section('title', 'Modifier le Laboratoire')

@section('page-title', 'Modifier le Laboratoire')
@section('page-subtitle', 'Mettez à jour les informations du laboratoire.')

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title" style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            Informations Générales du Laboratoire
        </h3>

        @if($errors->any())
            <div class="form-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.laboratories.update', $laboratory) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom du Laboratoire<span class="required-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $laboratory->name) }}" required placeholder="Ex: Laboratoire Pasteur" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $laboratory->city) }}" placeholder="Ex: Paris" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row" style="margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $laboratory->email) }}" placeholder="Ex: contact@labpasteur.com" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro de Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $laboratory->phone) }}" placeholder="Ex: +3312345678" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">Adresse Physique</label>
                <textarea name="address" rows="3" placeholder="Ex: 12 Rue de la Paix, 75002 Paris" class="form-control" style="resize: vertical;">{{ old('address', $laboratory->address) }}</textarea>
            </div>

            <!-- Footer actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                <a href="{{ route('admin.laboratories.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
@endsection
