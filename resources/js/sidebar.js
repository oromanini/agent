$(function () {

    $('.side-logo').on('click', function () {
        window.open('/', '_self');
    });

    const url = window.location.href;
    const selected = $(`a[href$="${url}"]:first`);

    if (selected[0] === undefined) {
        $('#side-home').addClass('side-active');
    }

    selected.parent('li').addClass('side-active');
});
