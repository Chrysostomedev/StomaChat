@extends('layouts.app')

@section('content')
<div class="container py-5">

  <div class="card shadow-lg border-0 mx-auto" style="max-width:600px;">
    <div class="card-body p-4">
      <h3 class="fw-bold text-center text-info mb-4"><i class="bi bi-person-gear me-2"></i>Modifier mon profil</h3>

      @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Photo de profil --}}
        <div class="text-center mb-3">
          @if($user->photo)
            <img src="{{ asset('storage/'.$user->photo) }}" class="rounded-circle mb-3 shadow-sm" width="100" height="100" style="object-fit:cover;">
          @else
            <i class="bi bi-person-circle text-secondary" style="font-size:4rem;"></i>
          @endif
          <label class="photo-upload w-100 mt-2">
            <i class="bi bi-camera text-info me-1"></i> Modifier ma photo
            <input type="file" name="photo" class="d-none" accept="image/*">
          </label>
        </div>

        <div class="mb-3">
          <label class="form-label">Pseudo</label>
          <input type="text" name="pseudo" class="form-control" value="{{ old('pseudo', $user->pseudo) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Âge</label>
          <input type="number" name="age" class="form-control" value="{{ old('age', $user->age) }}" min="17" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="2">{{ old('description', $user->description) }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Centres d'intérêt</label>
          <input type="text" name="centre_interet" class="form-control" value="{{ old('centre_interet', $user->centre_interet) }}">
        </div>

        <div class="mb-3">
          <label class="form-label">Profession</label>
          <input type="text" name="profession" class="form-control" value="{{ old('profession', $user->profession) }}">
        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-info text-white px-4"><i class="bi bi-save me-2"></i>Enregistrer</button>
          <a href="{{ route('profile.show') }}" class="btn btn-secondary ms-2"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.photo-upload {
  display: inline-block;
  background: rgba(91,192,190,0.1);
  border: 2px dashed #5bc0be;
  padding: 10px;
  border-radius: 12px;
  cursor: pointer;
  transition: 0.3s;
  font-weight: 500;
}
.photo-upload:hover {
  background: rgba(91,192,190,0.2);
}
</style>
@endsection
