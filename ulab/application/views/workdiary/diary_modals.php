<!-- создание записи для журнала -->
<form method="post" action="/ulab/workDiary/create" id="create_row"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Добавить</h3>
    <div class="line-dashed-small"></div>

    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="inspection_date_input" class="col-form-label">Дата проверки</label>
        </div>
        <div class="col-sm-6">
            <input required type="date" id="inspection_date_input" name="inspection_date" class="form-control justify-content-between" style="background: white;"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="сontractor_input" class="col-form-label">Подрядчик</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="сontractor" id="сontractor_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['contractors_handbook'] as $contractor) { ?>
                    <option value="<?= $contractor['id'] ?>"><?= $contractor['contractor_name'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="project_input" class="col-form-label">Проект</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="project" id="project_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['project_data'] as $project) { ?>
                    <option value="<?= $project['section_number'] ?>"><?= $project['section_number'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="work_type_input" class="col-form-label">Вид работ</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="work_type" id="work_type_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="constructive_name_input" class="col-form-label">Наименование конструктива</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="constructive_name" id="constructive_name_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="kind_of_work_input" class="col-form-label">Тип работы</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="kind_of_work" id="kind_of_work_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['kind_of_work_handbook'] as $work) { ?>
                    <option value="<?= $work['id'] ?>"><?= $work['name'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="coordinates_input" class="col-form-label">В осях/координаты</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="coordinates_input" name="coordinates" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="acceptance_results_input" class="col-form-label">Результаты приемки</label>
        </div>
        <div class="col-sm-6">
            <select required id="acceptance_results_input" name="acceptance_results"
                    class="form-control justify-content-between">
                <option value="0">Не принято</option>
                <option value="1">Принято</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="precepts_input" class="col-form-label">Предписания</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="precepts_input" name="precepts"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="сontent_of_remarks_input" class="col-form-label">Содержание замечаний</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="сontent_of_remarks_input" name="сontent_of_remarks"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="photo_input" class="col-form-label">Фото</label>
        </div>
        <div class="col-sm-6">
            <input required type="file" accept="image/*" id="photo_input" name="photo"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="technical_regulations_input" class="col-form-label">Пункт ТР НПА ПД</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="technical_regulations_input" name="technical_regulations"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="elimination_date_input" class="col-form-label">Дата устранения замечаний</label>
        </div>
        <div class="col-sm-6">
            <input required type="date" id="elimination_date_input" name="elimination_date"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="elimination_remark_input" class="col-form-label">Отметка об устранении замечаний</label>
        </div>
        <div class="col-sm-6">
            <input type="checkbox" id="elimination_remark_input" name="elimination_remark"
                   class="form-check justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="note_input" class="col-form-label">Примечание</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="note_input" name="note"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="volume" class="col-form-label">Объем</label>
        </div>
        <div class="col-sm-6">
            <input required type="number" id="volume" name="volume" min="0" step="0.01"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="responsible_for_acceptance_input" class="col-form-label">Ответственный ФИО по приемке</label>
        </div>
        <div class="col-sm-6">
            <select required id="responsible_for_acceptance_input" name="responsible_for_acceptance"
                    class="form-control justify-content-between">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['responsible_for_acceptance_handbook'] as $responsible) { ?>
                    <option value="<?= $responsible['id'] ?>"><?= $responsible['name'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>


    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- справочник с подрядчиками -->
<form method="post" action="#" id="contractor_handbook"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Подрядчики</h3>
    <div class="line-dashed-small"></div>

    <button data-js-create-contractor type="button" class="btn btn-primary">Добавить подрядчика</button>

    <div class="scroll mt-3 mb-3" style="position: relative">
        <div class="table-wrap">
            <table id="contractor-handbook-table" class="table table-striped journal" style="min-width: 100%">
                <thead>
                <tr class="table-light">
                    <th class="text-center">Наименование</th>
                    <th class="text-center"></th>
                </tr>

                </thead>
            </table>
            <div class="line-dashed"></div>
        </div>
    </div>

    <div class="line-dashed-small"></div>

<!--    <button type="submit" class="btn btn-primary">Отправить</button>-->
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- add contractor -->
<form method="post" action="/ulab/workDiary/createContractor" id="create-contractor-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Добавить подрядчика</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="contractor_name_input" class="form-label">Ф.И.О.</label>
            <input id="contractor_name_input" name="contractor_name" type="text" class="form-control"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>


<!-- edit contractor -->
<form method="post" action="/ulab/workDiary/editContractor" id="edit-contractor-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактировать подрядчика</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="contractor_name_input" class="form-label">Ф.И.О.</label>
            <input id="contractor_name_input" name="contractor_name" type="text" class="form-control"/>
            <input type="hidden" name="contractor_id" id="contractor_id" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- справочник с типами работ -->
<form method="post" action="#" id="kindofwork_handbook"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Типы работ</h3>
    <div class="line-dashed-small"></div>

    <button data-js-create-kindofwork type="button" class="btn btn-primary">Добавить тип работ</button>

    <div class="scroll mt-3 mb-3" style="position: relative">
        <div class="table-wrap">
            <table id="kindofwork-handbook-table" class="table table-striped journal" style="min-width: 100%">
                <thead>
                <tr class="table-light">
                    <th class="text-center">Наименование</th>
                    <th class="text-center"></th>
                </tr>

                </thead>
            </table>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <!--    <button type="submit" class="btn btn-primary">Отправить</button>-->
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- add kind of work -->
<form method="post" action="/ulab/workDiary/createKindOfWork" id="create-kindofwork-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Добавить тип работы</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="kindofwork_name_input" class="form-label">Наименование</label>
            <input id="kindofwork_name_input" name="name" type="text" class="form-control"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- edit kind of work -->
<form method="post" action="/ulab/workDiary/editKindOfWork" id="edit-kindofwork-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактировать тип работы</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="kindofwork_name_input" class="form-label">Наименование</label>
            <input id="kindofwork_name_input" name="name" type="text" class="form-control"/>
            <input id="kind_of_work_id" name="kind_of_work_id" type="hidden" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- справочник с ответственными по приемке -->
<form method="post" action="#" id="responsibleforacceptance_handbook"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Ответственные по приемке</h3>
    <div class="line-dashed-small"></div>

    <button data-js-create-responsibleforacceptance type="button" class="btn btn-primary">Добавить ответственного</button>

    <div class="scroll mt-3 mb-3" style="position: relative">
        <div class="table-wrap">
            <table id="responsibleforacceptance-handbook-table" class="table table-striped journal" style="min-width: 100%">
                <thead>
                <tr class="table-light">
                    <th class="text-center">Ф.И.О</th>
                    <th class="text-center"></th>
                </tr>

                </thead>
            </table>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <!--    <button type="submit" class="btn btn-primary">Отправить</button>-->
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- add ответственного по приемке -->
<form method="post" action="/ulab/workDiary/createResponsibleForAcceptance" id="create-responsible-for-acceptance-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Добавить ответственного</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="responsibleforacceptance_name_input" class="form-label">Ф.И.О.</label>
            <input id="responsibleforacceptance_name_input" name="name" type="text" class="form-control"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- edit ответственного по приемке -->
<form method="post" action="/ulab/workDiary/editResponsibleForAcceptance" id="edit-responsible-for-acceptance-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактировать ответственного</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="responsibleforacceptance_name_input" class="form-label">Ф.И.О.</label>
            <input id="responsibleforacceptance_name_input" name="name" type="text" class="form-control"/>
            <input id="responsible_id" name="responsible_id" type="hidden" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<!-- редактирование журнала -->
<form method="post" action="/ulab/workDiary/edit" id="edit_row"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактирование <span id="order_number"></span></h3>
    <div class="line-dashed-small"></div>
    <input type="hidden" name="row_id" id="row_id" />

    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="inspection_date_input" class="col-form-label">Дата проверки</label>
        </div>
        <div class="col-sm-6">
            <input required type="date" id="inspection_date_input" name="inspection_date" class="form-control justify-content-between" style="background: white;"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="сontractor_input" class="col-form-label">Подрядчик</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="сontractor" id="сontractor_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="project_input" class="col-form-label">Проект</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="project" id="project_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="work_type_input" class="col-form-label">Вид работ</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="work_type" id="work_type_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="constructive_name_input" class="col-form-label">Наименование конструктива</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="constructive_name" id="constructive_name_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="kind_of_work_input" class="col-form-label">Тип работы</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="kind_of_work" id="kind_of_work_input">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="coordinates_input" class="col-form-label">В осях/координаты</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="coordinates_input" name="coordinates" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="acceptance_results_input" class="col-form-label">Результаты приемки</label>
        </div>
        <div class="col-sm-6">
            <select required id="acceptance_results_input" name="acceptance_results"
                    class="form-control justify-content-between">
                <option value="0">Не принято</option>
                <option value="1">Принято</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="precepts_input" class="col-form-label">Предписания</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="precepts_input" name="precepts"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="сontent_of_remarks_input" class="col-form-label">Содержание замечаний</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="сontent_of_remarks_input" name="сontent_of_remarks"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="photo_input" class="col-form-label">Фото</label>
        </div>
        <div class="col-sm-6">
            <div class="block">
                <a id="photo_link" href="" target="_blank"></a>
                <div class="image" style="z-index: 9999;">
                    <img id="photo_path" src="" width="300" height="400" alt="Фото">
                </div>
            </div>
            <input type="file" accept="image/*" id="photo_input" name="photo"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="technical_regulations_input" class="col-form-label">Пункт ТР НПА ПД</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="technical_regulations_input" name="technical_regulations"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="elimination_date_input" class="col-form-label">Дата устранения замечаний</label>
        </div>
        <div class="col-sm-6">
            <input required type="date" id="elimination_date_input" name="elimination_date"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="elimination_remark_input" class="col-form-label">Отметка об устранении замечаний</label>
        </div>
        <div class="col-sm-6">
            <input type="checkbox" id="elimination_remark_input" name="elimination_remark"
                   class="form-check justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="note_input" class="col-form-label">Примечание</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="note_input" name="note"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="volume" class="col-form-label">Объем</label>
        </div>
        <div class="col-sm-6">
            <input required type="number" id="volume" name="volume" min="0" step="0.01"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="responsible_for_acceptance_input" class="col-form-label">Ответственный ФИО по приемке</label>
        </div>
        <div class="col-sm-6">
            <select required id="responsible_for_acceptance_input" name="responsible_for_acceptance"
                    class="form-control justify-content-between">
                <option value="0">Выбрать</option>
            </select>
        </div>
    </div>


    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>
