<nav class="panel">
    <p class="panel-heading">Componentes do kit gerador fotovoltaico</p>
    @foreach($lead->components() as $component)
        <a class="panel-block is-active">
            <span class="panel-icon">
              <i class="fas fa-book" aria-hidden="true"></i>
            </span>
            {{$component}}
        </a>
    @endforeach
</nav>
