$(function () {
    $('input[type="checkbox"]').change(function(e) {

        let checked     = $(this).prop("checked"),
            container   = $(this).parent(),
            siblings    = container.siblings();

        console.log(checked)

        container.find('input[type="checkbox"]').prop({
            indeterminate: false,
            checked: checked
        });

        checkSiblings(container, checked)
    });

    $('.method-name').each(function () {
        checkSiblings($(this).parent(), $(this).prop("checked"))
    })
})

function checkSiblings(el, checked) {

    let parent = el.parent().parent(),
        all = true;

    el.siblings().each(function() {
        return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked)
    });

    if (all && checked) {

        parent.children('input[type="checkbox"]').prop({
            indeterminate: false,
            checked: checked
        });

        checkSiblings(parent, checked);

    } else if (all && !checked) {

        parent.children('input[type="checkbox"]').prop("checked", checked);
        parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
        checkSiblings(parent, checked);

    } else {

        el.parents("li").children('input[type="checkbox"]').prop({
            indeterminate: true,
            checked: false
        });

    }
}