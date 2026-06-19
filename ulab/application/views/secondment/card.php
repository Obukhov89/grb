
<div class="secondment-wrapper">
    <header class="header-secondment mb-4">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link link-back" href="/ulab/secondment/list/" title="Вернуться назад">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="/ulab/assets/images/icons.svg#back"></use>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-list" target="_blank" href="/ulab/upload/secondment/Инструкция командировки.pdf" title="Инструкция">
                        <i class="fa-solid fa-file-lines"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <h2 class="d-flex mb-3">
        <span class="rounded border px-2 py-1 me-1 secondment-stage
            <?= $this->data['stage_border_color'] ?>"><?= $this->data['stage_name'] ?></span>
        <span class="px-2 py-1">
            <?= $this->data['title'] ?>
        </span>
    </h2>


    <?php if ($this->data['is_can_prepare_report'] && in_array($this->data['stage_name'], ['Согласована', 'В командировке'])): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <strong class="mb-2 d-block">Изменить стадию</strong>

                        <div class="row">
                            <div class="col-sm-6 text-nowrap">
                                <button type="button" class="btn btn-primary w-100 mw-100" id="isSecondment"
                                        name="is_secondment" data-stage="В командировке">
                                    В командировке
                                </button>
                            </div>
                            <div class="col-sm-6 text-nowrap">
                                <button type="button" class="btn btn-primary w-100 mw-100" id="preparingReport"
                                        name="preparing_report" data-stage="Подготовка отчета">
                                    Подготовка отчета
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--./panel-body-->
                </div>
                <!--./panel-->
            </div>

            <!--./col-md-12-->
        </div>

        <!--./row-->
    <?php endif; ?>

    <?php if (!empty($this->data['secondment']["cancel_comment"]) && $this->data['stage_name'] == "Отменена"):?>
        <div class="info-wrapper wrapper-shadow mb-4">
            <strong class="mb-2 d-block">Причина Отмены</strong>
            <div><?= $this->data['secondment']["cancel_comment"] ?></div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Общая информация
                    <?= empty($this->data['secondment']['s_id']) ? '<i class="fa fa-circle text-light-red"></i>' : '' ?>
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="javascript:;" class="fa fa-chevron-up"></a>
                        </span>
                </header>
                <div class="panel-body">
                    <form action="/ulab/secondment/insertUpdateInfo/" class="form-info" id="formInfo" method="post"
                          enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">
                        <?php if (!empty($this->data['secondment']['s_id'])): ?>
                            <input class="secondment-id" type="hidden" name="secondment_id"
                                   value="<?= $this->data['secondment']['s_id'] ?? '' ?>">
                        <?php endif; ?>


                        <div class="info-wrapper wrapper-shadow mb-4">
                          <strong class="mb-2 d-block">Данные сотрудника</strong>

                          <div class="row mb-2">
                              <div class="form-group col-sm-6">
                                  <label for="user">Сотрудник <span class="redStars">*</span></label>
                                  <select name="user_id"
                                          class="form-control h-auto user m_t_n-1"
                                          id="user"
                                          required

                                  >
                                      <option value="" selected disabled></option>
                                      <?php foreach ($this->data['users'] as $val): ?>
                                          <option value="<?= $val['ID'] ?>"
                                              <?= $val['ID'] === $this->data['user_id'] ? 'selected' : '' ?>>
                                              <?= $val['LAST_NAME'] ?> <?= $val['NAME'] ?> <?= $val['SECOND_NAME'] ?>
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
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
<!--                                    <input id="city" type="text" class="form-control" value="--><?//= $this->data['settlement_title'] ?><!--">-->
                                  <select
                                          name="settlement_id"
                                          class="form-control h-auto object"
                                          id="city"
                                          style="overflow: hidden"
                                          required
                                          <?= empty($this->data['objects'][$this->data['object_id']]["settlement_id"]) ? "" : "readonly" ?>
                                  >
<!--                                      <option value="" disabled></option>-->
                                      <option value="<?= $this->data['objects'][$this->data['object_id']]["settlement_id"] ?>" selected>
                                          <?= $this->data['objects'][$this->data['object_id']]["settlement"] ?>
                                      </option>
                                  </select>
                              </div>
                              <div class="form-group col-sm-4">
                                  <label for="object">Объект</label>
                                  <select name="object_id" class="form-control h-auto object" id="object" aria-hidden="true">
                                      <option value="" selected disabled></option>
                                      <?php foreach ($this->data['objects'] as $val): ?>
                                          <option value="<?= $val['ID'] ?>"
                                                  data-js-city="<?= $val["settlement"] ?>"
                                                  data-js-city-id="<?= $val["settlement_id"] ?>"
                                                  data-js-km="<?= $val["KM"] ?>"
                                              <?= $val['ID'] === $this->data['object_id'] ? 'selected' : '' ?>>
                                              <?= $val['NAME'] ?>
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              <!--TODO: Уточнить про "Километраж" и доработать-->
                              <div class="form-group col-sm-3">
                                  <lable for="kilometer">Км <span class="redStars">*</span></lable>
                                  <input type="number" class="form-control kilometer m_t-1" id="kilometer" min="0"
                                         step="any"
                                         name="kilometer"
                                         required
                                         <?= empty($this->data['object']["KM"]) ? "" : "readonly" ?>
                                         value="<?= $this->data['object']["KM"] ?>">
                              </div>
                          </div>
                          <div class="row mb-2">
                              <div class="form-group col-sm-6">
                                  <label for="company">Клиент <span class="redStars">*</span></label>
                                  <input id="company" class="form-control" list="company_list" type="text"
                                         name="company" value="<?= $this->data['company']['TITLE'] ?? '' ?>"
                                         autocomplete="off" required>
                                  <input type="hidden" name="company_id" id="company-hidden"
                                         value="<?= $this->data['company']['ID'] ?? '' ?>">
                                  <datalist id="company_list">
                                      <?php if (isset($this->data['companies'])): ?>
                                          <?php foreach ($this->data['companies'] as $company): ?>
                                              <option data-value="<?= $company['ID'] ?>"><?= $company['TITLE'] ?></option>
                                          <?php endforeach; ?>
                                      <?php endif; ?>
                                  </datalist>
                              </div>

                              <div class="form-group col-sm-6">
                                  <label for="contract">Договор</label>
                                  <div class="d-flex">
                                      <input type="text" name="contract_type" value="<?= $this->data['secondment']["contract_type"] ?>" style="display: none">
                                      <select name="contract_id" id="contract-select" style="margin-right: 5px">
                                          <option ></option>
                                          <?php foreach ($this->data['contracts'] as $contract): ?>
                                              <?php if ($contract["ID"] != '1475' && $contract["ID"] != '1649' &&
                                                  $contract["ID"] != '2019' && $contract["ID"] != '2389' &&
                                                  $contract["ID"] != '2675' && $contract["ID"] != '2838'): ?>
                                                  <option
                                                    value="<?= $contract["ID"] ?>"
                                                    data-js-type="0"
                                                    data-js-file="<?= $contract["PDF"] ?>"
                                                    <?= $this->data['secondment']["contract_id"] === $contract["ID"] && $this->data['secondment']["contract_type"] == 0 ? "selected" : "" ?>
                                                  ><?= $contract["NUMBER"] ?></option>
                                              <?php endif; ?>
                                          <?php endforeach; ?>
                                         <option value="" disabled>=====</option>
                                          <?php foreach ($this->data['secondmentContracts'] as $contract): ?>
                                            <?php if ($contract["id"] != '16' && $contract["id"] != '20' &&
                                                  $contract["id"] != '26' && $contract["id"] != '32' &&
                                                  $contract["id"] != '45' && $contract["id"] != '8'): ?>
                                                    <option value="<?= $contract["id"] ?>"
                                                        data-js-type="1"
                                                        data-js-file="<?= $contract["name"] ?>"
                                                        data-js-dir="<?= "/ulab/upload/contracts/{$contract["id"]}/" ?>"
                                                        <?= $this->data['secondment']["contract_id"] === $contract["id"] && $this->data['secondment']["contract_type"] == 1 ? "selected" : "" ?>

                                                    ><?= $contract["number"] ?></option>
                                              <?php endif; ?>
                                          <?php endforeach; ?>
                                      </select>

                                      <?php if (!empty($this->data['secondment']["contract_id"]) && $this->data['secondment']["contract_type"] == 1): ?>
                                          <div data-js-file-wrap="" class="position-relative">
                                              <a class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                 id="contract-file"
                                                 href=""
                                                 target="_blank"
                                                 style="margin-right: 4px; height: 30px; margin-top: 6px"
                                                 title=""
                                                 data-js-file-download=""
                                              ></a>
                                          </div>
                                      <?php endif; ?>

                                      <div data-js-upload-wrap="" class="d-flex align-items-center">
                                        <button type="button" class="btn btn-primary rounded fa-solid fa-plus"
                                                data-js-toggle-contract=""
                                        >
                                        </button>
                                      </div>
                                  </div>




                              </div>
                              <div data-js-form-contract style="margin-left: 10px; display: none; width: 100%">
                                  <h6>Добавить договор</h6>
                                  <div >
                                      <table id="obj">
                                          <tbody id="obj_body">
                                          <tr>
                                              <td>№ договора</td>
                                              <td>
                                                  <input class="tz" type="text" name="contract_number" value="" style="width: 100%">
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Прикрепить файл</td>
                                              <td>
                                                  <input class="form-control" type="file" name="contract" value="" style="width: 100%">
                                              </td>
                                          </tr>

                                          </tbody>
                                      </table>
                                      <button type="button" class="btn btn-primary" data-js-add-contract>Сохранить</button>
                                  </div>

                              </div>

                          </div>

                          <strong class="mb-2 d-block">Дата</strong>

                          <div class="row mb-2">
                              <div class="form-group col-sm-5">
                                  <lable for="dateBeginning">Дата начала<span class="redStars">*</span>
                                  </lable>
                                  <input type="date" class="form-control date-begin" id="dateBeginning"
                                         name="date_begin"
                                         value="<?= $this->data['date_begin'] ?>" required>
                              </div>
                              <div class="form-group col-sm-5">
                                  <lable for="dateEnding">Дата окончания<span class="redStars">*</span>
                                  </lable>
                                  <input type="date" class="form-control date-ending" id="dateEnding"
                                         name="date_end" value="<?= $this->data['date_end'] ?>" required>
                              </div>
                              <div class="form-group col-sm-2">
                                  <lable for="totalDays">Всего</lable>
                                  <div class="input-group total-days-wrapper
                                  <?= $this->data['total_days'] < 0 ? 'border border-red' : '' ?>">
                                      <input type="text" class="form-control number-only total-days" id="totalDays"
                                             name="total_days" value="<?= $this->data['total_days'] ?>"
                                             aria-describedby="basic-addon2" readonly>
                                  </div>
                              </div>
                          </div>

                          <div class="row mb-2">

                              <div class="form-group col-sm-12">
                                  <strong class="mb-2 d-block">Цель</strong>
                                  <lable for="content">Содержание задания</lable>
                                  <textarea class=" mw-100 content min-h-180" id="content" rows="6"
                                            name="content"><?= $this->data['content'] ?></textarea>
                              </div>
                          </div>

                          <div class="row mb-2">
                              <div class="form-group col-sm-6" >
                                  <strong class="mb-2 d-block">Транспорт</strong>
                                  <div class="d-flex mb-2">
                                      <div style="width: 95%; margin-right: 5px;">
                                          <select name="transport" id="transport">
                                              <option
                                                      value=""
                                                      data-js-fuel-price="0"
                                                      data-js-fuel-consumption="0"
                                              ></option>
                                              <?php foreach ($this->data["vehicles"] as $index => $vehicle): ?>
                                                  <option
                                                          value="<?= $vehicle["id"] ?>"
                                                          data-js-fuel-price="<?= $vehicle["price"] ?>"
                                                          data-js-fuel-title="<?= $vehicle["title"] ?>"
                                                          data-js-fuel-consumption="<?= $vehicle["consumption_rate"] ?>"
                                                      <?= $vehicle["id"] == $this->data['vehicle_id'] ? 'selected' : '' ?>
                                                  >
                                                      <?= $vehicle["model"] ?> (<?= $vehicle["number"] ?>)
                                                  </option>
                                              <?php endforeach; ?>
                                          </select>

                                      </div>
                                      <button type="button" class="btn btn-primary rounded" data-js-toggle-transport=""><i class="fa-solid fa-plus"></i></button>
                                  </div>

                                  <div data-js-vehicle-info>
                                    <div><strong>Расход:</strong><?= $this->data["vehicles"][$this->data['vehicle_id']]["consumption_rate"] ?></div>
                                    <div><strong>Топливо:</strong> <?= $this->data["vehicles"][$this->data['vehicle_id']]["title"] ?></div>
