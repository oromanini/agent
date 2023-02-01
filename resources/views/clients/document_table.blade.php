<div class="box">
    <div class="columns">
        <div class="column">
            <div class="column is-10"><h4 class="title is-4">Documento do cliente</h4></div>

        </div>
        <div class="column">
            <a href="/storage/{{ str_replace('public/', '', $client->owner_document) }}"
               class="button is-primary" target="_blank">
                <ion-icon name="eye-outline"></ion-icon>
                Visualizar Documento</a>
        </div>
    </div>
</div>
