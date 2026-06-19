<style>
    .nav-outline-active {
        border: 1px solid #0d6efd;
        color: #0d6efd;
    }

    .nav-fill-active {
        background-color: #0d6efd;
        color: #ffffff;
    }

    .custom-nav-link {
        width: 100%;
    }

    .custom-nav {
        --bs-nav-link-padding-x: 1rem;
        --bs-nav-link-padding-y: 0.5rem;
        --bs-nav-link-font-weight: ;
        --bs-nav-link-hover-color: var(--bs-link-hover-color);
        --bs-nav-link-disabled-color: var(--bs-secondary-color);
        display: flex;
        flex-wrap: wrap;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }

    div.btn-primary {
        font-size: 14px;
        width: 195px;
    }
</style>

<header class="header-requirement mb-3">
	<nav class="header-menu">
		<ul class="nav">
			<li class="nav-item me-2">
				<a class="nav-link" href="<?= URI ?>/order/newList/" title="Вернуться к списку">
					<svg class="icon" width="20" height="20">
						<use xlink:href="<?= URI ?>/assets/images/icons.svg#list"/>
					</svg>
				</a>
			</li>
			<li class="nav-item me-2">
				<a class="nav-link link-card" href="<?= URI ?>/order/newList/" title="Вернуться в карточку заявки">
					<svg class="icon" width="20" height="20">
						<use xlink:href="/ulab/assets/images/icons.svg#card"></use>
					</svg>
				</a>
			</li>
			<li class="nav-item me-2">
				<a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Contract_card/Contract_card.html"
				   title="ПОМОГИТЕ">
					<i class="fa-solid fa-question"></i>
				</a>
			</li>
		</ul>
	</nav>
</header>

<h2 class="d-flex mb-3">
	<?= $this->data['order']['company'] ?>
</h2>

<form class="form-horizontal" method="post" id="contract" action="<?= URI ?>/order/changeOrder/" enctype="multipart/form-data">
	<input type="hidden" id="input_order_id" name="orderID" value="<?= $this->data['order']['id'] ?>">

	<div class="panel panel-default"
		 style="<?= $this->data['order']['action'] ? '' : 'background-color: #ff3d3d45;' ?>">
		<header class="panel-heading">
			Основная информация
			<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
		</header>
		<div class="panel-body">
			<div class="input-group mb-3">
				<input type="text" name="CONTRACT_TYPE" class="form-control CONTRACT_TYPE"
					   value="<?= $this->data['order']['type'] ?>" <?= $this->data['order']['client_number'] == 1 ? '' : 'readonly' ?>>
				<span class="input-group-text">№</span>
				<input type="text" class="form-control NUMBER_EDIT" name="NUMBER"
					   value="<?= $this->data['order']['number_client'] ? $this->data['order']['number_client'] : $this->data['order']['number'] ?>" <?= $this->data['order']['custom_number'] == 1 ? '' : 'readonly' ?>>
                <input type="hidden" name="lims_number" value="<?= $this->data['order']['number'] ?>">
				<span class="input-group-text">от</span>
				<input type="date" class="form-control DATE_EDIT" name="DATE"
					   value="<?= $this->data['order']['date'] ?>" <?= $this->data['order']['custom_number'] == 1 ? '' : 'readonly' ?>>
			</div>

			<div>Тип договора:</div>

            <div class="mb-3">
                <?php foreach ($this->data['order_types'] as $value): ?>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="btn-check type-dogovor" data-js-name="<?= $value['name'] ?>" name="dogovor_type_id" id="dogovor_type_<?= $value['id'] ?>" value="<?= $value['id'] ?>"
                               autocomplete="off" <?= $this->data['order']['dogovor_type_id'] == $value['id'] ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" style="max-width: 1000px;" for="dogovor_type_<?= $value['id'] ?>"><?= $value['name'] ?></label>
                    </div>
                <?php endforeach; ?>
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="btn-check" name="custom_number" id="is_custom"
                           autocomplete="off" <?= $this->data['order']['custom_number'] ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" style="max-width: 1000px;" for="is_custom">Номер клиента</label>
                </div>
            </div>

<!--			<div class="mb-3">-->
<!--				<div class="form-check form-check-inline">-->
<!--					<input type="checkbox" class="btn-check" name="LONGTERM" id="LONGTERM"-->
<!--						   autocomplete="off" --><?//= $this->data['order']['longterm'] == 1 ? 'checked' : '' ?><!--
					<label class="btn btn-outline-primary" for="LONGTERM">Абонентский</label>-->
<!--				</div>-->
<!--				<div class="form-check form-check-inline">-->
<!--					<input type="checkbox" class="btn-check" name="CLIENT_NUMBER" id="CLIENT_NUMBER"-->
<!--						   autocomplete="off" --><?//= ($this->data['order']['client_number'] == 1 || $this->data['order']['custom_number'] == 1) ? 'checked' : '' ?><!--
				<label class="btn btn-outline-primary" for="CLIENT_NUMBER">Договор клиента</label>-->
<!--                    <input type="hidden" id="NORMAL_NUMBER" value="--><?//= $this->data['order']['NUMBER'] ?><!--">-->
<!--				</div>-->
<!--				<div class="form-check form-check-inline">-->
<!--					<input type="checkbox" class="btn-check" name="dogovor_type_id_1" id="dogovor_type_id_1"-->
<!--						   autocomplete="off" --><?//= ($this->data['order']['dogovor_type_id'] == 1 || $this->data['order']['type_id'] == 1) ? 'checked' : '' ?><!--
					<label class="btn btn-outline-primary" for="dogovor_type_id_1">Рамочный</label>-->