<!--                                    <div><strong>Цена:</strong> --><?//= $this->data["vehicles"][$this->data['vehicle_id']]["price"] ?><!--</div>-->
                                  </div>

                                  <div data-js-form-transport style="margin-left: 10px; display: none; width: 100%">
                                      <h6>Добавить транспорт</h6>
                                      <div >
                                          <table id="obj">
                                              <tbody id="obj_body">
                                              <tr>
                                                  <td>Модель</td>
                                                  <td>
                                                      <input class="tz" type="text" name="model" value="" style="width: 100%">
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td>Номер</td>
                                                  <td>
                                                      <input class="tz" type="text" name="number" value="" style="width: 100%">
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td>Владелец</td>
                                                  <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                                                  <td>
                                                      <input class="tz" type="text" name="owner_name" value="" style="width: 100%">
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td>Вид топлива</td>
                                                  <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                                                  <td>
                                                      <select name="fuel_id" id="" style="width: 100%">
                                                          <option value=""></option>
                                                          <?php foreach ($this->data["fuel_types"] as $fuelType): ?>
                                                              <option value="<?= $fuelType["id"] ?>"><?= $fuelType["title"] ?></option>
                                                          <?php endforeach; ?>
                                                      </select>
                                                      <!--                                                    <input class="tz" type="text" name="fuel_id" value="" style="width: 100%">-->
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td>Расход</td>
                                                  <td><input type="number" name="consumption_rate" style="width: 100%"></td>
                                              </tr>


                                              </tbody>
                                          </table>
                                          <button type="button" class="btn btn-primary" data-js-add-vehicle>Сохранить</button>
                                      </div>

                                  </div>

                              </div>

                              <div class="form-group col-sm-6">
                                  <strong class="mb-2 d-block">Комментарий</strong>
                                  <textarea class=" mw-100 content min-h-180" id="content" rows="6"
                                            name="comment"><?= $this->data['comment'] ?></textarea>
                              </div>
                          </div>

                          <div class="mb-2">
                              <strong>Оборудование</strong>
                              <select name="oborud[]" multiple="multiple" id="oborud" aria-hidden="true">
                                  <option value="" disabled></option>
                                  <?php foreach ($this->data["oborudList"] as $oborud): ?>
                                      <option value="<?= $oborud['ID'] ?>"
                                          <?= in_array($oborud["ID"], $this->data["secondmentOborudArr"]) ? 'selected' : '' ?>
                                      ><?= $oborud['OBJECT'] ?> (<?= $oborud['REG_NUM'] ?>)</option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                        </div>

                        <div class="extra-wrapper wrapper-shadow mb-4" data-js-plan-card>
                          <strong class="mb-2 d-block">Запланированные расходы</strong>
                          <input type="text" name="file_payment_delete" value="" style="display: none">
                          <div class="row mb-2 align-items-end overflow-hidden">
                            <div class="form-group col-sm-3">
                              <lable for="ticketPrice">Билеты</lable>
                              <input type="number" class="form-control ticket-price cost"
                                     id="ticketPrice"
                                     name="ticket_price"
                                     min="0" step="0.01"
                                     data-js-format-money
                                     value="<?= $this->data['ticket_price'] ?>">
                            </div>
                            <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPrice"
                                                    name="comment_ticket_price"
                                                    title="<?= trim($this->data['comment_ticket_price']) ?>"
                                          ><?= trim($this->data['comment_ticket_price']) ?></textarea>
                            </div>
                            <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group>

                              <div data-js-upload-wrap class="position-relative">
                                <label class="p-0 text-center"
                                       title="Загрузить билеты">
                                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                                  </div>
                                  <input
                                    multiple
                                    class="form-control d-none"
                                    type="file"
                                    id="edictBtn"
                                    name="ticket_payment[]"
                                    data-js-upload
                                  >
                                </label>
                              </div>

                                <?php foreach ($this->data["fileArr"]["ticket_payment"] as $ticket): ?>
                                  <div data-js-file-wrap class="position-relative">
                                    <a
                                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                                      href="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                                      target="_blank"
                                      style="margin-left: 4px; font-size: 16px;"
                                      title="<?= $ticket ?>"
                                      data-js-file-download

                                    ></a>
                                    <button
                                      data-js-delete-payment-file="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                                      type="button" class="position-absolute fa-solid fa-xmark"
                                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                    </button>
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
                              <lable for="gasolineConsumption">Топливо до&nbsp;объекта</lable>
                              <input type="number" class="form-control gasoline-consumption cost"
                                     id="gasolineConsumption"
                                     name="gasoline_consumption"
                                     min="0" step="0.01"
                                     data-js-format-money
                                     autocomplete="off"
                                     value="<?= $this->data['gasoline_consumption'] ?>">
                            </div>
                            <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumption"
                                                name="comment_gasoline_consumption"><?= $this->data['comment_gasoline_consumption'] == "" ? "До объекта и обратно" : $this->data['comment_gasoline_consumption'] ?></textarea>
                            </div>
                            <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                              <div data-js-upload-wrap class="position-relative">
                                <label class="p-0 text-center"
                                       title="Загрузить файлы">
                                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                                  </div>
                                  <input
                                    multiple
                                    class="form-control d-none"
                                    type="file"
                                    id="edictBtn"
                                    name="fuel_payment[]"
                                    data-js-upload
                                  >
                                </label>
                              </div>

                                <?php foreach ($this->data["fileArr"]["fuel_payment"] as $i => $file): ?>
                                  <div data-js-file-wrap class="position-relative">
                                    <a
                                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                                      href="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      target="_blank"
                                      style="margin-left: 4px; font-size: 16px;"
                                      title="<?= $file ?>"
                                      data-js-file-download

                                    ></a>
                                    <button
                                      data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      type="button" class="position-absolute fa-solid fa-xmark"
                                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                    </button>
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
                              <lable for="gasolineConsumptionObject">Топливо по&nbsp;объекту</lable>
                              <input type="number" class="form-control gasoline-consumption cost"
                                     id="gasolineConsumptionObject"
                                     name="gasoline_consumption_object"
                                     min="0" step="0.01"
                                     data-js-format-money
                                     autocomplete="off"
                                     value="<?= $this->data['gasoline_consumption_object'] ?>">
                            </div>
                            <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumptionObject"
                                                name="comment_gasoline_consumption_object"><?= $this->data['comment_gasoline_consumption_object'] == "" ? "По объекту" : $this->data['comment_gasoline_consumption_object'] ?></textarea>
                            </div>
                            <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                              <div data-js-upload-wrap class="position-relative">
                                <label class="p-0 text-center"
                                       title="Загрузить файлы">
                                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                                  </div>
                                  <input
                                    multiple
                                    class="form-control d-none"
                                    type="file"
                                    id="edictBtn"
                                    name="fuel_payment_object[]"
                                    data-js-upload
                                  >
                                </label>
                              </div>

                                <?php foreach ($this->data["fileArr"]["fuel_payment_object"] as $i => $file): ?>
                                  <div data-js-file-wrap class="position-relative">
                                    <a
                                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                                      href="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      target="_blank"
                                      style="margin-left: 4px; font-size: 16px;"
                                      title="<?= $file ?>"
                                      data-js-file-download

                                    ></a>
                                    <button
                                      data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      type="button" class="position-absolute fa-solid fa-xmark"
                                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                    </button>
                                  </div>
                                <?php endforeach; ?>

                              <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                    <?= count($this->data["fileArr"]["fuel_payment_object"]) ?>
                                </button>
                              </div>

                            </div>
                          </div>

                          <div class="row mb-2 align-items-end overflow-hidden">
                            <div class="form-group col-sm-3">
                              <lable for="perDiem">Суточные</lable>
                              <input type="number" class="form-control per-diem cost" id="perDiem"
                                     name="per_diem"
                                     min="0" step="0.01"
                                     readonly
                                     data-js-format-money
                                     autocomplete="off"
                                     value="<?= $this->data['per_diem'] ?>">
                            </div>
                            <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiem"
                                                    name="comment_per_diem"><?= trim($this->data['comment_per_diem']) ?>
                                          </textarea>
                            </div>
                            <div class="form-group col-sm-5 d-flex align-items-end align-items-end" data-js-btn-group >
                              <div data-js-upload-wrap class="position-relative">
                                <label class="p-0 text-center"
                                       title="Загрузить билеты">
                                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                                  </div>
                                  <input
                                    multiple
                                    class="form-control d-none"
                                    type="file"
                                    id="edictBtn"
                                    name="per_diem[]"
                                    data-js-upload
                                  >
                                </label>
                              </div>

                                <?php foreach ($this->data["fileArr"]["per_diem"] as $file): ?>
                                  <div data-js-file-wrap class="position-relative">
                                    <a
                                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                                      href="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      target="_blank"
                                      style="margin-left: 4px; font-size: 16px;"
                                      title="<?= $file ?>"
                                      data-js-file-download

                                    ></a>
                                    <button
                                      data-js-delete-payment-file="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      type="button" class="position-absolute fa-solid fa-xmark"
                                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                    </button>
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
                              <input type="number" class="form-control accommodation cost"
                                     id="accommodation"
                                     name="accommodation"
                                     min="0" step="0.01"
                                     data-js-format-money
                                     autocomplete="off"
                                     value="<?= $this->data['secondment']['accommodation'] ?>">
                            </div>
                            <div class="form-group col-sm-4">
                                      <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodation"
                                                name="comment_accommodation"><?= $this->data['comment_accommodation'] ?></textarea>
                            </div>

                            <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                              <div data-js-upload-wrap class="position-relative">
                                <label class="p-0 text-center"
                                       title="Загрузить билеты">
                                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                                  </div>
                                  <input
                                    multiple
                                    class="form-control d-none"
                                    type="file"
                                    id="edictBtn"
                                    name="accommodation[]"
                                    data-js-upload
                                  >
                                </label>
                              </div>

                                <?php foreach ($this->data["fileArr"]["accommodation"] as $file): ?>
                                  <div data-js-file-wrap class="position-relative">
                                    <a
                                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                                      href="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      target="_blank"
                                      style="margin-left: 4px; font-size: 16px;"
                                      title="<?= $file ?>"
                                      data-js-file-download

                                    ></a>
                                    <button
                                      data-js-delete-payment-file="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                      type="button" class="position-absolute fa-solid fa-xmark"
                                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                    </button>
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
                            <?php foreach ($this->data['other_fields'] as $i => $field): ?>
                              <div class="row mb-2 align-items-end overflow-hidden">
                                <div class="form-group col-sm-3">

                                  <input type="number" name="other_id[]" value="<?= $field["id"] ?>" class="d-none">
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


                                  <div data-js-upload-wrap class="position-relative">
                                    <label class="p-0 text-center"
                                           title="Загрузить билеты">
                                      <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                                      </div>
                                      <input
                                        multiple
                                        class="form-control d-none"
                                        type="file"
                                        id="edictBtn"
                                        name="other[<?= $i ?>][]"
                                        data-js-upload
                                      >
                                    </label>
                                  </div>

                                    <?php foreach ($this->data["fileArr"]["other"][$i] as $file): ?>
                                      <div data-js-file-wrap class="position-relative">
                                        <a
                                          class="btn btn-primary position-relative rounded fa-solid fa-file"
                                          href="/ulab/upload/secondment/other/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                          target="_blank"
                                          style="margin-left: 4px; font-size: 16px;"
                                          title="<?= $file ?>"
                                          data-js-file-download

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


                          <div class="form-group">
                            <button data-js-add-other type="button" class="btn btn-primary fa-solid fa-plus"></button>
                          </div>

                          <div class="row mb-2 align-items-end overflow-hidden">
                            <div class="form-group col-sm-3">
                              <lable for="plannedExpenses">Итого</lable>
                              <input type="text" class="form-control planned-expenses" id="plannedExpenses"
                                     name="planned_expenses"
                                     min="0" step="0.01"
                                     autocomplete="off"
                                     value="<?= $this->data['planned_expenses'] ?>" readonly>
                            </div>
                            <div class="form-group col-sm-9">
                                      <textarea class="form-control mw-100 comment-planned-expenses"
                                                id="commentPlannedExpenses"
                                                name="comment_planned_expenses"><?= $this->data['comment_planned_expenses'] ?></textarea>
                            </div>
                          </div>
                        </div>


                        <!-- /.info-wrapper -->


                        <div class="info-wrapper wrapper-shadow mb-4">
                          <strong class="mb-2 d-block">Продление командировки</strong>
                          <?php foreach ($this->data["archiveList"] as $index => $archive): ?>
                            <div class="d-flex">
                              <a class="text-decoration-none" href="/ulab/secondment/archiveCard/<?= $archive["id"] ?>">
                                  <?= $index + 1 ?>. Архив от <?= $archive["date_begin"] ?> до <?= $archive["date_end"] ?>
                              </a>
                            </div>

                          <?php endforeach; ?>
                        </div>

                        <div class="info-wrapper wrapper-shadow mb-4">
                            <strong class="mb-2 d-block">Доплата за продление</strong>
                            <?php foreach ($this->data["archiveList"] as $index => $archive): ?>
                                <div><?= $index + 1 ?>: <?= $archive["extraPayment"] ?></div>
                            <?php endforeach; ?>
                            <div><strong>Всего:</strong> <?= $this->data["archiveSum"] ?></div>
                        </div>

                        <?php if (!empty($this->data['secondment']["improvement_reason"])):?>
                            <div class="info-wrapper wrapper-shadow mb-4">
                                <strong class="mb-2 d-block">Причина доработки</strong>
                                <div><?= $this->data['secondment']["improvement_reason"] ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Сообщение об удалении -->
                        <div data-js-message> </div>

                        <div class="row wrapper-btn mb-2">
                            <?php if ($this->data['is_save_info'] && in_array($this->data['stage_name'],
                                    ['Новая', 'Нужна доработка'])): ?>
                                    <div class="col flex-grow-0 text-nowrap">
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                    <div class="col flex-grow-0 text-nowrap">
                                        <button type="button" class="btn btn-primary min-w-300" id="sendApprove"
                                                name="send_approve" data-stage="Ожидает подтверждения">
                                            Отправить на согласование
                                        </button>
                                    </div>
                                    <div class="col flex-grow-0 text-nowrap">
                                        <button type="button" data-js-delete-card="<?= $this->data['secondment']['s_id'] ?>" class="btn btn-danger">Удалить</button>
                                    </div>
                            <?php endif; ?>

                            <?php if ($this->data['is_confirm_secondment'] &&
                                $this->data['stage_name'] === 'Ожидает подтверждения'): ?>
                                    <div class="col flex-grow-0">
                                        <button type="button" class="btn btn-primary" id="confirmSecondment"
                                                name="confirm_secondment" data-stage="Подготовка приказа и СЗ">
                                            Подтвердить
                                        </button>
                                    </div>
