$(function () {
    let $body = $('body')

    $('.select2').select2({
        theme: 'bootstrap-5'
    })

    $('#r1').click(function () {
        $('.text-def-1').text('от')
        $('.text-def-2').text('до')
    })

    $('#r2').click(function () {
        $('.text-def-1').text('до')
        $('.text-def-2').text('от')
    })

    let countAddedMaterial = $('.group-norm').length
    $body.on("click", "#add_group_norm", function () {
        let $formGroupContainer = $(this).parents('.material-block').find('.group-norm:last-child')
        countAddedMaterial++

        $formGroupContainer.after(
            `<div class="form-group row group-norm">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">Группа материала</span>
                        <input type="text" class="form-control" name="form[dop_v][${countAddedMaterial}]" value="">
                        <span class="input-group-text">Нормы от</span>
                        <input type="number" class="form-control" step="0.001" name="form[dop_n][${countAddedMaterial}][0]" value="">
                        <span class="input-group-text">Нормы до</span>
                        <input type="number" class="form-control" step="0.001" name="form[dop_n][${countAddedMaterial}][1]" value="">
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-square remove_this" title="Удалить">
                        <i class="fa-solid fa-minus icon-fix"></i>
                    </button>
                </div>
            </div>`
        )
    })

    $('.is_output_check').change(function () {
        if ( $(this).prop('checked') ) {
            $('.norm_comment_elem').removeClass('d-none')
        } else {
            $('.norm_comment_elem').addClass('d-none')
        }
    })

})