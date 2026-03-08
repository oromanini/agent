$(function(){

    const sideNav = $('.side-navigation');

    sideNav.on('mouseenter', function (){

        const sidebarColumn = $('.sidebar-column');
        const contentColumn = $('.content-column');

        sideNav.addClass('side-expanded');

        $('.side-logo-img').attr('src', '/img/logo/alluz-horizontal.png').css('transition', 'width 0.25s').attr('width', 180)

        sidebarColumn.removeClass('is-1')
        contentColumn.removeClass('is-11')

        sidebarColumn.addClass('is-2').css('transition', 'width 0.35s')
        contentColumn.addClass('is-10').css('transition', 'width 0.35s')
    });

    sideNav.on('mouseleave', function (){

        const sidebarColumn = $('.sidebar-column');
        const contentColumn = $('.content-column');

        sideNav.removeClass('side-expanded');

        $('.side-logo-img').attr('src', '/img/logo/alluz-icon.png').attr('width', 46)

        sidebarColumn.removeClass('is-2')
        contentColumn.removeClass('is-10')

        sidebarColumn.addClass('is-1').css('transition', 'width 0.35s')
        contentColumn.addClass('is-11').css('transition', 'width 0.35s')

    });

    $('.side-logo-img').on('click', function() {
        window.open ('/', '_self')
    });


    let url = window.location.href;

    let selected = $(`a[href$="${url}"]:first`)

    if (selected[0] === undefined) {
        $('#side-home').addClass('side-active')
    }

    selected.parent('li').addClass('side-active')
});