<!--                                    <div class="col">-->
<!--                                        <button type="button" class="btn btn-primary popup-with-form btn-add-entry">-->
<!--                                            Вернуть на доработку-->
<!--                                        </button>-->
<!--                                    </div>-->
                                    <div class="col flex-grow-0">
                                        <button type="button" class="btn btn-primary" id="rejectSecondment"
                                                name="reject_secondment" data-stage="Отклонена">Отклонить
                                        </button>
                                    </div>
                            <?php elseif ($this->data['stage_name'] !== 'Нужна доработка' && $this->data['stage_name'] !== 'Новая'): ?>
<!--                                <div class="col">-->
<!--                                    <button type="button" class="btn btn-primary popup-with-form btn-add-entry">-->
<!--                                        Вернуть на доработку-->
<!--                                    </button>-->
<!--                                </div>-->
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
                <!--./panel-body-->
            </div>
            <!--./panel-->
        </div>
        <!--./col-md-12-->
    </div>
    <!--./row-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Приказ и служебное задание
                    <?= empty($this->data['secondment']['s_id']) ? '<i class="fa fa-circle text-light-red"></i>' : '' ?>
                    <span class="tools float-end">
                        <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                        <a href="javascript:;" class="fa fa-chevron-down"></a>
                    </span>
                </header>
                <div class="panel-body "> <!--panel-hidden -->
                    <form id="uploadFiles" action="/ulab/secondment/insertUpdateFiles/" class="form-upload-files" method="post"
                          enctype="multipart/form-data">
                        <?php if (!empty($this->data['secondment']['s_id'])): ?>
                            <input class="secondment-id" type="hidden" name="secondment_id"
                                   value="<?= $this->data['secondment']['s_id'] ?? '' ?>">
                            <input id="consumption_rate" type="hidden"
                                   value="<?= $this->data["vehicles"][$this->data['vehicle_id']]["consumption_rate"] ?? '' ?>">
                        <?php endif; ?>
                        <div class="documents-wrapper">
                            <div class="row mb-4">
                                <div class="row">
                                    <input type="text" name="file_delete" value="" hidden>
                                    <div class="d-flex align-items-center col-md-2 mb-2">
                                        <label for="edictNumber">№&nbsp;приказа</label>
                                        <input type="text" class="form-control" name="edict_number" style="margin-left: 5px" value="<?= $this->data["secondment"]["edict_number"] ?? "" ?>">
                                    </div>

                                    <div data-js-files class="d-flex align-items-center mb-1">
                                        <div>Приказ</div>
                                        <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['edict']['file'] ? "none" : "block" ?>">
                                            <label class="p-0 text-center <?= $this->data['is_may_save_files'] &&
                                            in_array($this->data['stage_name'], ['Подготовка приказа и СЗ', 'Согласована']) ? '' : 'disabled-upload' ?>"
                                                   title="Загрузить приказ">
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                    <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                    </span>
                                                </div>
                                                <input class="form-control d-none" type="file"  id="edictBtn" name="edict">
                                            </label>
                                        </div>

                                        <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['edict'] ? "d-none" : "" ?>">
                                            <?php foreach ($this->data['edict'] as $edict): ?>
                                                <div class="position-relative">
                                                    <a
                                                            class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                            data-js-file-edict
                                                            href="<?= $edict['dir'] ?><?= $edict['file'] ?>?<?= rand() ?>"
                                                            target="_blank"
                                                            style="margin-left: 5px; font-size: 16px"
                                                            title="<?= $edict['file'] ?>"
                                                    ></a>
                                                    <button
                                                            data-js-delete-file="<?= $edict['dir'] ?><?= $edict['file'] ?>"
                                                            type="button" class="position-absolute fa-solid fa-xmark"
                                                            style="color: red; border: none; background: transparent; right: -17px; top: -7px; z-index: 1000">
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div data-js-upload-wrap class="position-relative <?= !$this->data['edict'] ? "d-none" : "" ?>">
                                            <button type="button"
                                                    class="btn btn-primary position-relative rounded fa-solid fa-signature"
                                                    style="margin-left: 10px; font-size: 16px"
                                                    data-js-add-sign
                                                    data-js-file-path="<?= $this->data['edict']['dir'] ?><?= $this->data['edict']['file'] ?>"
                                                    data-js-img-path="/ulab/upload/signatures/sign.png"
                                                    data-js-img-params='{"x": 300,"y": 365,"width": 50,"height": 60}'
                                            >
                                            </button>
                                        </div>



                                    </div>

                                    <div data-js-files class="d-flex align-items-center mb-1">
                                        <div>Служебное задание</div>
                                        <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['service_assignment']['file'] ? "none" : "block" ?>">
                                            <label class="p-0 text-center upload <?= $this->data['is_may_save_files'] &&
                                            in_array($this->data['stage_name'], ['Подготовка приказа и СЗ', 'Согласована']) ? '' : 'disabled-upload' ?>"
                                                   title="Загрузить служебное задание">

                                                <input class="d-none"
                                                       id="serviceAssignmentBtn"
                                                       type="file"
                                                       name="service_assignment"
                                                >
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                    <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                    </span>
                                                </div>
                                            </label>
                                        </div>

