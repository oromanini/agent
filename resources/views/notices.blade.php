<div class="modal is-active" id="notices">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Novidades!</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body p-0">

            <a href="https://prezi.com/view/DS2MyzPGK0mnHiwvkjH4/" target="_blank">
                <img id="notice_image" src="/img/notices/1.png" alt="">
            </a>

        </section>
        <footer class="modal-card-foot">
            <button class="button is-warning">fechar</button>
        </footer>
    </div>
</div>

<script>

    $(function () {


        let images = ["/img/notices/2.png", "/img/notices/1.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png", "/img/notices/2.png", "/img/notices/1.png",]
        let time = 6000

        $.each(images, function (key, item) {
            setTimeout(function () {
                $('#notice_image').attr('src', item)
            }, time)
            time += 6000;
        })

    })

</script>