<!--				</div>-->
<!--				<div class="form-check form-check-inline">-->
<!--					<input type="checkbox" class="btn-check" name="dogovor_type_id_4" id="dogovor_type_id_4"-->
<!--						   autocomplete="off" --><?//= ($this->data['order']['dogovor_type_id'] == 4) ? 'checked' : '' ?><!--
					<label class="btn btn-outline-primary" for="dogovor_type_id_4">С нерезидентом</label>-->
<!--				</div>-->
<!--				<div class="form-check form-check-inline">-->
<!--					<input type="checkbox" class="btn-check" name="FLOW_DATE" id="FLOW_DATE"-->
<!--						   autocomplete="off">--><?////= $this->data['order']['longterm'] == 1 ? '' : 'checked' ?>
<!--    					<label class="btn btn-outline-primary" for="FLOW_DATE">На 11 месяцев</label>-->
<!--				</div>-->
<!--			</div>-->

			<div>Принадлежность к подразделению:</div>

			<div class="mb-3">
                <?php foreach($this->data['dogovor_belonging'] as $value): ?>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="btn-check" name="belonging_id" id="belonging_<?= $value['id'] ?>" value="<?= $value['id'] ?>"
                               autocomplete="off" <?= $this->data['order']['belonging_id'] == $value['id'] ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="belonging_<?= $value['id'] ?>"><?= $value['name'] ?></label>
                    </div>
                <?php endforeach; ?>
			</div>

			<div class="line-dashed"></div>

			<nav class="footer-menu mb-3">
				<ul class="nav">