<!--                                        <span class="upload-file-message" id="uploadServiceAssignment">-->
<!--                                            <a href="--><?//= $this->data['service_assignment']['dir'] ?><!----><?//= $this->data['service_assignment']['file'] ?><!--?v=--><?//= rand() ?><!--"-->
<!--                                               target="_blank">-->
<!--                                                --><?//= $this->data['service_assignment']['file'] ?>
<!--                                            </a>-->
<!--                                        </span>-->

                                        <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['service_assignment'] ? "d-none" : "" ?>">
                                            <?php foreach ($this->data['service_assignment'] as $file): ?>
                                                <div class="position-relative">
                                                    <a
                                                            class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                            href="<?= $file['dir'] ?><?= $file['file'] ?>?<?= rand() ?>"
                                                            target="_blank"
                                                            style="margin-left: 5px; font-size: 16px"
                                                            title="<?= $file['file'] ?>"
                                                    ></a>
                                                    <button
                                                            data-js-delete-file="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                            type="button" class="position-absolute fa-solid fa-xmark"
                                                            style="color: red; border: none; background: transparent; right: -17px; top: -7px; z-index: 1000">
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>

                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary rounded fa-solid fa-file-medical" data-js-add-order style="margin-left: 15px; font-size: 16px"></button>
                                        </div>
                                        <div data-js-upload-wrap class="position-relative <?= !$this->data['service_assignment'] ? "d-none" : "" ?>">
                                            <button type="button"
                                                    class="btn btn-primary position-relative rounded fa-solid fa-signature"
                                                    style="margin-left: 10px; font-size: 16px"
                                                    data-js-add-sign
                                                    data-js-file-path="<?= $this->data['service_assignment']['dir'] ?><?= $this->data['service_assignment']['file'] ?>"
                                                    data-js-img-path="/ulab/upload/signatures/sign.png"
                                                    data-js-img-params='{"x": 230,"y": 110,"width": 50,"height": 60}'
                                            >
                                            </button>
                                        </div>
                                    </div>

                                    <div data-js-files class="d-flex align-items-center mb-1">
                                        <div>Приказ с подписью</div>

                                        <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['signed_edict'] ? "d-none" : "" ?>">
                                            <?php foreach ($this->data['signed_edict'] as $file): ?>
                                                    <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                        target="_blank"
                                                        style="margin-left: 5px; font-size: 16px"
                                                        title="<?= $file['file'] ?>"
                                                    ></a>

                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div data-js-files class="d-flex align-items-center mb-1">
                                        <div>Служебное задание с подписью</div>

                                        <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['signed_service_assignment'] ? "d-none" : "" ?>">
                                            <?php foreach ($this->data['signed_service_assignment'] as $file): ?>
                                                <a
                                                    class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                    href="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                    target="_blank"
                                                    style="margin-left: 5px; font-size: 16px"
                                                    title="<?= $file['file'] ?>"
                                                ></a>
                                            <?php endforeach; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label>Без приказа</label>
                                <input type="checkbox" name="no_edict2">
                            </div>
                            <?php if ($this->data['is_may_save_files']): ?>
                                <?php if ($this->data['stage_name'] === 'Подготовка приказа и СЗ'): ?>
                                <div class="d-flex gap-2">
                                    <div>
                                        <button data-js-save-files type="submit" class="btn btn-primary min-w-200" id="saveUploadFiles"
                                                name="save_upload_files" data-stage="Согласована">Сохранить файлы
                                        </button>
                                    </div>
                                    <div>
                                        <button data-js-save-files type="submit" class="btn btn-primary min-w-200" id="saveUploadFiles"
                                                name="stage_ready" data-stage="Согласована">Готово
                                        </button>
                                    </div>
                                </div>

                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <!-- /.documents-wrapper -->
                    </form>
                </div>
                <!--./panel-body-->
            </div>
            <!--./panel-->
        </div>
        <!--./col-md-12-->
    </div>
    <!--./row-->
  <form action="/ulab/secondment/insertUpdateReport/" class="form-report" id="formReport"
        method="post"
        enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Отчет о командировке
                    <?= empty($this->data['secondment']['s_id']) ? '<i class="fa fa-circle text-light-red"></i>' : '' ?>
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                        </span>
                </header>
                <div class="panel-body ">

                        <?php if (!empty($this->data['secondment']['s_id'])): ?>
                            <input class="secondment-id" type="hidden" name="secondment_id"
                                   value="<?= $this->data['secondment']['s_id'] ?? '' ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">
                            <div data-js-fact-card class="extra-wrapper wrapper-shadow mb-4">
                                <strong class="mb-2 d-block">Данные отчёта</strong>
                                <input type="text" name="file_payment_delete" value="" style="display: none">
                                <div class="row mb-2 align-items-end overflow-hidden">
                                    <div class="form-group col-sm-3">
                                        <lable for="ticketPriceFact">Билеты</lable>
                                        <input type="number" class="form-control ticket-price cost"
                                               id="ticketPriceFact"
                                               name="ticket_price_fact"
                                               min="0" step="0.01"
                                               data-js-format-money
                                               data-js-fact
                                               value="<?= $this->data['secondment']['ticket_price_fact'] ?>">
                                    </div>
                                    <div class="form-group col-sm-4">
                                            <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPriceFact"
                                                      name="comment_ticket_price_fact"
                                                      title="<?= trim($this->data['secondment']['comment_ticket_price_fact']) ?>"
                                                      data-js-fact
                                            ><?= trim($this->data['secondment']['comment_ticket_price_fact']) ?></textarea>
                                    </div>
                                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group>
                                        <div data-js-upload-wrap class="position-relative">
                                            <label class="p-0 text-center"
                                                   title="Загрузить билеты">
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                        <span data-js-input-count
                                                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                              style="z-index: 100"
                                                        >
                                                        </span>
                                                </div>
                                                <input
                                                        multiple
                                                        class="form-control d-none"
                                                        type="file"
                                                        id="edictBtn"
                                                        name="ticket_payment_fact[]"
                                                        data-js-upload
                                                >
                                            </label>
                                        </div>

                                        <?php foreach ($this->data["fileArr"]["ticket_payment_fact"] as $ticket): ?>
                                            <div data-js-file-wrap class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="/ulab/upload/secondment/ticket_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                                                        target="_blank"
                                                        style="margin-left: 4px; font-size: 16px;"
                                                        title="<?= $ticket ?>"
                                                        data-js-file-download

                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="/ulab/upload/secondment/ticket_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                <?= count($this->data["fileArr"]["ticket_payment_fact"]) ?>
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <div class="row mb-2 align-items-end overflow-hidden">
                                    <div class="form-group col-sm-3">
                                        <lable for="gasolineConsumption">Топливо до&nbsp;объекта</lable>
                                        <input type="number" class="form-control gasoline-consumption cost"
                                               id="gasolineConsumptionFact"
                                               name="gasoline_consumption_fact"
                                               min="0" step="0.01"
                                               data-js-format-money
                                               autocomplete="off"
                                               value="<?= $this->data['secondment']['gasoline_consumption_fact'] ?>">
                                    </div>
                                    <div class="form-group col-sm-4">

                                        <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                  id="commentGasolineConsumption"
                                                  name="comment_gasoline_consumption_fact"><?= $this->data['secondment']['comment_gasoline_consumption_fact'] == "" ? "До объекта и обратно" : $this->data['comment_gasoline_consumption'] ?></textarea>
                                    </div>
                                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                                        <div data-js-upload-wrap class="position-relative">
                                            <label class="p-0 text-center"
                                                   title="Загрузить файлы">
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                        <span data-js-input-count
                                                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                              style="z-index: 100"
                                                        >
                                                        </span>
                                                </div>
                                                <input
                                                        multiple
                                                        class="form-control d-none"
                                                        type="file"
                                                        id="edictBtn"
                                                        name="fuel_payment_fact[]"
                                                        data-js-upload
                                                >
                                            </label>
                                        </div>

                                        <?php foreach ($this->data["fileArr"]["fuel_payment_fact"] as $i => $file): ?>
                                            <div data-js-file-wrap class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="/ulab/upload/secondment/fuel_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        target="_blank"
                                                        style="margin-left: 4px; font-size: 16px;"
                                                        title="<?= $file ?>"
                                                        data-js-file-download

                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>

                                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                <?= count($this->data["fileArr"]["fuel_payment_fact"]) ?>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <div class="row mb-2 align-items-end overflow-hidden">
                                  <div class="form-group col-sm-3">
                                    <lable for="gasolineConsumption">Топливо по&nbsp;объекту</lable>
                                    <input type="number" class="form-control gasoline-consumption cost"
                                           id="gasolineConsumptionFact"
                                           name="gasoline_consumption_object_fact"
                                           min="0" step="0.01"
                                           data-js-format-money
                                           autocomplete="off"
                                           value="<?= $this->data['secondment']['gasoline_consumption_object_fact'] ?>">
                                  </div>
                                  <div class="form-group col-sm-4">

                                          <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                    id="commentGasolineConsumption"
                                                    name="comment_gasoline_consumption_object_fact"><?= $this->data['secondment']['comment_gasoline_consumption_object_fact'] == "" ? "По объекту" : $this->data['comment_gasoline_object_consumption'] ?></textarea>
                                  </div>
                                  <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                                    <div data-js-upload-wrap class="position-relative">
                                      <label class="p-0 text-center"
                                             title="Загрузить файлы">
                                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                          <span data-js-input-count
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                                style="z-index: 100"
                                                          >
                                                          </span>
                                        </div>
                                        <input
                                          multiple
                                          class="form-control d-none"
                                          type="file"
                                          id="edictBtn"
                                          name="fuel_payment_object_fact[]"
                                          data-js-upload
                                        >
                                      </label>
                                    </div>

                                      <?php foreach ($this->data["fileArr"]["fuel_payment_object_fact"] as $i => $file): ?>
                                        <div data-js-file-wrap class="position-relative">
                                          <a
                                            class="btn btn-primary position-relative rounded fa-solid fa-file"
                                            href="/ulab/upload/secondment/fuel_payment_object_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                            target="_blank"
                                            style="margin-left: 4px; font-size: 16px;"
                                            title="<?= $file ?>"
                                            data-js-file-download

                                          ></a>
                                          <button
                                            data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_object_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                            type="button" class="position-absolute fa-solid fa-xmark"
                                            style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                          </button>
                                        </div>
                                      <?php endforeach; ?>

                                    <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                      <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                          <?= count($this->data["fileArr"]["fuel_payment_object_fact"]) ?>
                                      </button>
                                    </div>

                                  </div>
                                </div>

                                <div class="row mb-2 align-items-end overflow-hidden">
                                    <div class="form-group col-sm-3">
                                        <label for="perDiemFact">Суточные</label>
                                        <input type="number" class="form-control per-diem cost" id="perDiemFact"
                                               name="per_diem_fact"
                                               min="0" step="0.01"
                                               data-js-format-money
                                               autocomplete="off"
                                               value="<?= $this->data['secondment']['per_diem_fact'] ?>">
                                    </div>
                                    <div class="form-group col-sm-4">
                                            <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiemFact"
                                                      name="comment_per_diem_fact"><?= trim($this->data['secondment']['comment_per_diem_fact']) ?>
                                            </textarea>
                                    </div>
                                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                                        <div data-js-upload-wrap class="position-relative">
                                            <label class="p-0 text-center"
                                                   title="Загрузить билеты">
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                    <span data-js-input-count
                                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                          style="z-index: 100"
                                                    >
                                                    </span>
                                                </div>
                                                <input
                                                        multiple
                                                        class="form-control d-none"
                                                        type="file"
                                                        id="edictBtn"
                                                        name="per_diem_fact[]"
                                                        data-js-upload
                                                >
                                            </label>
                                        </div>

                                        <?php foreach ($this->data["fileArr"]["per_diem_fact"] as $file): ?>
                                            <div data-js-file-wrap class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="/ulab/upload/secondment/per_diem_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        target="_blank"
                                                        style="margin-left: 4px; font-size: 16px;"
                                                        title="<?= $file ?>"
                                                        data-js-file-download

                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="/ulab/upload/secondment/per_diem_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>

                                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                <?= count($this->data["fileArr"]["per_diem_fact"]) ?>
                                            </button>
                                        </div>

                                    </div>


                                </div>
                                <div class="row mb-2 align-items-end overflow-hidden">
                                    <div class="form-group col-sm-3">
                                        <lable for="accommodation">Проживание</lable>
                                        <input type="number" class="form-control accommodation cost"
                                               id="accommodationFact"
                                               name="accommodation_fact"
                                               min="0" step="0.01"
                                               data-js-format-money
                                               autocomplete="off"
                                               value="<?= $this->data['secondment']['accommodation_fact'] ?>">
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodationFact"
                                                  name="comment_accommodation_fact"><?= $this->data['secondment']['comment_accommodation_fact'] ?></textarea>
                                    </div>

                                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                                        <div data-js-upload-wrap class="position-relative">
                                            <label class="p-0 text-center"
                                                   title="Загрузить билеты">
                                                <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                    <span data-js-input-count
                                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                          style="z-index: 100"
                                                    >
                                                    </span>
                                                </div>
                                                <input
                                                        multiple
                                                        class="form-control d-none"
                                                        type="file"
                                                        id="edictBtn"
                                                        name="accommodation_fact[]"
                                                        data-js-upload
                                                >
                                            </label>
                                        </div>

                                        <?php foreach ($this->data["fileArr"]["accommodation_fact"] as $file): ?>
                                            <div data-js-file-wrap class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="/ulab/upload/secondment/accommodation_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        target="_blank"
                                                        style="margin-left: 4px; font-size: 16px;"
                                                        title="<?= $file ?>"
                                                        data-js-file-download

                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="/ulab/upload/secondment/accommodation_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>

                                        <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                <?= count($this->data["fileArr"]["accommodation_fact"]) ?>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <lable for="other">Прочее</lable>
                                <?php foreach ($this->data['other_fields'] as $i => $field): ?>
                                    <div class="row mb-2 align-items-end overflow-hidden">
                                        <div class="form-group col-sm-3">

                                            <input type="number" name="other_id[]" value="<?= $field["id"] ?>" class="d-none">
                                            <input type="number" class="form-control other cost" id="otherFact"
                                                   name="other_fact[]"
                                                   min="0" step="0.01"
                                                   data-js-format-money
                                                   autocomplete="off"
                                                   value="<?= $field["sum_fact"] ?>">
                                        </div>
                                        <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-other" id="commentOther"
                                                  name="comment_other_fact[]"><?= $field['comment_fact'] ?></textarea>
                                        </div>
                                        <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                                            <div data-js-upload-wrap class="position-relative">
                                                <label class="p-0 text-center"
                                                       title="Загрузить билеты">
                                                    <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                    <span data-js-input-count
                                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                          style="z-index: 100"
                                                    >
                                                    </span>
                                                    </div>
                                                    <input
                                                            multiple
                                                            class="form-control d-none"
                                                            type="file"
                                                            id="edictBtn"
                                                            name="other_fact[<?= $i ?>][]"
                                                            data-js-upload
                                                    >
                                                </label>
                                            </div>

                                            <?php foreach ($this->data["fileArr"]["other_fact"][$i] as $file): ?>
                                                <div data-js-file-wrap class="position-relative">
                                                    <a
                                                            class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                            href="/ulab/upload/secondment/other_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                                            target="_blank"
                                                            style="margin-left: 4px; font-size: 16px;"
                                                            title="<?= $file ?>"
                                                            data-js-file-download

                                                    ></a>
                                                    <button
                                                            data-js-delete-payment-file="/ulab/upload/secondment/other_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                                            type="button" class="position-absolute fa-solid fa-xmark"
                                                            style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>

                                            <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                                <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                    <?= count($this->data["fileArr"]["other_fact"][$i]) ?>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="row mb-2" style="margin-top: 70px">
                                    <div class="form-group col-sm-6">
                                        <lable for="totalSpent">Всего потрачено</lable>
                                        <div class="input-group">
                                            <input type="number" class="form-control total-spent" id="totalSpent"
                                                   name="total_spent" value="<?= $this->data['total_spent'] ?>"
                                                   aria-describedby="spent" min="0" step="0.01">

                                        </div>
                                    </div>
