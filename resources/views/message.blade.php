@if(session()->get('message'))
    <article class="mymessage message is-success">
        <div class="message-header">
            <p>{{session()->get('message')[0] == 'success' ? 'Sucesso :)' : 'Erro :('}}</p>
            <button class="delete" aria-label="delete"></button>
        </div>
        <div class="message-body">
            {{ session()->get('message')[1] }}
        </div>
    </article>
@endif