<!--					<li class="nav-item me-2">-->
<!--						--><?php //if (!empty($this->data['order']['pdf'])): ?>
<!--							<a class="nav-link link-card" href="/pdf/--><?//= $this->data['order']['pdf'] ?><!--"-->
<!--							   title="Скачать подписанную версию">-->
<!--								<svg class="icon" width="30" height="30">-->
<!--									<use xlink:href="/ulab/assets/images/icons.svg#docs"></use>-->
<!--								</svg>-->
<!--							</a>-->
<!--						--><?php //else: ?>
<!--							<label class="upload-pdf cursor-pointer nav-link link-card"-->
<!--								   title="Загрузить">-->
<!--								<svg class="icon" width="30" height="30">-->
<!--									<use xlink:href="--><?//= URI ?><!--/assets/images/icons.svg#upload"/>-->
<!--								</svg>-->
<!--								<input class="d-none upload_pdf" type="file" name="upload_pdf"-->
<!--									   data-dogovor_id="--><?//= $this->data['order']['id'] ?><!--">-->
<!--							</label>-->
<!--						--><?php //endif; ?>
<!--					</li>-->
					<li class="nav-item me-2">
						<a class="nav-link link-card"
						   href="/protocol_generator/archive_dog/<?= $this->data['order']['id'] ?>/<?= $this->data['order']['actual_ver'] ?>.docx"
						   title="Скачать редактируемую версию">
                            <svg width="30px" height="30px" viewBox="0 0 400.00 400.00" xmlns="http://www.w3.org/2000/svg" fill="#000000" stroke="#000000" stroke-width="0.004" transform="matrix(1, 0, 0, 1, 0, 0)rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="3.2"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:#001eff;}</style> </defs> <title></title> <g id="xxx-word"> <path class="cls-1" d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z"></path> <path class="cls-1" d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z"></path> <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z"></path> <path class="cls-1" d="M179.67,182.68,165.41,236H155.33l-10.62-39.22L135.06,236h-9.88l-14.57-53.32h10.2l10.31,38.87,9.61-38.87h9.73l10.63,38.87,10.12-38.87Z"></path> <path class="cls-1" d="M199.08,236.82q-8.75,0-13.36-6.29a23.75,23.75,0,0,1-4.61-14.41,21.32,21.32,0,0,1,5.1-14.57,17,17,0,0,1,13.46-5.82,16.75,16.75,0,0,1,13,5.66q5.1,5.66,5.1,14.73,0,9.34-5.29,15A17.54,17.54,0,0,1,199.08,236.82Zm.31-7.34q9,0,9-13.4,0-6.05-2.15-9.55a7.21,7.21,0,0,0-6.6-3.5,7.47,7.47,0,0,0-6.84,3.61q-2.23,3.61-2.23,9.59,0,6.45,2.36,9.84A7.46,7.46,0,0,0,199.39,229.48Z"></path> <path class="cls-1" d="M234.86,236H226V196.55h8V206q1.72-5.51,4.73-8a9.52,9.52,0,0,1,6.17-2.54l1.17,0V205q-6.8,0-9,4.34a18.47,18.47,0,0,0-2.21,8.4Z"></path> <path class="cls-1" d="M284.9,236h-8.32v-8q-3.44,8.79-11.64,8.79a12.43,12.43,0,0,1-11.13-6.05q-3.87-6.05-3.87-15a26.2,26.2,0,0,1,4-14.57,12.86,12.86,0,0,1,11.45-6.21q7.62,0,10.59,7V182.68h8.91ZM276,212.72q0-4.8-2.29-7.48a7.42,7.42,0,0,0-5.92-2.68,7,7,0,0,0-6.17,3.44q-2.23,3.44-2.23,10.27,0,13.2,8.28,13.2a7.58,7.58,0,0,0,5.8-2.83A10.49,10.49,0,0,0,276,219.4Z"></path> </g> </g></svg>
						</a>
					</li>
					<li class="nav-item me-2">
						<a class="nav-link link-card"
						   href="/protocol_generator/archive_dog/<?= $this->data['order']['id'] ?>/<?= $this->data['order']['actual_ver'] ?>.pdf"
						   title="Скачать pdf версию">
                            <svg height="30px" width="30px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#ff0000" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#ff0000;} </style> <g> <path class="st0" d="M347.746,346.204c-8.398-0.505-28.589,0.691-48.81,4.533c-11.697-11.839-21.826-26.753-29.34-39.053 c24.078-69.232,8.829-88.91-11.697-88.91c-16.119,0-24.167,17.011-22.376,35.805c0.906,9.461,8.918,29.34,18.78,48.223 c-6.05,15.912-16.847,42.806-27.564,62.269c-12.545,3.812-23.305,8.048-31.027,11.622c-38.465,17.888-41.556,41.773-33.552,51.894 c15.197,19.226,47.576,2.638,80.066-55.468c22.243-6.325,51.508-14.752,54.146-14.752c0.304,0,0.721,0.097,1.204,0.253 c16.215,14.298,35.366,30.67,51.128,32.825c22.808,3.136,35.791-13.406,34.891-23.692 C382.703,361.461,376.691,347.942,347.746,346.204z M203.761,408.88c-9.401,11.178-24.606,21.9-29.972,18.334 c-5.373-3.574-6.265-13.86,5.819-25.497c12.076-11.623,32.29-17.657,35.329-18.787c3.59-1.337,4.482,0,4.482,1.791 C219.419,386.512,213.154,397.689,203.761,408.88z M244.923,258.571c-0.899-11.192,1.33-21.922,10.731-23.26 c9.386-1.352,13.868,9.386,10.292,26.828c-3.582,17.464-5.38,29.08-7.164,30.44c-1.79,1.338-3.567-3.144-3.567-3.144 C251.627,282.27,245.815,269.748,244.923,258.571z M248.505,363.697c4.912-8.064,17.442-40.702,17.442-40.702 c2.683,4.926,23.699,29.956,23.699,29.956S257.438,360.123,248.505,363.697z M345.999,377.995 c-13.414-1.768-36.221-17.895-36.221-17.895c-3.128-1.337,24.992-5.157,35.79-4.466c13.875,0.9,18.794,6.718,18.794,12.53 C364.362,373.982,359.443,379.787,345.999,377.995z"></path> <path class="st0" d="M461.336,107.66l-98.34-98.348L353.683,0H340.5H139.946C92.593,0,54.069,38.532,54.069,85.901v6.57H41.353 v102.733h12.716v230.904c0,47.361,38.525,85.893,85.878,85.893h244.808c47.368,0,85.893-38.532,85.893-85.893V130.155v-13.176 L461.336,107.66z M384.754,480.193H139.946c-29.875,0-54.086-24.212-54.086-54.086V195.203h157.31V92.47H85.86v-6.57 c0-29.882,24.211-54.102,54.086-54.102H332.89v60.894c0,24.888,20.191,45.065,45.079,45.065h60.886v288.349 C438.855,455.982,414.636,480.193,384.754,480.193z M88.09,166.086v-47.554c0-0.839,0.683-1.524,1.524-1.524h15.108 c2.49,0,4.786,0.409,6.837,1.212c2.029,0.795,3.812,1.91,5.299,3.322c1.501,1.419,2.653,3.144,3.433,5.121 c0.78,1.939,1.182,4.058,1.182,6.294c0,2.282-0.402,4.414-1.19,6.332c-0.78,1.918-1.932,3.619-3.418,5.054 c-1.479,1.427-3.27,2.549-5.321,3.329c-2.036,0.78-4.332,1.174-6.822,1.174h-6.376v17.241c0,0.84-0.683,1.523-1.523,1.523h-7.208 C88.773,167.61,88.09,166.926,88.09,166.086z M134.685,166.086v-47.554c0-0.839,0.684-1.524,1.524-1.524h16.698 c3.173,0,5.968,0.528,8.324,1.568c2.386,1.062,4.518,2.75,6.347,5.009c0.944,1.189,1.694,2.504,2.236,3.916 c0.528,1.375,0.929,2.862,1.189,4.407c0.253,1.531,0.401,3.181,0.453,4.957c0.045,1.694,0.067,3.515,0.067,5.447 c0,1.924-0.022,3.746-0.067,5.44c-0.052,1.769-0.2,3.426-0.453,4.964c-0.26,1.546-0.661,3.025-1.189,4.399 c-0.55,1.427-1.3,2.743-2.23,3.909c-1.842,2.282-3.976,3.969-6.354,5.016c-2.334,1.04-5.135,1.568-8.324,1.568h-16.698 C135.368,167.61,134.685,166.926,134.685,166.086z M214.269,137.981c0.84,0,1.523,0.684,1.523,1.524v6.48 c0,0.84-0.683,1.524-1.523,1.524h-18.244v18.579c0,0.84-0.684,1.523-1.524,1.523h-7.209c-0.84,0-1.523-0.683-1.523-1.523v-47.554 c0-0.839,0.683-1.524,1.523-1.524h27.653c0.839,0,1.524,0.684,1.524,1.524v6.48c0,0.84-0.684,1.524-1.524,1.524h-18.92v11.444 H214.269z"></path> <path class="st0" d="M109.418,137.706c1.212-1.092,1.798-2.645,1.798-4.749c0-2.096-0.587-3.649-1.798-4.741 c-1.263-1.13-2.928-1.68-5.098-1.68h-5.975v12.848h5.975C106.489,139.385,108.155,138.836,109.418,137.706z"></path> <path class="st0" d="M156.139,157.481c1.13-0.424,2.103-1.107,2.973-2.088c0.944-1.055,1.538-2.571,1.769-4.511 c0.26-2.208,0.386-5.091,0.386-8.569c0-3.485-0.126-6.369-0.386-8.569c-0.231-1.946-0.825-3.462-1.762-4.51 c-0.869-0.982-1.873-1.679-2.972-2.089c-1.182-0.453-2.534-0.676-4.042-0.676h-7.164v31.68h7.164 C153.605,158.15,154.965,157.927,156.139,157.481z"></path> </g> </g></svg>
						</a>
					</li>
					<li class="nav-item me-2">
						<a class="nav-link link-card"
						   href="/protocol_generator/dogovor.php?ID=<?= $this->data['dealID'] ?>&TZ_ID=<?= $this->data['tz_id'] ?>&ID_C=<?= $this->data['order']['id'] ?>"
						   title="Сформировать договор">
							<svg class="icon" width="30" height="30">
								<use xlink:href="/ulab/assets/images/icons.svg#edit"></use>
							</svg>
						</a>
					</li>
					<li class="nav-item me-2">
						<a class="nav-link link-card" style="color: black; font-size: 20px"
						   href="/ulab/order/cancelOrder/<?= $this->data['order']['id'] ?>"
						   title="Удалить">
							<i class="fa-solid fa-xmark fa-2xl"></i>
						</a>
					</li>