<!--                                    <div class="form-group col-sm-6 d-none">-->
<!--                                        <lable for="overspending">Перерасход %</lable>-->
<!--                                        <input type="number" class="form-control overspending-->
<!--                                                --><?//= $this->data['overspending'] > 20 ? 'border-red' : '' ?><!--" id="overspending"-->
<!--                                               name="overspending" step="0.01"-->
<!--                                               value="--><?//= $this->data['overspending'] ?><!--" readonly>-->
<!--                                        <div id="userCheckedOverspending" class="form-text">-->
<!--                                            --><?//= $this->data['user_checked_overspending']['short_name'] ?>
<!--                                            --><?//= $this->data['user_checked_overspending']['date'] ?>
<!--                                        </div>-->
<!--                                    </div>-->
                                </div>


                                <strong class="mb-2 d-block">Данные отчёта</strong>

                                <div class="row mb-2">
                                    <div class="form-group col-sm-6">
                                        <lable for="comment">Комментарий сотрудника</lable>
                                        <textarea class="form-control mw-100 comment" id="comment"
                                                  name="comment"><?= $this->data['comment'] ?></textarea>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <lable for="memo">Служебная записка</lable>
                                        <textarea class="form-control mw-100 memo" id="memo"
                                                  name="memo"><?= $this->data['memo'] ?></textarea>
                                    </div>
                                </div>

                                <div data-js-files class="d-flex align-items-center">
                                    <div>Приказ с подписью</div>
                                    <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['signed_edict']['file'] ? "none" : "block" ?>">
                                        <label class="p-0 text-center"
                                               title="Загрузить приказ">
                                            <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                        <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                        </span>
                                            </div>
                                            <input class="form-control d-none" type="file" multiple  id="edictBtn" name="signed_edict[]">
                                        </label>
                                    </div>

                                    <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['signed_edict'] ? "d-none" : "" ?>">
                                        <?php foreach ($this->data['signed_edict'] as $file): ?>
                                            <div class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                        target="_blank"
                                                        style="margin-left: 5px; font-size: 16px"
                                                        title="<?= $file['file'] ?>"
                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -17px; top: -7px; z-index: 1000">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div data-js-files class="d-flex align-items-center">
                                    <div>Служебное задание с подписью</div>
                                    <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['signed_service_assignment']['file'] ? "none" : "block" ?>">
                                        <label class="p-0 text-center upload"
                                               title="Загрузить служебное задание">

                                            <input class="d-none"
                                                   id="serviceAssignmentBtn"
                                                   type="file"
                                                   multiple
                                                   name="signed_service_assignment[]"
                                            >
                                            <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                        <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                        </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div data-js-file-wrap class="d-flex position-relative <?= !$this->data['signed_service_assignment'] ? "d-none" : "" ?>">
                                        <?php foreach ($this->data['signed_service_assignment'] as $file): ?>
                                            <div class="position-relative">
                                                <a
                                                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                        href="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                        target="_blank"
                                                        style="margin-left: 5px; font-size: 16px"
                                                        title="<?= $file['file'] ?>"

                                                ></a>
                                                <button
                                                        data-js-delete-payment-file="<?= $file['dir'] ?><?= $file['file'] ?>"
                                                        type="button" class="position-absolute fa-solid fa-xmark"
                                                        style="color: red; border: none; background: transparent; right: -17px; top: -7px; z-index: 1000">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <?php if (isset($this->data["compensationItem"]["sum"])): ?>
                                <div>Компенсация: <b><span><?= $this->data["compensationItem"]["sum"] ?></span></b></div>
                                <?php endif; ?>

                            </div>
                            <div data-js-fact-card class="extra-wrapper wrapper-shadow mb-4">
                                <strong class="mb-2 d-block">Дополнительные расходы</strong>
                                <div class="row mb-2 align-items-end overflow-hidden">
                                    <?php foreach ($this->data['additional_fields'] as $i => $field): ?>
                                        <div class="row mb-2 align-items-end overflow-hidden">
                                            <div class="form-group col-sm-3">

                                                <input type="number" name="additional_id[]" value="<?= $field["id"] ?>" class="d-none">
                                                <input type="number" class="form-control other cost" id="other"
                                                       name="additional[]"
                                                       min="0" step="0.01"
                                                       data-js-format-money
                                                       autocomplete="off"
                                                       value="<?= $field["sum"] ?>">
                                            </div>
                                            <div class="form-group col-sm-4">
                                    <textarea class="form-control mw-100 comment-other" id="commentAdditional"
                                              name="comment_additional[]"><?= $field['comment'] ?></textarea>
                                            </div>
                                            <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >

                                                <div data-js-upload-wrap class="position-relative">
                                                    <label class="p-0 text-center" title="Загрузить">
                                                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                            <span data-js-input-count
                                                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                                  style="z-index: 100"
                                                            >
                                                            </span>
                                                        </div>
                                                        <input
                                                            multiple
                                                            class="form-control d-none"
                                                            type="file"
                                                            id="edictBtn"
                                                            name="additional[<?= $i ?>][]"
                                                            data-js-upload
                                                        >
                                                    </label>
                                                </div>

                                                <?php foreach ($this->data["fileArr"]["additional"][$i] as $file): ?>
                                                    <div data-js-file-wrap class="position-relative">
                                                        <a
                                                                class="btn btn-primary position-relative rounded fa-solid fa-file"
                                                                href="/ulab/upload/secondment/additional/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>?v=<?= rand() ?>"
                                                                target="_blank"
                                                                style="margin-left: 4px; font-size: 16px;"
                                                                title="<?= $file ?>"
                                                                data-js-file-download

                                                        ></a>
                                                        <button
                                                                data-js-delete-payment-file="/ulab/upload/secondment/additional/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                                                type="button" class="position-absolute fa-solid fa-xmark"
                                                                style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div data-js-upload-wrap class="position-relative btn-count-wrap">
                                                    <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                                        <?= count($this->data["fileArr"]["additional"][$i]) ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                    <div class="form-group">
                                        <button data-js-add-additional type="button" class="btn btn-primary fa-solid fa-plus"></button>
                                    </div>

                                </div>
                            </div>
                        </div>

