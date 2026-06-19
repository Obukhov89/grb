<div class="panel-body">
	<div class="confirm">
		<div class="row mb-2">
			<div class="col-auto">
				<table class="table table-confirm mb-0">
					<tbody>
					<?php foreach ($this->data['assigned'] as $key => $value): ?>
						<?php
						if ((array_search($value['user_id'], $this->data['laboratory_head']) === false)) {
							continue;
						}
						?>
						<tr>
							<th scope="row" class="text-success">
								<?= !empty($this->data['check_tz'][$value['user_id']]['confirm']) ? '&#128504;' : ''?>
							</th>
							<td><?= $value["short_name"] ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-auto footer-confirm">
				<?php if ($this->data['is_confirm']): ?>
					<strong>Техническое задание утверждено!</strong>
				<?php else: ?>
					<?php if (empty($this->data['lab_leaders_tz'])): ?>
						<button type="button" class="btn btn-primary btn-transfer" <?= empty($this->data['requirement']['methods']) && !empty($this->data['check_tz']) || !in_array($_SESSION['SESS_AUTH']['USER_ID'], $this->data['assigned_id']) ? 'disabled' : '' ?>>Передать</button>
					<?php else: ?>
						<?php if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], $this->data['lab_leaders_tz'])): ?>
							<strong>Заявка передана на рассмотрение!</strong>
						<?php else: ?>
							<?php if (!empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['confirm'])): ?>
								<strong>Утверждено Вами!</strong>
							<?php else: ?>
								<button type="button" class="btn btn-primary btn-approve me-2">Утвердить</button>
								<button type="button" class="btn btn-danger btn-no-transfer"
									<?= !empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['date_return']) ||
									!empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['confirm']) ? 'disabled' : '' ?>>
									Вернуть
								</button>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

