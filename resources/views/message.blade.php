@if(session()->get('message'))

@dd(session()->get('message'))
    <article class="mymessage message @if(isset(session()->get('message')['0']) && session()->get('message')['0'] == 'success') is-success @else is-error @endif">
        <div class="message-header">
            <p>{{ isset(session()->get('message')['0']) && session()->get('message')['0'] == 'success' ? 'Sucesso :)' : 'Erro :('}}</p>
            <button class="delete" aria-label="delete"></button>
        </div>
        <div class="message-body">
            {{ session()->get('message')[1]}}
        </div>
    </article>
@endif
