@section('top')
<nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm">
  <a class="navbar-brand" href="{{ url('projects') }}">
    <img src="{{ asset('img/scrumapp.png') }}" alt="Scrumapp" width="30" height="30" loading="lazy">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
      @if (Auth::check())
      <li class="nav-item dropdown">
        <a class="navbar-brand nav-link dropdown-toggle active font-weight-bold" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          @yield('project')
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          @if ($project == 'Scrumapp')
          <a class="dropdown-item" href="{{ url('/projects/create') }}">Create Project</a>
          @else
          <a class="dropdown-item" href="{{ url('/projects/'.$project.'/edit') }}">Settings</a>
          @endif
          <a class="dropdown-item" href="{{ url('/projects') }}">Projects Overview</a>
        </div>
      </li>
      @else
      <li class="nav-item">
        <a class="navbar-brand nav-link active font-weight-bold">
          @yield('project')
        </a>
      </li>
      @endif
      @if (Auth::check() && $project != 'Scrumapp')
      <li class="nav-item @if (Request::segment(3) == 'stories') active @endif">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/stories') }}">Backlog</a>
      </li>
      @if (Request::segment(3) == 'sprints' && Request::segment(5) == 'taskboard')
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/sprints') }}">Planning</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/taskboard') }}">Taskboard</a>
      </li>
      @elseif (Request::segment(3) == 'sprints')
      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Planning
        </a>
        <div class="dropdown-menu" >
          <a class="dropdown-item" href="{{ url('/projects/'.$project.'/sprints/create') }}">Create Sprint</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/taskboard') }}">Taskboard</a>
      </li>
      @else
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/sprints') }}">Planning</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/projects/'.$project.'/taskboard') }}">Taskboard</a>
      </li>
      @endif
      @endif
    </ul>
  </div>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      @guest
      @if (Route::has('login'))
      <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
      </li>
      @endif
      @if (Route::has('register'))
      <li class="nav-item">
        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
      </li>
      @endif
      @else
      <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
          {{ Auth::user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </li>
      @endguest
    </ul>
  </div>
</nav>
@endsection
