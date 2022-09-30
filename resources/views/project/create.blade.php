@extends('layouts.app')
@section('title', $title)
@section('project', $project)
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
        @if ($errors->any())
        <div class="container">
            @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
            @endforeach
        </div>
        @endif
      <form action="{{ url('/projects') }}" method="post">
        @csrf
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="projectNameInput">Project Name</label>
              <input type="text" name="projectName" placeholder="Enter project name..."
                     value="{{ old('projectName') }}" class="form-control" id="projectNameInput"
                     aria-describedby="projectNameInput">
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label for="projectEndDate">Project End Date</label>
              <input type="date" name="projectEndDate"
                     class="form-control" id="projectEndDate"
                     aria-describedby="projectEndDate">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="projectDescription">Project Description</label>
          <textarea class="form-control" name="projectDescription" id="projectDescription" placeholder="Enter Project Description..." rows="3">{{ Request:: old('projectDescription') }}</textarea>
        </div>
        <div class="form-group">
          <label for="projectUsers">Add users</label>
          <select multiple name="users[]" class="form-control" id="projectUsers">
            @foreach ($users as $user)
              <option value="{{ $user->id }}" >{{ $user->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>
</div>
@endsection
