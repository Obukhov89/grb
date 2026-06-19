<div class="secondment-wrapper">
    <h2 class="d-flex mb-3">
        <span class="px-2 py-1">
                Архив заявки №<?= $this->data["secondment_id"] ?> от <?= $this->data["created_at"] ?>
        </span>
    </h2>

    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <header class="panel-heading">
            Общая информация

            <span class="tools float-end">
                              <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                              <a href="javascript:;" class="fa fa-chevron-up"></a>
                          </span>
          </header>
          <div class="panel-body">
            <div class="form-info" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">
              <div class="">
                <div class="info-wrapper wrapper-shadow mb-4">
                  <strong class="mb-2 d-block">Данные сотрудника</strong>
                  <div class="row mb-2">
                    <div class="form-group col-sm-6">
                      <label for="user">Сотрудник <span class="redStars">*</span></label>
                      <input type="text" class="form-control" value="<?= $this->data["user_name"] ?>" readonly>
                    </div>
                    <div class="form-group col-sm-6">
                      <lable for="workPosition">Должность</lable>
                      <input type="text" class="form-control work-position" id="workPosition"
                             value="<?= $this->data['work_position'] ?>" readonly>
                    </div>
                  </div>

                  <strong class="mb-2 d-block">Место назначение</strong>

                  <div class="row mb-2">
                    <div class="form-group col-sm-5">
                      <label for="city">Населенный пункт <span class="redStars">*</span></label>
                      <input type="text" class="form-control" value="<?= $this->data["city"] ?>" readonly>

                    </div>
                    <div class="form-group col-sm-5">
                      <label for="object">Объект</label>
                      <input type="text" class="form-control" value="<?= $this->data["object"] ?>" readonly>

                    </div>
                    <!--TODO: Уточнить про "Километраж" и доработать-->
                    <div class="form-group col-sm-2">
                      <lable for="kilometer">Км <span class="redStars">*</span></lable>
                      <input type="text" class="form-control" value="<?= $this->data["km"] ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="form-group col-sm-6">
                      <label for="company">Клиент <span class="redStars">*</span></label>
                      <input type="text" class="form-control" value="<?= $this->data["company"] ?>" readonly>

                    </div>

                    <div class="form-group col-sm-6">
                      <label for="contract">Договор</label>
                      <input type="text" class="form-control" value="<?= $this->data["contract"] ?>" readonly>
                    </div>
                  </div>

                  <strong class="mb-2 d-block">Дата</strong>

                  <div class="row mb-2">
                    <div class="form-group col-sm-5">
                      <lable for="dateBeginning">Дата начала<span class="redStars">*</span></lable>
                      <input type="date" class="form-control date-begin" id="dateBeginning"
                             name="date_begin"
                             value="<?= $this->data['date_begin'] ?>" readonly>
                    </div>
                    <div class="form-group col-sm-5">
                      <lable for="dateEnding">Дата окончания<span class="redStars">*</span>
                      </lable>
                      <input type="date" class="form-control date-ending" id="dateEnding"
                             name="date_end" value="<?= $this->data['date_end'] ?>" readonly>
                    </div>
                    <div class="form-group col-sm-2">
                      <lable for="totalDays">Всего</lable>
                      <input type="text" class="form-control" value="<?= $this->data['total_days'] ?>" readonly>
                    </div>
                  </div>

                  <div class="row mb-2">

                    <div class="form-group col-sm-12">
                      <strong class="mb-2 d-block">Цель</strong>
                      <lable for="content">Содержание задания</lable>
                      <textarea class=" mw-100 content min-h-180" id="content" rows="6" readonly
                                name="content"><?= $this->data['content'] ?>
                      </textarea>
                    </div>
                  </div>

                  <div class="row mb-2">

                    <div class="form-group col-sm-6" >
                      <strong class="mb-2 d-block">Транспорт</strong>
                      <div class="mb-2">
                        <input type="text" class="form-control" value="<?= $this->data['transport'] ?>" readonly>

                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <strong class="mb-2 d-block">Комментарий</strong>
                      <textarea class=" mw-100 content min-h-180" id="content" rows="6"
                                name="comment"><?= $this->data['comment'] ?></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="">
                <div class="">
                  <div class="extra-wrapper wrapper-shadow mb-4" data-js-plan-card>
                    <strong class="mb-2 d-block">Запланированные расходы</strong>
                    <input type="text" name="file_payment_delete" value="" style="display: none">
                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="ticketPrice">Билеты</lable>
                        <input type="text" class="form-control ticket-price cost" readonly
                               value="<?= $this->data['ticket_price'] ?>">
                      </div>
                      <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPrice"
                                                    name="comment_ticket_price"
                                                    title="<?= trim($this->data['ticket_price_comment']) ?>"
                                          ><?= trim($this->data['ticket_price_comment']) ?></textarea>
                      </div>
                      <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group>



                        <?php foreach ($this->data["fileArr"]["ticket_payment"] as $ticket): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/archive/ticket_payment/<?= $this->data["id"] ?>/<?= $ticket ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $ticket ?>"
                              data-js-file-download
                              download
                            ></a>
                          </div>
                        <?php endforeach; ?>

                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                          <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                              <?= count($this->data["fileArr"]["ticket_payment"]) ?>
                          </button>
                        </div>

                      </div>

                    </div>
                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="gasolineConsumption">Расход бензина</lable>
                        <input type="text" class="form-control gasoline-consumption cost" readonly
                               value="<?= $this->data['gasoline_consumption'] ?>">
                      </div>
                      <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumption"
                                                name="comment_gasoline_consumption"><?= $this->data['gasoline_consumption_comment'] ?></textarea>
                      </div>
                      <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                          <?php foreach ($this->data["fileArr"]["fuel_payment"] as $i => $file): ?>
                            <div data-js-file-wrap class="position-relative">
                              <a
                                class="btn btn-primary position-relative rounded fa-solid fa-file"
                                href="/ulab/upload/secondment/archive/fuel_payment/<?= $this->data["id"] ?>/<?= $file ?>"
                                target="_blank"
                                style="margin-left: 4px; font-size: 16px;"
                                title="<?= $file ?>"
                                data-js-file-download
                                download
                              ></a>
                            </div>
                          <?php endforeach; ?>

                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                          <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                              <?= count($this->data["fileArr"]["fuel_payment"]) ?>
                          </button>
                        </div>

                      </div>
                    </div>

                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="gasolineConsumption">Бензин по объекту</lable>
                        <input type="text" class="form-control gasoline-consumption cost" readonly
                               value="<?= $this->data['gasoline_consumption_object'] ?>">
                      </div>
                      <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumption"
                                                name="comment_gasoline_consumption"><?= $this->data['gasoline_consumption_object_comment'] ?></textarea>
                      </div>
                      <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                          <?php foreach ($this->data["fileArr"]["fuel_payment_object"] as $i => $file): ?>
                            <div data-js-file-wrap class="position-relative">
                              <a
                                class="btn btn-primary position-relative rounded fa-solid fa-file"
                                href="/ulab/upload/secondment/archive/fuel_payment_object/<?= $this->data["id"] ?>/<?= $file ?>"
                                target="_blank"
                                style="margin-left: 4px; font-size: 16px;"
                                title="<?= $file ?>"
                                data-js-file-download
                                download
                              ></a>
                            </div>
                          <?php endforeach; ?>

                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                          <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                              <?= count($this->data["fileArr"]["fuel_payment"]) ?>
                          </button>
                        </div>

                      </div>
                    </div>

                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="perDiem">Суточные</lable>
                        <input type="text" class="form-control per-diem cost" readonly
                               value="<?= $this->data['per_diem'] ?>">
                      </div>
                      <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiem"
                                                    name="comment_per_diem"><?= trim($this->data['per_diem_comment']) ?>
                                          </textarea>
                      </div>
                      <div class="form-group col-sm-5 d-flex align-items-end align-items-end" data-js-btn-group >
                          <?php foreach ($this->data["fileArr"]["per_diem"] as $file): ?>
                            <div data-js-file-wrap class="position-relative">
                              <a
                                class="btn btn-primary position-relative rounded fa-solid fa-file"
                                href="/ulab/upload/secondment/archive/per_diem/<?= $this->data["id"] ?>/<?= $file ?>"
                                target="_blank"
                                style="margin-left: 4px; font-size: 16px;"
                                title="<?= $file ?>"
                                data-js-file-download
                                download
                              ></a>
                            </div>
                          <?php endforeach; ?>

                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                          <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                              <?= count($this->data["fileArr"]["per_diem"]) ?>
                          </button>
                        </div>

                      </div>


                    </div>
                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="accommodation">Проживание</lable>
                        <input type="text" class="form-control accommodation cost" readonly
                               value="<?= $this->data['accommodation'] ?>">
                      </div>
                      <div class="form-group col-sm-4">
                                      <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodation"
                                                name="comment_accommodation"><?= $this->data['accommodation_comment'] ?></textarea>
                      </div>

                      <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                          <?php foreach ($this->data["fileArr"]["accommodation"] as $file): ?>
                            <div data-js-file-wrap class="position-relative">
                              <a
                                class="btn btn-primary position-relative rounded fa-solid fa-file"
                                href="/ulab/upload/secondment/archive/accommodation/<?= $this->data["id"] ?>/<?= $file ?>"
                                target="_blank"
                                style="margin-left: 4px; font-size: 16px;"
                                title="<?= $file ?>"
                                data-js-file-download
                                download
                              ></a>
                            </div>
                          <?php endforeach; ?>

                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                          <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                              <?= count($this->data["fileArr"]["accommodation"]) ?>
                          </button>
                        </div>


                      </div>
                    </div>

                    <lable for="other">Прочее</lable>
                      <?php foreach ($this->data['other'] as $i => $field): ?>
                        <div class="row mb-2 align-items-end overflow-hidden">
                          <div class="form-group col-sm-3">

                            <input type="number" name="other_id[]" class="d-none">
                            <input type="number" class="form-control other cost" id="other"
                                   name="other[]"
                                   min="0" step="0.01"
                                   data-js-format-money
                                   autocomplete="off"
                                   value="<?= $field["sum"] ?>">
                          </div>
                          <div class="form-group col-sm-4">
                                      <textarea class="form-control mw-100 comment-other" id="commentOther"
                                                name="comment_other[]"><?= $field['comment'] ?></textarea>
                          </div>
                          <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >

                              <?php foreach ($this->data["fileArr"]["other"][$i] as $file): ?>
                                <div data-js-file-wrap class="position-relative">
                                  <a
                                    class="btn btn-primary position-relative rounded fa-solid fa-file"
                                    href="/ulab/upload/secondment/archive/other/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                    target="_blank"
                                    style="margin-left: 4px; font-size: 16px;"
                                    title="<?= $file ?>"
                                    data-js-file-download
                                    download
                                  ></a>
                                  <button
                                    data-js-delete-payment-file="/ulab/upload/secondment/other/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                    type="button" class="position-absolute fa-solid fa-xmark"
                                    style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                  </button>
                                </div>
                              <?php endforeach; ?>

                            <div data-js-upload-wrap class="position-relative btn-count-wrap">
                              <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                  <?= count($this->data["fileArr"]["other"][$i]) ?>
                              </button>
                            </div>

                          </div>
                        </div>
                      <?php endforeach; ?>

                    <div class="row mb-2 align-items-end overflow-hidden">
                      <div class="form-group col-sm-3">
                        <lable for="plannedExpenses">Итого</lable>
                        <input type="text" class="form-control planned-expenses"
                               value="<?= $this->data['planned_expenses'] ?>" readonly>
                      </div>
                      <div class="form-group col-sm-9">
                                      <textarea class="form-control mw-100 comment-planned-expenses"
                                                id="commentPlannedExpenses"
                                                name="comment_planned_expenses"><?= $this->data['comment_planned_expenses'] ?></textarea>
                      </div>
                    </div>


                  </div>
                </div>
              </div>
            </div>

          </div>
          <!--./panel-body-->
        </div>
        <!--./panel-->
      </div>
      <!--./col-md-12-->
    </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <header class="panel-heading">
          Приказ и служебное задание
          <span class="tools float-end">
                        <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                        <a href="javascript:;" class="fa fa-chevron-down"></a>
                    </span>
        </header>
        <div class="panel-body "> <!--panel-hidden -->
          <div class="form-upload-files">
            <div class="documents-wrapper">
              <div class="row mb-4">
                <div class="row">
                  <input type="text" name="file_delete" value="" hidden>
                  <div class="d-flex align-items-center col-md-2 mb-2">
                    <label for="edictNumber">№&nbsp;приказа</label>
                    <input type="text" class="form-control" name="edict_number" style="margin-left: 5px" value="<?= $this->data["edict_number"] ?? "" ?>">
                  </div>

                  <div data-js-files class="d-flex align-items-center">
                    <div>Приказ</div>
                    <div data-js-file-wrap class="position-relative">
                      <a
                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                        href="<?= $this->data['fileArr']['edict'][0] ?>"
                        target="_blank"
                        style="margin-left: 5px; font-size: 16px"
                        title="<?= $this->data['fileArr']['edict'][0] ?>"
                        download

                      ></a>
                    </div>
                  </div>

                  <div data-js-files class="d-flex align-items-center">
                    <div>Служебное задание</div>
                    <div data-js-file-wrap class="position-relative">
                      <a
                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                        href="<?= $this->data['fileArr']['service_assignment'] ?><?= $this->data['fileArr']['service_assignment'] ?>?v=<?= rand() ?>"
                        target="_blank"
                        style="margin-left: 5px; font-size: 16px"
                        title="<?= $this->data['service_assignment']['file'] ?>"
                        download
                      ></a>
                    </div>
                  </div>

                </div>
              </div>
                <?php if ($this->data['is_may_save_files']): ?>
                    <?php if ($this->data['stage_name'] === 'Подготовка приказа и СЗ'): ?>
                    <div class="d-flex gap-2">
                      <div>
                        <button data-js-save-files type="submit" class="btn btn-primary min-w-200" id="saveUploadFiles"
                                name="save_upload_files" data-stage="Готова">Сохранить файлы
                        </button>
                      </div>
                      <div>
                        <button data-js-save-files type="submit" class="btn btn-primary min-w-200" id="saveUploadFiles"
                                name="stage_ready" data-stage="Готова">Готово
                        </button>
                      </div>
                    </div>

                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!-- /.documents-wrapper -->
          </div>
        </div>
        <!--./panel-body-->
      </div>
      <!--./panel-->
    </div>
    <!--./col-md-12-->
  </div>
  <!--./row-->
</div>