<!--					<li class="nav-item me-2">-->
<!--                        --><?php //if($this->data['order']['pdf']): ?>
<!--                            <a class="nav-link link-card" style="padding-top: 6px"-->
<!--                               href="/ulab/order/untieOrder/--><?//= $this->data['order']['id'] ?><!--"-->
<!--                               title="Отвязать pdf версию договора">-->
<!--                                <svg fill="#000000" height="35px" width="35px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <g> <path d="M511.997,255.863c-0.028-10.457-0.759-20.89-2.218-31.055c-1.902-13.361-14.312-22.747-27.575-20.724 c-13.312,1.877-22.577,14.239-20.699,27.551c1.146,7.973,1.707,16.189,1.731,24.405c0,0.015,0.001,0.031,0.001,0.046 c-0.051,94.206-76.447,170.572-170.665,170.572c-90.748,0-164.951-70.847-170.34-160.241l31.196,31.196 c9.521,9.521,24.958,9.521,34.48,0s9.521-24.958,0-34.48l-73.143-73.143c-9.521-9.521-24.958-9.521-34.48,0L7.141,263.132 c-9.521,9.521-9.521,24.958,0,34.48c9.521,9.521,24.959,9.521,34.48,0l31.753-31.753 c5.163,116.6,101.325,209.561,219.197,209.561c121.18,0,219.429-98.248,219.429-219.429 C512,255.948,511.997,255.906,511.997,255.863z"></path> <path d="M399.65,123.066c11.093,8.972,21.041,19.285,29.598,30.671c4.779,6.363,12.117,9.752,19.505,9.752 c5.096,0,10.216-1.585,14.629-4.901c10.752-8.07,12.922-23.357,4.852-34.133c-10.947-14.604-23.698-27.819-37.912-39.302 c-10.533-8.46-25.844-6.851-34.328,3.633C387.557,99.27,389.191,114.606,399.65,123.066z"></path> <path d="M133.191,140.913c4.681,4.389,10.654,6.558,16.628,6.558c6.51,0,13.019-2.609,17.822-7.778 c9.728-10.435,20.748-19.627,32.67-27.307c11.337-7.266,14.604-22.382,7.314-33.695c-7.266-11.288-22.382-14.58-33.694-7.314 c-15.336,9.874-29.452,21.675-41.96,35.084C122.78,116.312,123.341,131.745,133.191,140.913z"></path> <path d="M280.33,85.763c14.287-1,28.696-0.244,42.545,2.243c1.463,0.268,2.901,0.39,4.34,0.39 c11.581,0,21.845-8.265,23.966-20.065c2.389-13.239-6.412-25.917-19.675-28.306c-17.798-3.218-36.547-4.169-54.565-2.901 c-13.434,0.951-23.552,12.605-22.626,26.014C255.242,76.571,267.115,86.641,280.33,85.763z"></path> </g> </g> </g> </g></svg>-->
<!--                            </a>-->
<!--                        --><?php //else: ?>
<!--                            <a class="nav-link link-card disabled"-->
<!--                               href=""-->
<!--                               title="Отвязать pdf версию договора">-->
<!---->
<!--                            </a>-->
<!--                        --><?php //endif; ?>
<!--					</li>-->
					<?php if ($this->data['order']['longterm'] == 1): ?>
						<li class="nav-item me-2">
							<?php if (!empty($this->data['client']['price'])): ?>
								<a class="nav-link disable-after-click"
								   href="/price_upd.php?ID_CONTRACT=<?= $this->data['order']['id'] ?>"
								   title="Перейти к прайсу">
									<svg class="icon" width="30" height="30">
										<use xlink:href="<?= URI ?>/assets/images/icons.svg#price"/>
									</svg>
								</a>
							<?php else: ?>
								<a class="nav-link disable-after-click"
								   href="/ulab/order/creatOrderPrice/<?= $this->data['order']['id'] ?>"
								   title="Создать прайс клиент">
									<svg class="icon" width="30" height="30">
										<use xlink:href="<?= URI ?>/assets/images/icons.svg#addPrice"/>
									</svg>
								</a>
							<?php endif; ?>
						</li>
					<?php endif; ?>
				</ul>
			</nav>

            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="open-doc-modal">Документы</button>
            </div>

            <div class="mb-3 d-flex gap-1 align-items-center" style="max-width: 500px">
                <span class="text-nowrap">Срок договора</span>
                <input type="<?= $this->data['order']['date_end'] ? 'date' : 'text'?>" class="form-control" name="date_end" id="date-end" placeholder="Срок договора до" value="<?= $this->data['order']['date_end'] ?>">
                <span class="text-nowrap">Спец. отметки</span>
                <input type="text" id="special-notes" class="form-control" name="special_notes" placeholder="Специальные отметки" value="<?= $this->data['order']['special_notes'] ?>">
            </div>

            <div class="">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
		</div>

	</div>


	<div class="panel panel-default"
		 style="<?= $this->data['order']['action'] ? '' : 'background-color: #ff3d3d45;' ?>">
		<header class="panel-heading">
			Информация о заказчике
			<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
		</header>
		<div class="panel-body">
			<div class="row mb-3">
				<div class="col-4">
					<div>Е-mail:</div>
					<div><strong><?= $this->data['client']['email'] ?></strong></div>
				</div>
				<div class="col-4">
					<div>Телефон:</div>
					<div><strong><?= $this->data['client']['phone'] ?></strong></div>
				</div>
				<div class="col-4">
					<div>Контактное лицо:</div>
					<div><strong><?= $this->data['client']['contact'] ?></strong></div>
				</div>
			</div>
		</div>
	</div>


	<div class="panel panel-default <?= $this->data['order']['longterm'] == 1 ? '' : 'visually-hidden' ?>" id="payment">
		<header class="panel-heading">
			Оплаты по договору
			<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
		</header>
		<div class="panel-body">
			<div class="row mb-3">
				<div class="col-5">
					<div class="row mb-3">
							<label class="col-sm-5 col-form-label">Основная заявка контракта/договора</label>
							<div class="col-sm-6">
								<select class="form-control select2 col-sm-6" name="head_request">
									<option value="">Нет</option>
									<?php foreach ($this->data['request'] as $request): ?>
										<option value="<?= $request['ID_DEAL'] ?>" <?= $this->data['order']['head_request'] == $request['ID_DEAL'] ? 'selected' : '' ?>>
											Заявка <?= $request['REQUEST_TITLE'] ?> от <?= $request['DATE_CREATE'] ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
					</div>
				</div>
				<div class="col-5">
					<div class="row mb-3">
						<label for="contract_summ" class="col-sm-3 col-form-label">Сумма договора:</label>
						<div class="col-sm-6">
							<input type="number" name="CONTRACT_SUMM" class="form-control" id="contract_summ"
								   value="<?= $this->data['order']['summ'] ?>"
								<?= $this->data['order']['longterm'] == 1 ? '' : 'disabled' ?>>
						</div>
					</div>
				</div>
			</div>

				</div>

	</div>


    <div class="panel panel-default"
         style="<?= $this->data['order']['action'] ? '' : 'background-color: #ff3d3d45;' ?>">
        <header class="panel-heading">
            Стоимость договора и условия оплаты
            <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
