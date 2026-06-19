$(function ($) {
    $('.grain__gost_list').select2()

    $('#add-new-sieve').on('click', function () {
        $.magnificPopup.open({
            items: {
                src: '#new_sieve',
                type: 'inline',
            },
            fixedContentPos: false,
            closeOnBgClick: false
        })
    })
})