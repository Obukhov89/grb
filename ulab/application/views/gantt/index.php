<?php include "helpers.php"; ?>
    <style>
        .table-container {
            overflow-x: auto;
        }

        .filter-container {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>

    <div class="container">
        <div class="container _table-responsive-xxl" style="overflow: auto;">
            <button data-js-create-user class="btn btn-primary mb-3" style="position: relative; display: block;">
                Добавить
                пользователя
            </button>

            <button data-js-show-product-calendar class="btn btn-secondary mb-3" style="position: relative; display: block;">Производственный календарь</button>
            <!--        <button data-js-create-project class="btn btn-primary mb-3" style="position: relative; display: block;">Добавить проект</button>-->
            <!--        <b onclick="openSecondView();" class="btn btn-secondary mb-3" style="position: relative; display: block;">Пользователи</b>-->

            <div style="text-align: left; margin-bottom: 15px;">
                <input onclick="openSecondView(1);" type="radio" class="btn-check" name="options" id="option1" autocomplete="off" <?php echo $this->data['VIEW_MODE'] == 2 ? "" : "checked";?>/>
                <label class="btn btn-secondary" for="option1">Пользователи</label>

                <input onclick="openSecondView(2);" type="radio" <?php echo $this->data['VIEW_MODE'] == 2 ? "checked" : ""; ?>
                       class="btn-check" name="options" id="option2" autocomplete="off"/>
                <label class="btn btn-secondary" for="option2">Проекты</label>
            </div>
            <!--todo: тут прикрепить логику-->
           <div style="display: none;">
               <div class="alert alert-danger" role="alert">
                   This is a danger alert—check it out!
               </div>
               <div class="alert alert-success" role="alert">
                   This is a danger alert—check it out!
               </div>
           </div>

            <?php include "filter.php"; ?>
            <?php

            if ($this->data['VIEW_MODE'] == 2) {
                include "table_view_2.php";
            } else {
                include "table_view_1.php";
            }
            ?>
        </div>
    </div>


<?php include "gantt_modals.php" ?>

<?php include "gantt_script.php"; ?>