<div class="table-container mb-4">
    <table class="table table-bordered table-hover" id="gantt_table">
        <thead>
        <tr>
            <th scope="col" class="text-center" style="width: 500px;"><span></span></th>
            <?php foreach ($this->data['calendar'] as $month) { ?>
                <th scope="col" colspan="<?php echo count($month['days']) ?>"
                    class="text-center"><?php echo $month['local_name'] . " " . $month['year'] ?></th>
            <?php } ?>
        </tr>
        <tr>
            <th class=""></th>
            <!-- Заголовки для дней в месяце -->
            <?php foreach ($this->data['calendar'] as $month) {
                foreach ($month['days'] as $day) { ?>
                    <td id="sep_<?= $day['day'] ?>" <?php if ($this->data['current_date']['month']['name'] . "_" . $this->data['current_date']['day'] == $month['local_name'] . "_" . $day['day']) {
                        echo 'style="background: #ffa50087;"';
                    } else if ($day['is_weekend']) {
                        echo 'style="background: #00b8ce;"';
                    } ?>
                    >
                        <span><?= $day['day'] ?></span>
                    </td>
                <?php }
            }
            ?>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($this->data['table']['projects'] as $project) { ?>
            <tr>
                <td style="display: none;"></td>
                <td rowspan="1" class="fixed-column"
                    style="background: white;"
                    onclick="editProject(<?= $project['id'] ?>);"><?= $project['project_name'] ?></td>
                <?php echo renderCells($this->data['calendar'], $this->data['current_date']); ?>
            </tr>

            <?php foreach ($project['members'] as $user) {
                $timeLinesCount = $user->getTimeLinesCountByProjectId();
                ?>
                <tr>
                    <td onclick="editUser(<?= $user->getId(); ?>, <?= $user->getProjectId(); ?>)"
                        class="fixed-column"
                        rowspan="<?= $timeLinesCount ?>"
                        style="background: lightgray; text-align: right; vertical-align: middle;">
                        <span><?= $user->getName(); ?></span></td>
                    <?php
                    $timeLines = $user->getTimeLinesByProjectId();

                    for ($i = 0; $i < $timeLinesCount; $i++) {
                        echo renderCells($this->data['calendar'],
                            $this->data['current_date'],
                            $timeLines[$i],
                            $user->getId(),
                            $project['id'],
                            $project['color1'],
                            $project['color2'],
                            $i,
                        );
                    }
                    ?>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>