<!--                <label class="col-auto col-form-label">-->
<!--                    Счет-->
<!--                </label>-->
<!--                <div class="col-auto">-->
<!--                    <input id="finance" type="number" step="0.01" name="" class="form-control" value="--><?php //=$this->data['order']['finance'] ?? '0'?><!--" readonly>-->
<!--                </div>-->
<!--                --><?php //if ($this->data['is_show_finance']): ?>
<!--                    <div class="col-auto">-->
<!--                        <a href="#add-finance" class="btn btn-primary popup-with-form">Добавить</a>-->
<!--                    </div>-->
<!--                --><?php //endif; ?>
<!--                <div class="col-auto">-->
<!--                    <a href="#history-finance" class="btn btn-primary popup-with-form">История</a>-->
<!--                </div>-->
                <label class="col-auto col-form-label">
                    Стоимость договора
                </label>
                <div class="col-auto">
                    <input id="dogovor_price" type="number" step="0.01" name="dogovor_price" class="form-control" value="<?=$this->data['order']['dogovor_price'] ?? '0'?>">
                </div>
                <div class="col-auto">
                    <select name="payment_id" id="payment_select" class="form-select">
                        <?php foreach($this->data['payment_types'] as $value): ?>
                            <option value="<?= $value['id'] ?>" <?= $this->data['payment_type'] == $value['id'] ? 'selected' : ''?>><?= $value['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>


	<div class="panel panel-default"
		 style="<?= $this->data['order']['action'] ? '' : 'background-color: #ff3d3d45;' ?>">
		<header class="panel-heading">
			Заявки по договору:
			<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
		</header>
		<div class="panel-body">

			<div class="filters mb-4">
				<select id="selectStage" class="form-control filter filter-pay">
					<option value="">Фильтр по оплате</option>
					<option value="0">Не оплачено</option>
					<option value="1">Оплачено</option>
				</select>
			</div>

			<table id="journal_order" class="table table-striped journal">
				<thead>
                <tr class="table-light">
					<th>Заявка</th>
					<th>Дата</th>
					<th>Сумма</th>
					<th>Счет</th>
					<th>ТЗ</th>
					<th>ТЗ PDF</th>
					<th>Скидка</th>
					<th>Оплата</th>
					<th></th>
				</tr>
                <tr class="header-search">
                    <th scope="col">
                        <input type="text" class="form-control search">
                    </th>
                    <th scope="col">

                    </th>
                    <th scope="col">
                        <input type="text" class="form-control search">
                    </th>
                    <th scope="col">
                        <input type="text" class="form-control search">
                    </th>
                    <th scope="col">
                    </th>
                    <th scope="col">
                    </th>
                    <th scope="col">
                    </th>
                    <th scope="col">
                        <input type="text" class="form-control search">
                    </th>
                    <th scope="col">
                    </th>
                </tr>
				</thead>
				<tbody>
				</tbody>
			</table>

			<?php if ($this->data['order']['longterm'] == 1):?>
				<div class="mb-3">
					Общая расчетная сумма по заявкам: <b><?= $this->data['cost_contract'] ?></b>
				</div>
				<div class="mb-3">
					Остаток по договору (без заявок ИЦ): <b><?= $this->data['debt_contract'] ?></b>
				</div>
			<?php else:?>
				<div class="mb-3">
					Общая расчетная сумма по всем рабочим заявкам договора: <b><?= $this->data['Cost'] ?></b>
				</div>
				<div class="mb-3">
					Сумма по всем неоплаченным заявкам договора: <b><?= $this->data['Debt'] ?></b>
				</div>
			<?php endif;?>
		</div>
	</div>
</form>

<form id="upd-client" action="/ulab/order/updateClientData" method="post">
    <div class="panel panel-default">
        <header class="panel-heading">
            Реквизиты
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
             </span>
        </header>
        <div class="panel-body">

            <input type="hidden" value="<?=$this->data['company']['id']?>" name="id">
            <input type="hidden" name="orderID" value="<?= $this->data['order']['id'] ?>">

            <div class="form-group row">
                <label for="company" class="col-sm-2 col-form-label">Клиент <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="company" class="form-control" value="<?=$this->data['company']['TITLE'] ?? ''?>">
                    <input type="hidden" name="company_id" id="company-hidden" value="<?= $this->data['company']['id'] ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ИП</label>
                <div class="col-sm-8">
                    <input class="form-check-input check-ip" type="checkbox" name="check_ip" value="1" <?=$this->data['company']['check_ip'] == 1? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Полное наименование компании</label>
                <div class="col-sm-8">
                    <input type="text" name="CompanyFullName" class="form-control clearable" value="<?=$this->data['company']['CompanyFullName'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ИНН</label>
                <div class="col-sm-8">
                    <input type="number" name="INN" class="form-control appearance-none clearable" value="<?=$this->data['company']['INN'] ?? ''?>">
                    <div id="innHelp" class="form-text"></div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">КПП</label>
                <div class="col-sm-8">
                    <input type="text" name="KPP" class="form-control number-only clearable" value="<?=$this->data['company']['KPP'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label for="ogrnip" class="col-sm-2 col-form-label">ОГРНИП</label>
                <div class="col-sm-8">
                    <input type="text" name="OGRNIP" id="ogrnip" class="form-control number-only clearable" <?=$this->data['company']['check_ip'] == 1? '': 'disabled'?> value="<?=$this->data['company']['OGRNIP'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ОГРН</label>
                <div class="col-sm-8">
                    <input type="text" name="OGRN" class="form-control number-only clearable" value="<?=$this->data['company']['OGRN'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Адрес</label>
                <div class="col-sm-8">
                    <input type="text" name="ADDR" class="form-control clearable" value="<?=$this->data['company']['ADDR'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

<!--            <div class="form-group row">-->
<!--                <label class="col-sm-2 col-form-label">Фактический адрес</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input type="text" id="actual_address" name="ACTUAL_ADDRESS" class="form-control clearable" value="--><?php //=$this->data['company']['ACTUAL_ADDRESS'] ?? ''?><!--">-->
<!--                </div>-->
<!--                <div class="col-sm-2"></div>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group row">-->
<!--                <label class="col-sm-2 col-form-label">Почтовый адрес</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input type="text" name="mailingAddress" class="form-control clearable" value="--><?php //=$this->data['company']['mailingAddress'] ?? ''?><!--">-->
<!--                </div>-->
<!--                <div class="col-sm-2"></div>-->
<!--            </div>-->

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">E-mail для договора</label>
                <div class="col-sm-8">
                    <input type="email" name="EMAIL" class="form-control clearable" placeholder="_@_._" value="<?=$this->data['company']['EMAIL'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">E-mail <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="email" name="POST_MAIL" class="form-control clearable" placeholder="_@_._" required value="<?=$this->data['company']['POST_MAIL'] ?? ''?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Телефон <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="PHONE" class="form-control clearable" required value="<?=$this->data['company']['PHONE'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Контактное лицо</label>
                <div class="col-sm-8">
                    <input type="text" name="CONTACT" class="form-control clearable" maxlength="150" value="<?=$this->data['company']['CONTACT'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Должность руководителя</label>
                <div class="col-sm-8">
                    <input type="text" name="Position2" class="form-control clearable" value="<?=$this->data['company']['Position2'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Должность руководителя в родительном падеже</label>
                <div class="col-sm-8">
                    <input type="text" name="PositionGenitive" class="form-control clearable" value="<?=$this->data['company']['PositionGenitive'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ФИО руководителя</label>
                <div class="col-sm-8">
                    <input type="text" name="DirectorFIO" class="form-control clearable" value="<?=$this->data['company']['DirectorFIO'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Действует на основании</label>
                <div class="col-sm-8">
                    <input type="text" name="ACTS_BASIS" class="form-control" value="<?=$this->data['company']['ACTS_BASIS'] ?? 'Устава'?>" >
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Расчетный счет</label>
                <div class="col-sm-8">
                    <input type="text" name="RaschSchet" class="form-control clearable" value="<?=$this->data['company']['RaschSchet'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Кор. счёт</label>
                <div class="col-sm-8">
                    <input type="text" name="KSchet" class="form-control clearable" value="<?=$this->data['company']['KSchet'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Лицевой счёт</label>
                <div class="col-sm-8">
                    <input type="text" name="l_schet" class="form-control clearable" value="<?=$this->data['company']['l_schet'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">БИК</label>
                <div class="col-sm-8">
                    <input type="text" name="BIK" class="form-control clearable" value="<?=$this->data['company']['BIK'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Наименование банка</label>
                <div class="col-sm-8">
                    <input type="text" name="BankName" class="form-control clearable" value="<?=$this->data['company']['BankName'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="mb-3">
                <button form="upd-client" type="submit" class="btn btn-primary">Сохранить</button>
            </div>

        </div>
    </div>
</form>

<?php if ($this->data['is_show_finance']): ?>
    <form id="add-payment" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
          action="/ulab/order/addPayment/" method="post">
        <div class="title mb-3 h-2">
            Оплатить заявку
        </div>
    
        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label">На счету</label>
            <input type="number" step="0.01" min="0" class="form-control" value="<?=$this->data['order']['finance'] ?? '0'?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Необходимо оплатить</label>
            <input type="number" id="need-input" step="0.01" min="0" class="form-control" value="0" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Оплата</label>
            <input type="number" id="money-input" name="money" step="0.01" min="0" class="form-control" value="0" required>
            <input type="hidden" name="orderId" value="<?= $this->data['order']['id'] ?>">
            <input type="hidden" id="deal_id_input" name="deal_id" value="">
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>


    <form id="add-finance" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
          action="/ulab/order/addFinance/" method="post">
        <div class="title mb-3 h-2">
            Данные оплаты
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label">Добавить на счет</label>
            <input type="number" name="money" step="0.01" min="0" class="form-control" value="" required>
            <input type="hidden" name="orderId" value="<?= $this->data['order']['id'] ?>">
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
<?php endif; ?>

<div id="history-finance" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        История финансов
    </div>

    <div class="line-dashed-small"></div>

    <div class="mb-3">
        <div class="row">
            <div class="col"><strong>Дата</strong></div>
            <div class="col"><strong>Событие</strong></div>
            <div class="col"><strong>Заявка</strong></div>
            <div class="col"><strong>Сумма</strong></div>
            <div class="col"><strong>Пользователь</strong></div>
        </div>
        <?php foreach ($this->data['history'] as $row): ?>
            <div class="row">
                <div class="col"><?=$row['date']?></div>
                <div class="col"><?=$row['action']?></div>
                <div class="col"><?=$row['REQUEST_TITLE']?></div>
                <div class="col"><?=$row['money']?></div>
                <div class="col"><?=$row['user_name']?></div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<form id="pay-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
	  action="/ulab/order/setOplata/" method="post">
	<div class="title mb-3 h-2">
		Данные оплаты
	</div>

	<div class="line-dashed-small"></div>

	<div class="mb-3">
		<label class="form-label">Оплата</label>
		<input type="number" name="pay" step="0.01" max="<?= $this->data['Debt_Dog_modal'] ?>" class="form-control"
			   value="<?= $this->data['Debt_Dog_modal'] ?>" required>
	</div>

	<div class="mb-3">
		<label class="form-label">Дата оплаты</label>
		<input type="date" name="payDate" class="form-control" value="<?= date('Y-m-d') ?>">
	</div>

	<input name="order_id" value="<?= $this->data['order']['id'] ?>" type="hidden">

	<div class="line-dashed-small"></div>

	<button type="submit" class="btn btn-primary">Отправить</button>
</form>

<form id="doc-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" style="width: fit-content;"
	  action="" method="post">
	<div class="title mb-3 h-2">
		Прикрепить документы к договору
	</div>

	<div class="line-dashed-small"></div>

    <div class="d-flex gap-5">
        <div class="d-flex flex-column gap-3" id="third-page" style="min-width: 400px; max-width: 400px">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item pages">
                    <a class="nav-link nav-fill-active" href="">Договор</a>
                </li>
            </ul>
            <div class="d-flex gap-3 justify-content-center align-items-center calc-count">
                <input type="number" step="1" class="d-flex justify-content-center align-items-center" style="width: 50px; height: 50px" value="1" readonly>
            </div>
            <div class="d-flex justify-content-between">
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="<?= $this->data['documents'][3]['has_scan'] ? 'Оригинал уже загружен' : 'Загрузить оригинал' ?>">
                    <div class="btn btn-primary <?= $this->data['documents'][3]['has_original'] ? 'disabled' : '' ?>">Загрузить оригинал</div>
                    <input class="d-none upload-document" type="<?= $this->data['documents'][3]['has_original'] ? '' : 'file' ?>" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="3" data-is_scan="0">
                </label>
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="<?= $this->data['documents'][3]['has_scan'] ? 'Скан уже загружен' : 'Загрузить скан' ?>">
                    <div class="btn btn-primary <?= $this->data['documents'][3]['has_scan'] ? 'disabled' : '' ?>" style="width: 198px">Загрузить скан</div>
                    <input class="d-none upload-document" type="<?= $this->data['documents'][3]['has_scan'] ? '' : 'file' ?>" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="3" data-is_scan="1">
                </label>
            </div>
            <div class="d-flex justify-content-between">
                <div class="btn btn-primary sended-doc" style="background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="3" data-is_scan="0">Оригинал отправлен заказчику</div>
                <div class="btn btn-primary sended-doc" style="width: 198px; background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="3" data-is_scan="1">Скан отправлен заказчику</div>
            </div>

            <div class="mb-1 h-2">
                Загруженные документы
            </div>

            <?php foreach ($this->data['documents'][3]['docs'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href="<?= $value['exist_file'] ? ($value['save_directory'] . '/' . $value['dogovor_id'] . '/' . $value['actual_version']) : ('/pdf' . '/' . $value['actual_version'])?>"><?= $value['actual_version'] . " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>

            <div class="mb-1">
                Отправленные документы
            </div>

            <?php foreach ($this->data['documents'][3]['send'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href=""><?= " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-send-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex flex-column gap-3" id="first_page" style="min-width: 400px; max-width: 400px">
            <ul class="custom-nav">
                <button type="button" class="custom-nav-link nav-link change-need <?= $this->data['order']['need_additional'] ? 'nav-fill-active' : 'nav-outline-active' ?>" data-type_id="1" data-need_doc="<?= $this->data['order']['need_additional'] ? 1 : 0 ?>">Доп. соглашения</button>
            </ul>
            <div class="d-flex gap-3 justify-content-center align-items-center calc-count <?= $this->data['order']['need_additional'] ? '' : 'visually-hidden' ?>">
                <button class="change-count-additional btn" type="button" data-count_plus="-1">
                    <svg width="25px" height="25px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M1 10L1 6L15 6V10L1 10Z" fill="#000000"></path> </g></svg>
                </button>
                <input type="number" step="1" class="d-flex justify-content-center align-items-center additional-count" style="width: 50px; height: 50px" value="<?= $this->data['order']['additional_count'] ?? 1 ?>">
                <button class="change-count-additional btn" type="button" data-count_plus="1">
                    <svg width="25px" height="25px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10 1H6V6L1 6V10H6V15H10V10H15V6L10 6V1Z" fill="#000000"></path> </g></svg>
                </button>
            </div>
            <div class="d-flex justify-content-between block-buttons <?= $this->data['order']['need_additional'] ? '' : 'visually-hidden' ?>">
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="Загрузить оригинал">
                    <div class="btn btn-primary">Загрузить оригинал</div>
                    <input class="d-none upload-document" type="file" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="1" data-is_scan="0">
                </label>
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="Загрузить скан">
                    <div class="btn btn-primary" style="width: 198px">Загрузить скан</div>
                    <input class="d-none upload-document" type="file" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="1" data-is_scan="1">
                </label>
            </div>
            <div class="d-flex justify-content-between block-buttons <?= $this->data['order']['need_additional'] ? '' : 'visually-hidden' ?>">
                <div class="btn btn-primary sended-doc" style="background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="1" data-is_scan="0">Оригинал отправлен заказчику</div>
                <div class="btn btn-primary sended-doc" style="width: 198px; background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="1" data-is_scan="1">Скан отправлен заказчику</div>
            </div>

            <div class="mb-1 h-2">
                Загруженные документы
            </div>

            <?php foreach ($this->data['documents'][1]['docs'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href="<?= $value['save_directory'] . '/' . $value['dogovor_id'] . '/' . $value['actual_version']?>"><?= $value['actual_version'] . " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>

            <div class="mb-1">
                Отправленные документы
            </div>

            <?php foreach ($this->data['documents'][1]['send'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href=""><?= " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-send-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex flex-column gap-3" id="second_page" style="min-width: 400px; max-width: 400px">
            <ul class="custom-nav">
                <button type="button" class="custom-nav-link nav-link change-need <?= $this->data['order']['need_protocol'] ? 'nav-fill-active' : 'nav-outline-active' ?>" data-type_id="2" data-need_doc="<?= $this->data['order']['need_protocol'] ? 1 : 0 ?>">Протокол разногласий</button>
            </ul>
            <div class="d-flex gap-3 justify-content-center align-items-center calc-count <?= $this->data['order']['need_protocol'] ? '' : 'visually-hidden' ?>">
                <input type="number" step="1" class="d-flex justify-content-center align-items-center" style="width: 50px; height: 50px" value="1" readonly>
            </div>
            <div class="d-flex justify-content-between block-buttons <?= $this->data['order']['need_protocol'] ? '' : 'visually-hidden' ?>">
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="Загрузить оригинал">
                    <div class="btn btn-primary">Загрузить оригинал</div>
                    <input class="d-none upload-document" type="file" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="2" data-is_scan="0">
                </label>
                <label class="upload-document cursor-pointer nav-link link-card"
                       title="Загрузить скан">
                    <div class="btn btn-primary" style="width: 198px">Загрузить скан</div>
                    <input class="d-none upload-document" type="file" name="upload-document"
                           data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="2" data-is_scan="1">
                </label>
            </div>
            <div class="d-flex justify-content-between block-buttons <?= $this->data['order']['need_protocol'] ? '' : 'visually-hidden' ?>">
                <div class="btn btn-primary sended-doc" style="background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="2" data-is_scan="0">Оригинал отправлен заказчику</div>
                <div class="btn btn-primary sended-doc" style="width: 198px; background-color: #0d6efd" data-dogovor_id="<?= $this->data['order']['id'] ?>" data-type_id="2" data-is_scan="1">Скан отправлен заказчику</div>
            </div>

            <div class="mb-1">
                Загруженные документы
            </div>

            <?php foreach ($this->data['documents'][2]['docs'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href="<?= $value['save_directory'] . '/' . $value['dogovor_id'] . '/' . $value['actual_version']?>"><?= $value['actual_version'] . " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>

            <div class="mb-1">
                Отправленные документы
            </div>

            <?php foreach ($this->data['documents'][2]['send'] as $key => $value): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a style="overflow: hidden" href=""><?= " ({$value['date']}) - " . $value['type_format'] ?></a>
                    <button type="button" class="btn px-0 delete-send-document" data-document_id="<?= $value['document_id'] ?>">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#0d6efd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.64"></g><g id="SVGRepo_iconCarrier"> <path d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H11M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M15 16L17.5 18.5M20 21L17.5 18.5M17.5 18.5L20 16M17.5 18.5L15 21" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</form>
