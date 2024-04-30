@if(session()->get('message'))
    <article class="mymessage message @if(isset(session()->get('message')['0']) && session()->get('message')['0'] == 'success') is-success @else is-warning @endif">
        <div class="message-header">
            <p>{{ isset(session()->get('message')['0']) && session()->get('message')['0'] == 'success' ? 'Sucesso :)' : 'Atenção!'}}</p>
            <button class="delete" aria-label="delete"></button>
        </div>
        <div class="message-body">
            {{ isset(session()->get('message')[1]) ? session()->get('message')[1] : session()->get('message')['success'] }}
        </div>
    </article>
@endif