<!--                        <div>Чеки</div>-->
<!--                        <div class="checks-wrapper wrapper-shadow mb-4 pt-4 pb-4">-->
<!--                            <div class="row file-preview-container">-->
<!--                                --><?php //foreach ($this->data['checks_files'] as $file): ?>
<!--                                    <div class="col-2 file-preview-block d-flex flex-column">-->
<!--                                        <div class="file-preview-img">-->
<!--                                            <img src="--><?//= $file['img'] ?><!--" alt="ico" width="90">-->
<!--                                        </div>-->
<!--                                        <div class="file-preview-title align-center">-->
<!--                                            <a class="text-decoration-none"-->
<!--                                               href="/ulab/upload/secondment/checks/--><?//= $this->data['secondment_id'] ?><!--/--><?//= $file['name'] ?><!--"-->
<!--                                               target="_blank">--><?//= $file['name'] ?><!--</a>-->
<!--                                        </div>-->
<!--                                        --><?php //if ($this->data['is_save_secondment'] && $this->data['stage_name'] === 'Подготовка отчета'): ?>
<!--                                            <div class="file-preview-back flex-column">-->
<!--                                                <a class="btn btn-danger"-->
<!--                                                   href="/ulab/secondment/deleteChecksFile/--><?//= $this->data['secondment_id'] ?><!--?file=--><?//= $file['name'] ?><!--">Удалить</a>-->
<!--                                                <a download class="btn btn-success"-->
<!--                                                   href="/ulab/upload/secondment/checks/--><?//= $this->data['secondment_id'] ?><!--/--><?//= $file['name'] ?><!--">Скачать</a>-->
<!--                                            </div>-->
<!--                                        --><?php //endif; ?>
<!--                                    </div>-->
<!--                                --><?php //endforeach; ?>
<!--                            </div>-->
<!---->
<!--                            <div class="line-dashed"></div>-->
<!---->
<!--                            <div class="dropzone-msg"></div>-->
<!---->
<!--                            <div class="row">-->
<!--                                <div class="col">-->
<!--                                    <div id="checks" class="dropzone dz-clickable min-h-180">-->
<!--                                        <div class="dropzone-previews"></div>-->
<!--                                        <div class="dz-default dz-message"><span>Drop files here to upload</span></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <!-- /.checks-wrapper -->

                        <?php if (!empty($this->data['users_confirmed_report'])): ?>
                            <div class="verification-data-wrapper">
                                <strong class="mb-2 d-block">Данные о подтверждении отчета</strong>

                                <div class="row">
                                    <?php foreach ($this->data['users_confirmed_report'] as $key => $data): ?>
                                        <div class="col-sm-6 mb-2">
                                            <div class="verification-data wrapper-shadow mb-2">
                                                <div class="row">
                                                    <div class="col">
                                                        Отчет подтвержден -
                                                        <?= $this->data['users_confirmed_report'][$key]['action'] ? 'Да' : 'Нет' ?>
                                                        <span class="<?= $this->data['users_confirmed_report'][$key]['action'] ?
                                                            'text-success' : 'text-danger' ?>">&#128504;</span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        Кем отчет подтвержден
                                                        - <?= $this->data['users_confirmed_report'][$key]['short_name'] ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        Дата подтверждения отчета
                                                        - <?= $this->data['users_confirmed_report'][$key]['date'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.verification-data -->
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- /.verification-data-wrapper -->
                        <?php endif; ?>

                        <div class="row mb-2 wrapper-btn">
                            <?php if ($this->data['is_save_report']): ?>
                                <?php if ($this->data['stage_name'] === 'Подготовка отчета'): ?>
                                    <div class="col flex-grow-0 text-nowrap">
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                    <div class="col flex-grow-0 text-nowrap">
                                        <button type="button" class="btn btn-primary min-w-300" id="sendVerify"
                                                name="send_verify" data-stage="Проверка отчета">
                                            Отправить на проверку
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($this->data['is_confirm_report']): ?>
                                <?php if ($this->data['stage_name'] === 'Проверка отчета'
                                    && empty($this->data['confirmation_current_user'])
                                    && ($this->data["is_root"] || $this->data["is_manager"])): ?>
                                    <div class="col flex-grow-0">
                                        <button type="button" class="btn btn-primary" id="confirmReport"
                                                name="confirm_report" data-stage="Отчет подтвержден">Подтвердить
                                        </button>
                                    </div>
                                    <div class="col">
                                        <button type="button" class="btn btn-primary min-w-300" id="expensesNotVerified"
                                                name="expenses_not_verified" data-stage="Отчет не подтвержден">
                                            Отклонить отчёт
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
<!--                    </form>-->
                </div>
                <!--./panel-body-->
            </div>
            <!--./panel-->
        </div>
        <!--./col-md-12-->
    </div>
    <!--./row-->

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <header class="panel-heading">
          Документы
            <?= empty($this->data['secondment']['s_id']) ? '<i class="fa fa-circle text-light-red"></i>' : '' ?>
          <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="javascript:;" class="fa fa-chevron-up"></a>
                        </span>
        </header>
        <div class="panel-body">
          <div id="uploadDocuments" class="form-upload-files">
              <?php if (!empty($this->data['secondment']['s_id'])): ?>
                <input class="secondment-id" type="hidden" name="secondment_id"
                       value="<?= $this->data['secondment']['s_id'] ?? '' ?>">
              <?php endif; ?>
            <div class="documents-wrapper">
              <div class="row mb-4">
                <div class="row">
                  <input type="text" name="file_delete" style="display: none">
                  <div data-js-files class="d-flex align-items-center mb-1">
                    <div>Служебная записка</div>
                    <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['memo_doc']['file'] ? "none" : "block" ?>">
                      <label class="p-0 text-center"
                             title="Загрузить приказ">
                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                    <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                    </span>
                        </div>
                        <input class="form-control d-none" type="file"  id="edictBtn" name="memo_doc[]">
                      </label>
                    </div>

                      <div data-js-upload-wrap class="position-relative" style="margin-left: 5px; display: block">
                          <button data-js-generate-memo-doc type="button" class="btn btn-primary rounded fa-solid fa-file-medical"></button>
                      </div>

                    <div data-js-file-wrap class="position-relative <?= !$this->data['memo_doc']['file'] ? "d-none" : "" ?>">
                      <a
                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                        href="<?= $this->data['memo_doc']['dir'] ?><?= $this->data['memo_doc']['file'] ?>?v=<?= rand() ?>"
                        target="_blank"
                        style="margin-left: 5px; font-size: 16px"
                        title="<?= $this->data['memo_doc']['file'] ?>"


                      ></a>
                      <button
                        data-js-delete-file="<?= $this->data['memo_doc']['dir'] ?><?= $this->data['memo_doc']['file'] ?>"
                        type="button" class="position-absolute fa-solid fa-xmark"
                        style="color: red; border: none; background: transparent; right: -17px; top: -7px">
                      </button>
                    </div>
                  </div>

                  <?php if ($this->data["vehicles"][$this->data['secondment']['vehicle_id']]["personal"] == 1): ?>
                  <div data-js-files class="d-flex align-items-center mb-1">
                    <div>Компенсация</div>
                    <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['compensation']['file'] ? "none" : "block" ?>">
                      <label class="p-0 text-center"
                             title="Загрузить приказ">
                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                                                    <span data-js-input-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                                                    </span>
                        </div>
                        <input class="form-control d-none" type="file"  id="edictBtn" name="compensation[]">
                      </label>
                    </div>

                      <div data-js-upload-wrap class="position-relative" style="margin-left: 5px; display: <?= $this->data['compensation']['file'] ? "none" : "block" ?>">
                          <button data-js-generate-compensation type="button" class="btn btn-primary rounded fa-solid fa-file-medical"></button>
                      </div>

                    <div data-js-file-wrap class="position-relative <?= !$this->data['compensation']['file'] ? "d-none" : "" ?>">
                      <a
                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                        href="<?= $this->data['compensation']['dir'] ?><?= $this->data['compensation']['file'] ?>?v=<?= rand() ?>"
                        target="_blank"
                        style="margin-left: 5px; font-size: 16px"
                        title="<?= $this->data['compensation']['file'] ?>"


                      ></a>
                      <button
                        data-js-delete-file="<?= $this->data['compensation']['dir'] ?><?= $this->data['compensation']['file'] ?>"
                        type="button" class="position-absolute fa-solid fa-xmark"
                        style="color: red; border: none; background: transparent; right: -17px; top: -7px">
                      </button>
                    </div>
                  </div>
                  <?php endif; ?>

                  <div data-js-files class="d-flex align-items-center mb-1">
                    <div>Путевой лист</div>
                    <div data-js-upload-wrap class="position-relative" style="display: <?= $this->data['waybill']['file'] ? "none" : "block" ?>">
                      <label class="p-0 text-center upload"
                             title="Путевой лист">

                        <input class="d-none"
                               id="serviceAssignmentBtn"
                               type="file"
                               name="waybill[]"
                        >
                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="margin-left: 10px; font-size: 16px">
                            <span data-js-input-count
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded">
                            </span>
                        </div>
                      </label>
                    </div>

                    <div data-js-file-wrap class="position-relative <?= !$this->data['waybill']['file'] ? "d-none" : "" ?>">
                      <a
                        class="btn btn-primary position-relative rounded fa-solid fa-file"
                        href="<?= $this->data['waybill']['dir'] ?><?= $this->data['waybill']['file'] ?>?v=<?= rand() ?>"
                        target="_blank"
                        style="margin-left: 5px; font-size: 16px"
                        title="<?= $this->data['waybill']['file'] ?>"

                      ></a>
                      <button
                        data-js-delete-file="<?= $this->data['waybill']['dir'] ?><?= $this->data['waybill']['file'] ?>"
                        type="button" class="position-absolute fa-solid fa-xmark"
                        style="color: red; border: none; background: transparent; right: -17px; top: -7px">
                      </button>

                    </div>
                  </div>
                </div>
              </div>

            </div>
            <!-- /.documents-wrapper -->
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>

    <?php if (in_array($this->data["stage_name"], ['Проверка отчета','Отчет подтвержден','Завершена','Отменена'])): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <header class="panel-heading">
            Итого
              <?= empty($this->data['secondment']['s_id']) ? '<i class="fa fa-circle text-light-red"></i>' : '' ?>
            <span class="tools float-end">
                              <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                              <a href="javascript:;" class="fa fa-chevron-up"></a>
                          </span>
          </header>
          <div class="panel-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">

                <div class="extra-wrapper wrapper-shadow mb-4" data-js-plan-card-result>
                  <strong class="mb-2 d-block">Запланированные расходы</strong>
                  <input type="text" name="file_payment_delete" value="" style="display: none">
                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="ticketPrice">Билеты</lable>
                      <input type="number" class="form-control ticket-price cost"
                             id="ticketPrice"
                             name="ticket_price"
                             min="0" step="0.01"
                             data-js-format-money
                             value="<?= $this->data['ticket_price'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPrice"
                                                    name="comment_ticket_price"
                                                    title="<?= trim($this->data['comment_ticket_price']) ?>"
                                          ><?= trim($this->data['comment_ticket_price']) ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group>

                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить билеты">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="ticket_payment[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["ticket_payment"] as $ticket): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $ticket ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
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
                      <lable for="gasolineConsumption">Топливо до&nbsp;объекта</lable>
                      <input type="number" class="form-control gasoline-consumption cost"
                             id="gasolineConsumption"
                             name="gasoline_consumption"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data['gasoline_consumption'] ?>">
                    </div>
                    <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumption"
                                                name="comment_gasoline_consumption"><?= $this->data['comment_gasoline_consumption'] == "" ? "До объекта и обратно" : $this->data['comment_gasoline_consumption'] ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить файлы">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="fuel_payment[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["fuel_payment"] as $i => $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
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
                      <lable for="gasolineConsumptionObject">Топливо по&nbsp;объекту</lable>
                      <input type="number" class="form-control gasoline-consumption cost"
                             id="gasolineConsumptionObject"
                             name="gasoline_consumption_object"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data['gasoline_consumption_object'] ?>">
                    </div>
                    <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumptionObject"
                                                name="comment_gasoline_consumption_object"><?= $this->data['comment_gasoline_consumption_object'] == "" ? "По объекту" : $this->data['comment_gasoline_consumption_object'] ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить файлы">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="fuel_payment_object[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["fuel_payment_object"] as $i => $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
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
                      <input type="number" class="form-control per-diem cost" id="perDiem"
                             name="per_diem"
                             min="0" step="0.01"
                             readonly
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data['per_diem'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                          <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiem"
                                                    name="comment_per_diem"><?= trim($this->data['comment_per_diem']) ?>
                                          </textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end align-items-end" data-js-btn-group >
                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить билеты">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="per_diem[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["per_diem"] as $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
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
                      <input type="number" class="form-control accommodation cost"
                             id="accommodation"
                             name="accommodation"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data['secondment']['accommodation'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                      <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodation"
                                                name="comment_accommodation"><?= $this->data['comment_accommodation'] ?></textarea>
                    </div>

                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить билеты">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="accommodation[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["accommodation"] as $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
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
                    <?php foreach ($this->data['other_fields'] as $i => $field): ?>
                      <div class="row mb-2 align-items-end overflow-hidden">
                        <div class="form-group col-sm-3">

                          <input type="number" name="other_id[]" value="<?= $field["id"] ?>" class="d-none">
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


                          <div data-js-upload-wrap class="position-relative">
                            <label class="p-0 text-center"
                                   title="Загрузить билеты">
                              <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                  <span data-js-input-count
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                        style="z-index: 100"
                                                  >
                                                  </span>
                              </div>
                              <input
                                multiple
                                class="form-control d-none"
                                type="file"
                                id="edictBtn"
                                name="other[<?= $i ?>][]"
                                data-js-upload
                              >
                            </label>
                          </div>

                            <?php foreach ($this->data["fileArr"]["other"][$i] as $file): ?>
                              <div data-js-file-wrap class="position-relative">
                                <a
                                  class="btn btn-primary position-relative rounded fa-solid fa-file"
                                  href="/ulab/upload/secondment/other/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                  target="_blank"
                                  style="margin-left: 4px; font-size: 16px;"
                                  title="<?= $file ?>"
                                  data-js-file-download

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


                  <div class="form-group">
                    <button data-js-add-other type="button" class="btn btn-primary fa-solid fa-plus"></button>
                  </div>

                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="plannedExpenses">Итого</lable>
                      <input type="text" class="form-control planned-expenses" id="plannedExpenses"
                             name="planned_expenses"
                             min="0" step="0.01"
                             autocomplete="off"
                             value="<?= $this->data['planned_expenses'] ?>" readonly>
                    </div>
                    <div class="form-group col-sm-9">
                                      <textarea class="form-control mw-100 comment-planned-expenses"
                                                id="commentPlannedExpenses"
                                                name="comment_planned_expenses"><?= $this->data['comment_planned_expenses'] ?></textarea>
                    </div>
                  </div>
                </div>

                <div class="extra-wrapper wrapper-shadow mb-4" data-js-result-card>
                  <strong class="mb-2 d-block">Фактические расходы</strong>
                  <input type="text" name="file_payment_fact_delete" value="" style="display: none">
                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="ticketPriceFact">Билеты</lable>
                      <input type="number" class="form-control ticket-price cost"
                             id="ticketPriceFact"
                             min="0" step="0.01"
                             data-js-format-money
                             data-js-fact
                             value="<?= $this->data["secondment"]['ticket_price_fact'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPrice"

                                                  title="<?= trim($this->data["secondment"]['comment_ticket_price_fact']) ?>"
                                                  data-js-fact
                                        ><?= trim($this->data["secondment"]['comment_ticket_price_fact']) ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group>

                        <?php foreach ($this->data["fileArr"]["ticket_payment_fact"] as $ticket): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/ticket_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $ticket ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/ticket_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
                          </div>
                        <?php endforeach; ?>
                      <div data-js-upload-wrap class="position-relative btn-count-wrap">
                        <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                            <?= count($this->data["fileArr"]["ticket_payment_fact"]) ?>
                        </button>
                      </div>

                    </div>

                  </div>
                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="gasolineConsumption">Топливо до&nbsp;объекта</lable>
                      <input type="number" class="form-control gasoline-consumption cost"
                             id="gasolineConsumption"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data["secondment"]['gasoline_consumption_fact'] ?>">
                    </div>
                    <div class="form-group col-sm-4">

                                    <textarea class="form-control mw-100 comment-gasoline-consumption"
                                              id="commentGasolineConsumption"
                                    ><?= $this->data["secondment"]['comment_gasoline_consumption_fact'] == ""
                                            ? "До объекта и обратно" : $this->data["secondment"]['comment_gasoline_consumption_fact'] ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                        <?php foreach ($this->data["fileArr"]["fuel_payment_fact"] as $i => $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/fuel_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
                          </div>
                        <?php endforeach; ?>

                      <div data-js-upload-wrap class="position-relative btn-count-wrap">
                        <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                            <?= count($this->data["fileArr"]["fuel_payment_fact"]) ?>
                        </button>
                      </div>

                    </div>
                  </div>

                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="gasolineConsumption">Топливо по&nbsp;объекту</lable>
                      <input type="number" class="form-control gasoline-consumption cost"
                             id="gasolineConsumptionFact"
                             name="gasoline_consumption_object_fact"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data['secondment']['gasoline_consumption_object_fact'] ?>">
                    </div>
                    <div class="form-group col-sm-4">

                                          <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                    id="commentGasolineConsumption"
                                                    name="comment_gasoline_consumption_object_fact"><?= $this->data['secondment']['comment_gasoline_consumption_object_fact'] == "" ? "По объекту" : $this->data['comment_gasoline_object_consumption'] ?></textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                      <div data-js-upload-wrap class="position-relative">
                        <label class="p-0 text-center"
                               title="Загрузить файлы">
                          <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                          <span data-js-input-count
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                                style="z-index: 100"
                                                          >
                                                          </span>
                          </div>
                          <input
                            multiple
                            class="form-control d-none"
                            type="file"
                            id="edictBtn"
                            name="fuel_payment_object_fact[]"
                            data-js-upload
                          >
                        </label>
                      </div>

                        <?php foreach ($this->data["fileArr"]["fuel_payment_object_fact"] as $i => $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/fuel_payment_object_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_object_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
                          </div>
                        <?php endforeach; ?>

                      <div data-js-upload-wrap class="position-relative btn-count-wrap">
                        <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                            <?= count($this->data["fileArr"]["fuel_payment_object_fact"]) ?>
                        </button>
                      </div>

                    </div>
                  </div>

                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="perDiem">Суточные</lable>
                      <input type="number" class="form-control per-diem cost" id="perDiem"
                             min="0" step="0.01"
                             readonly
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data["secondment"]['per_diem_fact'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiem"><?= trim($this->data["secondment"]['comment_per_diem_fact']) ?>
                                        </textarea>
                    </div>
                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                        <?php foreach ($this->data["fileArr"]["per_diem_fact"] as $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/per_diem_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/per_diem_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
                          </div>
                        <?php endforeach; ?>

                      <div data-js-upload-wrap class="position-relative btn-count-wrap">
                        <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                            <?= count($this->data["fileArr"]["per_diem_fact"]) ?>
                        </button>
                      </div>

                    </div>


                  </div>
                  <div class="row mb-2 align-items-end overflow-hidden">
                    <div class="form-group col-sm-3">
                      <lable for="accommodation">Проживание</lable>
                      <input type="number" class="form-control accommodation cost"
                             id="accommodation"
                             min="0" step="0.01"
                             data-js-format-money
                             autocomplete="off"
                             value="<?= $this->data["secondment"]['accommodation_fact'] ?>">
                    </div>
                    <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodation"><?= $this->data["secondment"]['comment_accommodation_fact'] ?>
                                        </textarea>
                    </div>

                    <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                        <?php foreach ($this->data["fileArr"]["accommodation_fact"] as $file): ?>
                          <div data-js-file-wrap class="position-relative">
                            <a
                              class="btn btn-primary position-relative rounded fa-solid fa-file"
                              href="/ulab/upload/secondment/accommodation_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              target="_blank"
                              style="margin-left: 4px; font-size: 16px;"
                              title="<?= $file ?>"
                              data-js-file-download

                            ></a>
                            <button
                              data-js-delete-payment-file="/ulab/upload/secondment/accommodation_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                              type="button" class="position-absolute fa-solid fa-xmark"
                              style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                            </button>
                          </div>
                        <?php endforeach; ?>

                      <div data-js-upload-wrap class="position-relative btn-count-wrap">
                        <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                            <?= count($this->data["fileArr"]["accommodation_fact"]) ?>
                        </button>
                      </div>

                    </div>
                  </div>

                  <lable for="other">Прочее</lable>
                    <?php foreach ($this->data['other_fields'] as $i => $field): ?>
                      <div class="row mb-2 align-items-end overflow-hidden">
                        <div class="form-group col-sm-3">
                          <input type="number" value="<?= $field["id"] ?>" class="d-none">
                          <input type="number" class="form-control other cost" id="other"
                                 min="0" step="0.01"
                                 data-js-format-money
                                 autocomplete="off"
                                 value="<?= $field["sum_fact"] ?>">
                        </div>
                        <div class="form-group col-sm-4">
                          <textarea class="form-control mw-100 comment-other" id="commentOther"><?= $field['comment_fact'] ?></textarea>
                        </div>
                        <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >
                            <?php foreach ($this->data["fileArr"]["other_fact"][$i] as $file): ?>
                              <div data-js-file-wrap class="position-relative">
                                <a
                                  class="btn btn-primary position-relative rounded fa-solid fa-file"
                                  href="/ulab/upload/secondment/other_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                  target="_blank"
                                  style="margin-left: 4px; font-size: 16px;"
                                  title="<?= $file ?>"
                                  data-js-file-download

                                ></a>
                                <button
                                  data-js-delete-payment-file="/ulab/upload/secondment/other_fact/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                  type="button" class="position-absolute fa-solid fa-xmark"
                                  style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                </button>
                              </div>
                            <?php endforeach; ?>

                          <div data-js-upload-wrap class="position-relative btn-count-wrap">
                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                <?= count($this->data["fileArr"]["other_fact"][$i]) ?>
                            </button>
                          </div>

                        </div>
                      </div>
                    <?php endforeach; ?>

                  <strong class="mb-2 d-block">Дополнительные расходы</strong>

                    <?php foreach ($this->data['additional_fields'] as $i => $field): ?>
                      <div class="row mb-2 align-items-end overflow-hidden">
                        <div class="form-group col-sm-3">

                          <input type="number" name="additional_id[]" value="<?= $field["id"] ?>" class="d-none">
                          <input type="number" class="form-control other cost" id="other"
                                 name="additional[]"
                                 min="0" step="0.01"
                                 data-js-format-money
                                 autocomplete="off"
                                 value="<?= $field["sum"] ?>">
                        </div>
                        <div class="form-group col-sm-4">
                                    <textarea class="form-control mw-100 comment-other" id="commentAdditional"
                                              name="comment_additional[]"><?= $field['comment'] ?></textarea>
                        </div>
                        <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group >



                            <?php foreach ($this->data["fileArr"]["additional"][$i] as $file): ?>
                              <div data-js-file-wrap class="position-relative">
                                <a
                                  class="btn btn-primary position-relative rounded fa-solid fa-file"
                                  href="/ulab/upload/secondment/additional/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                  target="_blank"
                                  style="margin-left: 4px; font-size: 16px;"
                                  title="<?= $file ?>"
                                  data-js-file-download

                                ></a>
                                <button
                                  data-js-delete-payment-file="/ulab/upload/secondment/additional/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                                  type="button" class="position-absolute fa-solid fa-xmark"
                                  style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                                </button>
                              </div>
                            <?php endforeach; ?>

                          <div data-js-upload-wrap class="position-relative btn-count-wrap">
                            <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                                <?= count($this->data["fileArr"]["additional"][$i]) ?>
                            </button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>


                  <div class="row mb-2" style="margin-top: 70px">
                    <div class="form-group col-sm-6">
                      <lable for="totalSpent">Всего потрачено</lable>
                      <div class="input-group">
                        <input type="number" class="form-control total-spent" id="totalSpent"
                               name="total_spent" value="<?= $this->data['total_spent'] ?>"
                               aria-describedby="spent" min="0" step="0.01">

                      </div>
                    </div>
                    <div class="form-group col-sm-6">
                      <lable for="overspending">Перерасход %</lable>
                      <input type="number" class="form-control overspending
                                            <?= $this->data['overspending'] > 20 ? 'border-red' : '' ?>" id="overspending"
                             name="overspending" step="0.01"
                             value="<?= $this->data['overspending'] ?>" readonly>
                      <div id="userCheckedOverspending" class="form-text">
                          <?= $this->data['user_checked_overspending']['short_name'] ?>
                          <?= $this->data['user_checked_overspending']['date'] ?>
                      </div>
                    </div>
                  </div>


                  <strong class="mb-2 d-block">Данные отчёта</strong>

                  <div class="row mb-2">
                    <div class="form-group col-sm-6">
                      <lable for="comment">Комментарий сотрудника</lable>
                      <textarea class="form-control mw-100 comment" id="comment"
                                name="comment"><?= $this->data['comment'] ?></textarea>
                    </div>
                    <div class="form-group col-sm-6">
                      <lable for="memo">Служебная записка</lable>
                      <textarea class="form-control mw-100 memo" id="memo"
                                name="memo"><?= $this->data['memo'] ?></textarea>
                    </div>


                  </div>

                  <?php if (isset($this->data["compensationItem"]["sum"])): ?>
                    <div>Компенсация: <b><span><?= $this->data["compensationItem"]["sum"] ?></span></b></div>
                  <?php endif; ?>
                </div>

          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->

    <form id="add-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
          action="/ulab/secondment/insertUpdateInfo/" method="post">
        <h3>
            Напишите причину доработки
        </h3>

        <div class="line-dashed-small"></div>
        <textarea name="improvement_reason" id="" style="min-height: 200px">
            <?= $this->data['secondment']["improvement_reason"] ?>
        </textarea>
        <div class="row mb-3">

        </div>

        <div class="line-dashed-small"></div>

        <button type="button"
                class="btn btn-primary"
                id="returnSecondment"
                name="return_secondment"
                data-stage="Нужна доработка"
        >
            Отправить
        </button>
   </form>

    <div id="change-stage" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <h3>
            Напишите причину отмены
        </h3>

        <div class="line-dashed-small"></div>
        <textarea name="cancel_reason" id="" style="min-height: 200px"><?= $this->data['secondment']["cancel_reason"] ?></textarea>
        <div class="row mb-3">

        </div>

        <div class="line-dashed-small"></div>

        <button type="button"
                class="btn btn-primary"
                name="stage"
                data-stage="Отменена"
                id="cancelStage"
        >
            Отправить
        </button>
    </div>

    <!--  Продление командировки - модальное окно   -->
    <form id="extend-secondment" enctype="multipart/form-data" method="post" action="/ulab/secondment/extend/" class="bg-light mfp-hide col-md-8 m-auto p-3 position-relative">
        <?php if (!empty($this->data['secondment']['s_id'])): ?>
          <input class="secondment-id" type="hidden" name="secondment_id"
                 value="<?= $this->data['secondment']['s_id'] ?? '' ?>">
        <?php endif; ?>
      <h3>
        Продление командировки
      </h3>
      <div class="line-dashed-small"></div>
      <strong class="mb-2 d-block">Дата</strong>

      <div class="row mb-2">
        <div class="form-group col-md-4">
          <lable for="dateBeginning">Дата начала<span class="redStars">*</span>
          </lable>
          <input type="date" class="form-control date-begin" id="dateBeginning"
                 name="date_begin"
                 value="<?= $this->data['date_begin'] ?>" required>
        </div>
        <div class="form-group col-md-4">
          <lable for="dateEnding">Дата окончания<span class="redStars">*</span>
          </lable>
          <input type="date" class="form-control date-ending" id="dateEnding"
                 name="date_end" value="<?= $this->data['date_end'] ?>" required>
        </div>
        <div class="form-group col-md-2">
          <lable for="totalDays">Всего</lable>
          <div class="input-group total-days-wrapper
                                    <?= $this->data['total_days'] < 0 ? 'border border-red' : '' ?>">
            <input type="text" class="form-control number-only total-days" id="totalDays"
                   name="total_days" value="<?= $this->data['total_days'] ?>"
                   aria-describedby="basic-addon2" readonly>
          </div>
        </div>
      </div>

      <div class="row mb-2">
        <div class="extra-wrapper mb-4" data-js-extend-card>
          <strong class="mb-2 d-block">Запланированные расходы</strong>
          <input type="text" name="file_payment_delete" value="" style="display: none">
          <div class="row mb-2 align-items-end overflow-hidden">
            <div class="form-group col-sm-3">
              <lable for="ticketPrice">Билеты</lable>
              <input type="number" class="form-control ticket-price cost"
                     id="ticketPrice"
                     name="ticket_price"
                     min="0" step="0.01"
                     data-js-format-money
                     value="0">
            </div>
            <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-ticket-price" id="commentTicketPrice"
                                                  name="comment_ticket_price"
                                                  title="<?= trim($this->data['comment_ticket_price']) ?>"
                                        ><?= trim($this->data['comment_ticket_price']) ?></textarea>
            </div>
            <div class="form-group col-sm-4 d-flex align-items-end" data-js-btn-group>

              <div data-js-upload-wrap class="position-relative">
                <label class="p-0 text-center"
                       title="Загрузить билеты">
                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                    <span data-js-input-count
                                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                          style="z-index: 100"
                                                    >
                                                    </span>
                  </div>
                  <input
                    multiple
                    class="form-control d-none"
                    type="file"
                    id="edictBtn"
                    name="ticket_payment[]"
                    data-js-upload
                  >
                </label>
              </div>

                <?php foreach ($this->data["fileArr"]["ticket_payment"] as $ticket): ?>
                  <div data-js-file-wrap class="position-relative">
                    <a
                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                      href="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                      target="_blank"
                      style="margin-left: 4px; font-size: 16px;"
                      title="<?= $ticket ?>"
                      data-js-file-download

                    ></a>
                    <button
                      data-js-delete-payment-file="/ulab/upload/secondment/ticket_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $ticket ?>"
                      type="button" class="position-absolute fa-solid fa-xmark"
                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                    </button>
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
              <lable for="gasolineConsumption">Топливо до&nbsp;объекта</lable>
              <input type="number" class="form-control gasoline-consumption cost"
                     id="gasolineConsumption"
                     name="gasoline_consumption"
                     min="0" step="0.01"
                     data-js-format-money
                     autocomplete="off"
                     value="">
            </div>
            <div class="form-group col-sm-4">

                                    <textarea class="form-control mw-100 comment-gasoline-consumption"
                                              id="commentGasolineConsumption"
                                              name="comment_gasoline_consumption"><?= $this->data['comment_gasoline_consumption'] == "" ? "До объекта и обратно" : $this->data['comment_gasoline_consumption'] ?></textarea>
            </div>
            <div class="form-group col-sm-4 d-flex align-items-end" data-js-btn-group >
              <div data-js-upload-wrap class="position-relative">
                <label class="p-0 text-center"
                       title="Загрузить файлы">
                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                    <span data-js-input-count
                                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                          style="z-index: 100"
                                                    >
                                                    </span>
                  </div>
                  <input
                    multiple
                    class="form-control d-none"
                    type="file"
                    id="edictBtn"
                    name="fuel_payment[]"
                    data-js-upload
                  >
                </label>
              </div>

                <?php foreach ($this->data["fileArr"]["fuel_payment"] as $i => $file): ?>
                  <div data-js-file-wrap class="position-relative">
                    <a
                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                      href="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      target="_blank"
                      style="margin-left: 4px; font-size: 16px;"
                      title="<?= $file ?>"
                      data-js-file-download

                    ></a>
                    <button
                      data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      type="button" class="position-absolute fa-solid fa-xmark"
                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                    </button>
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
              <lable for="gasolineConsumptionObject">Топливо по&nbsp;объекту</lable>
              <input type="number" class="form-control gasoline-consumption cost"
                     id="gasolineConsumptionObject"
                     name="gasoline_consumption_object"
                     min="0" step="0.01"
                     data-js-format-money
                     autocomplete="off"
                     value="">
            </div>
            <div class="form-group col-sm-4">

                                      <textarea class="form-control mw-100 comment-gasoline-consumption"
                                                id="commentGasolineConsumptionObject"
                                                name="comment_gasoline_consumption_object"><?= $this->data['comment_gasoline_consumption_object'] == "" ? "По объекту" : $this->data['comment_gasoline_consumption_object'] ?></textarea>
            </div>
            <div class="form-group col-sm-4 d-flex align-items-end" data-js-btn-group >
              <div data-js-upload-wrap class="position-relative">
                <label class="p-0 text-center"
                       title="Загрузить файлы">
                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                      <span data-js-input-count
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                            style="z-index: 100"
                                                      >
                                                      </span>
                  </div>
                  <input
                    multiple
                    class="form-control d-none"
                    type="file"
                    id="edictBtn"
                    name="fuel_payment_object[]"
                    data-js-upload
                  >
                </label>
              </div>

                <?php foreach ($this->data["fileArr"]["fuel_payment_object"] as $i => $file): ?>
                  <div data-js-file-wrap class="position-relative">
                    <a
                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                      href="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      target="_blank"
                      style="margin-left: 4px; font-size: 16px;"
                      title="<?= $file ?>"
                      data-js-file-download

                    ></a>
                    <button
                      data-js-delete-payment-file="/ulab/upload/secondment/fuel_payment_object/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      type="button" class="position-absolute fa-solid fa-xmark"
                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                    </button>
                  </div>
                <?php endforeach; ?>

              <div data-js-upload-wrap class="position-relative btn-count-wrap">
                <button data-js-download type="button" class="btn btn-primary position-relative rounded">
                    <?= count($this->data["fileArr"]["fuel_payment_object"]) ?>
                </button>
              </div>

            </div>
          </div>

          <div class="row mb-2 align-items-end overflow-hidden">
            <div class="form-group col-sm-3">
              <lable for="perDiem">Суточные</lable>
              <input type="number" class="form-control per-diem cost" id="perDiem"
                     name="per_diem"
                     min="0" step="0.01"
                     readonly
                     data-js-format-money
                     autocomplete="off"
                     value="<?= $this->data['per_diem'] ?>">
            </div>
            <div class="form-group col-sm-4">
                                        <textarea class="form-control mw-100 comment-per-diem" id="commentPerDiem"
                                                  name="comment_per_diem"><?= trim($this->data['comment_per_diem']) ?>
                                        </textarea>
            </div>
            <div class="form-group col-sm-4 d-flex align-items-end align-items-end" data-js-btn-group >
              <div data-js-upload-wrap class="position-relative">
                <label class="p-0 text-center"
                       title="Загрузить билеты">
                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                <span data-js-input-count
                                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                      style="z-index: 100"
                                                >
                                                </span>
                  </div>
                  <input
                    multiple
                    class="form-control d-none"
                    type="file"
                    id="edictBtn"
                    name="per_diem[]"
                    data-js-upload
                  >
                </label>
              </div>

                <?php foreach ($this->data["fileArr"]["per_diem"] as $file): ?>
                  <div data-js-file-wrap class="position-relative">
                    <a
                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                      href="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      target="_blank"
                      style="margin-left: 4px; font-size: 16px;"
                      title="<?= $file ?>"
                      data-js-file-download

                    ></a>
                    <button
                      data-js-delete-payment-file="/ulab/upload/secondment/per_diem/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      type="button" class="position-absolute fa-solid fa-xmark"
                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                    </button>
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
              <input type="number" class="form-control accommodation cost"
                     id="accommodation"
                     name="accommodation"
                     min="0" step="0.01"
                     data-js-format-money
                     autocomplete="off"
                     value="">
            </div>
            <div class="form-group col-sm-4">
                                    <textarea class="form-control mw-100 comment-accommodation" id="commentAccommodation"
                                              name="comment_accommodation"><?= $this->data['comment_accommodation'] ?></textarea>
            </div>

            <div class="form-group col-sm-4 d-flex align-items-end" data-js-btn-group >
              <div data-js-upload-wrap class="position-relative">
                <label class="p-0 text-center"
                       title="Загрузить билеты">
                  <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                <span data-js-input-count
                                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                      style="z-index: 100"
                                                >
                                                </span>
                  </div>
                  <input
                    multiple
                    class="form-control d-none"
                    type="file"
                    id="edictBtn"
                    name="accommodation[]"
                    data-js-upload
                  >
                </label>
              </div>

                <?php foreach ($this->data["fileArr"]["accommodation"] as $file): ?>
                  <div data-js-file-wrap class="position-relative">
                    <a
                      class="btn btn-primary position-relative rounded fa-solid fa-file"
                      href="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      target="_blank"
                      style="margin-left: 4px; font-size: 16px;"
                      title="<?= $file ?>"
                      data-js-file-download

                    ></a>
                    <button
                      data-js-delete-payment-file="/ulab/upload/secondment/accommodation/<?= $this->data['secondment']['s_id'] ?>/<?= $file ?>"
                      type="button" class="position-absolute fa-solid fa-xmark"
                      style="color: red; border: none; background: transparent; right: -15px; top: -10px; z-index: 100">
                    </button>
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
            <?php foreach ($this->data['other_fields'] as $i => $field): ?>
              <div class="row mb-2 align-items-end overflow-hidden">
                <div class="form-group col-md-3">

                  <input type="number" name="other_id[]" value="<?= $field["id"] ?>" class="d-none">
                  <input type="number" class="form-control other cost" id="other"
                         name="other[]"
                         min="0" step="0.01"
                         data-js-format-money
                         autocomplete="off"
                         value="0">
                </div>
                <div class="form-group col-md-4">
                                    <textarea class="form-control mw-100 comment-other" id="commentOther"
                                              name="comment_other[]"><?= $field['comment'] ?></textarea>
                </div>
                <div class="form-group col-md-4 d-flex align-items-end" data-js-btn-group >


                  <div data-js-upload-wrap class="position-relative">
                    <label class="p-0 text-center"
                           title="Загрузить билеты">
                      <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                                                <span data-js-input-count
                                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded"
                                                      style="z-index: 100"
                                                >
                                                </span>
                      </div>
                      <input
                        multiple
                        class="form-control d-none"
                        type="file"
                        id="edictBtn"
                        name="other[<?= $i ?>][]"
                        data-js-upload
                      >
                    </label>
                  </div>

                    <?php foreach ($this->data["fileArr"]["other"][$i] as $file): ?>
                      <div data-js-file-wrap class="position-relative">
                        <a
                          class="btn btn-primary position-relative rounded fa-solid fa-file"
                          href="/ulab/upload/secondment/other/<?= $this->data['secondment']['s_id'] ?>/<?= $field["id"] ?>/<?= $file ?>"
                          target="_blank"
                          style="margin-left: 4px; font-size: 16px;"
                          title="<?= $file ?>"
                          data-js-file-download

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


<!--          <div class="form-group">-->
<!--            <button data-js-add-other type="button" class="btn btn-primary fa-solid fa-plus"></button>-->
<!--          </div>-->

          <div class="row mb-2 align-items-end overflow-hidden d-none">
            <div class="form-group col-sm-3">
              <lable for="extendExpenses">Итого</lable>
              <input type="text" class="form-control planned-expenses" id="extendExpenses"
                     name="planned_expenses"
                     min="0" step="0.01"
                     autocomplete="off"
                     value="<?= $this->data['planned_expenses'] ?>" readonly>
            </div>

          </div>


        </div>
      </div>

      <input type="text" id="json_data" name="json_data" style="display: none">

      <div class="line-dashed-small"></div>

      <button type="submit"
              class="btn btn-primary"
              name="stage"
              data-stage="Подготовка приказа и СЗ"
              id="btn-extend"
      >
        Сохранить
      </button>
    </form>


    <?php if ($this->data['stage_name'] !== 'Завершена'): ?>
    <div class="btn-group-toolbar d-flex gap-3">
        <?php if ($this->data['is_save_info'] && in_array($this->data['stage_name'],
                ['Новая', 'Нужна доработка'])): ?>
            <div class="col flex-grow-0 text-nowrap">
                <button data-js-save-info type="button" class="btn btn-primary">Сохранить</button>
            </div>
            <div class="col flex-grow-0 text-nowrap">
                <button type="button" class="btn btn-primary min-w-300" id="sendApprove"
                        name="send_approve" data-stage="Ожидает подтверждения">
                    Отправить на согласование
                </button>
            </div>
            <div class="col flex-grow-0 text-nowrap">
                <button type="button" data-js-delete-card="<?= $this->data['secondment']['s_id'] ?>" class="btn btn-danger">
                    Удалить</button>
            </div>
        <?php endif; ?>


        <?php if ($this->data['is_may_save_files']): ?>
            <?php if (in_array($this->data['stage_name'], ['Подготовка приказа и СЗ'])): ?>
                <button data-js-save-files type="button" class="btn btn-primary min-w-200" id="saveUploadFiles"
                        name="save_upload_files" data-stage="Согласована">Сохранить приказ и СЗ
                </button>
            <?php endif; ?>

            <?php if ($this->data['stage_name'] === 'Подготовка приказа и СЗ'): ?>
                <button data-js-save-files type="button" class="btn btn-primary min-w-200" id="saveUploadFiles"
                        name="stage_ready" data-stage="Согласована">Готово
                </button>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->data['is_save_report']): ?>
            <?php if ($this->data['stage_name'] === 'Подготовка отчета'): ?>
                <button data-js-save-report type="button" class="btn btn-primary">Сохранить</button>
                <button type="button" class="btn btn-primary min-w-300" id="sendVerify"
                        name="send_verify" data-stage="Проверка отчета">
                    Отправить на проверку
                </button>

            <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->data['is_confirm_report']): ?>
            <?php if ($this->data['stage_name'] === 'Проверка отчета'
                && empty($this->data['confirmation_current_user'])
                && ($this->data["is_root"] || $this->data["is_manager"])): ?>
                <button type="button" class="btn btn-primary" id="confirmReport"
                        name="confirm_report" data-stage="Отчет подтвержден">Подтвердить
                </button>

                <button type="button" class="btn btn-primary min-w-300" id="expensesNotVerified"
                        name="expenses_not_verified" data-stage="Отчет не подтвержден">
                    Отклонить отчёт
                </button>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->data['is_confirm_secondment'] &&
            $this->data['stage_name'] === 'Ожидает подтверждения'): ?>
            <button type="button" class="btn btn-primary" id="confirmSecondment"
                    name="confirm_secondment" data-stage="Подготовка приказа и СЗ">
                Подтвердить
            </button>
            <button data-js-rework type="button" class="btn btn-primary text-nowrap">
                Вернуть&nbsp;на&nbsp;доработку
            </button>
            <button type="button" class="btn btn-primary" id="rejectSecondment"
                    name="reject_secondment" data-stage="Отклонена">Отклонить
            </button>
        <?php elseif ($this->data['is_confirm_secondment'] && $this->data['stage_name'] !== 'Нужна доработка'
            && $this->data['stage_name'] !== 'Новая'
            && $this->data['stage_name'] !== 'Подготовка отчета'): ?>
            <button data-js-rework type="button" class="btn btn-danger">
                Вернуть на доработку
            </button>
        <?php endif; ?>

        <?php if ($this->data['is_confirm_secondment'] && $this->data['stage_name'] !== 'Отменена'): ?>
            <button id="cancel-stage-toggle" type="button" data-js-change-stage="Отменена" class="btn btn-danger">
                Отменить
            </button>
        <?php endif; ?>
        <?php if ($this->data['is_confirm_secondment'] && $this->data['stage_name'] !== 'Проверка отчета'): ?>
            <button data-js-extend type="button" class="btn btn-primary">Продлить</button>
        <?php endif ?>
    </div>
    <?php endif ?>


 </div>

<form id="memo-modal" enctype="multipart/form-data" method="post" action="/ulab/secondment/" class="bg-light mfp-hide col-md-8 m-auto p-3 position-relative">
    <input type="number" name="secondment_id" value="<?= $this->data['secondment']['s_id'] ?>" hidden>
    <h3>
        Служебная записка
    </h3>
    <div class="line-dashed-small"></div>
    <h4>Отчет по поездкам</h4>
    <table id="gsm-report-table" class="table table-bordered"
        <thead>
            <tr>
                <th class="text-center w100">Км</th>
                <th class="text-center w100">ГСМ, л.</th>
                <th class="text-center w100">Цена, р.</th>
                <th class="text-center">Объект</th>
            </tr>
        </thead>

    </table>
    <div class="d-flex justify-content-center">
        <button id="gsm-report-add" type="button" class="btn btn-primary rounded fa-solid fa-plus"></button>
    </div>
    <div class="line-dashed-small"></div>
    <h4>Чеки</h4>
    <table id="transport-report-table" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center w100">№ чека</th>
                <th class="text-center">Место</th>
                <th class="text-center w100">Дата</th>
                <th class="text-center w100">Сумма</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        <button id="transport-report-add" type="button" class="btn btn-primary rounded fa-solid fa-plus"></button>
    </div>

    <div class="line-dashed-small"></div>
    <div class="d-flex">
        <button id="send-memo" type="button" class="btn btn-primary rounded">
            Сформировать&nbsp;документ
        </button>
    </div>
</form>


