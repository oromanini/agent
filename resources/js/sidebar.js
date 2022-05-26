$(function(){

    const sideNav = $('.side-navigation');

    sideNav.mouseover(function (){

        const sidebarColumn = $('.sidebar-column');
        const contentColumn = $('.content-column');

        $('.side-logo-img').attr('src', '/img/logo/alluz-horizontal.png').css('transition', 'width 0.3s').attr('width', 280)

        sidebarColumn.removeClass('is-1')
        contentColumn.removeClass('is-11')

        sidebarColumn.addClass('is-2').css('transition', 'width 0.5s')
        contentColumn.addClass('is-10').css('transition', 'width 0.5s')
    });

    sideNav.mouseout(function (){

        const sidebarColumn = $('.sidebar-column');
        const contentColumn = $('.content-column');

        $('.side-logo-img').attr('src', '/img/logo/alluz-icon.png').attr('width', 60)

        sidebarColumn.removeClass('is-2')
        contentColumn.removeClass('is-10')

        sidebarColumn.addClass('is-1').css('transition', 'width 0.5s')
        contentColumn.addClass('is-11').css('transition', 'width 0.5s')

    });

    $('.side-logo-img').on('click', function() {
        window.open ('/', '_self')
    });

});
