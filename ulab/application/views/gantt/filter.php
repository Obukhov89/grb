<form action="/ulab/gantt/index" method="GET">
    <div class="filter-container mb-3">
        <div class="row">
            <div class="col-md-3 mb-3" style="text-align: left">
                <input type="hidden" name="VIEW_MODE" value="<?= $this->data['VIEW_MODE']; ?>"/>
                <label class="form-label" for="user_filter">Пользователь</label>
                <input value="<?= $this->data['filter']['user_filter']; ?>"
                       name="user_filter" placeholder="Пользователь" aria-label="Пользователь"
                       id="user_filter"
                       type="text" class="form-control"/>
            </div>

            <div class="col-md-3 mb-3" style="text-align: left">
                <label class="form-label" for="project_filter">Проект</label>
                <input value="<?= $this->data['filter']['project_filter']; ?>"
                       name="project_filter" placeholder="Проект" type="text"
                       id="project_filter"
                       class="form-control" aria-label="Проект"/>
            </div>

            <div class="col-md-3 mb-3" style="text-align: left">
                <label class="form-label" for="filter_dates">Диапазон календаря</label>
                <input value="<?= $this->data['filter']['filter_dates'] ?>"
                       id="filter_dates"
                       name="filter_dates" placeholder="Диапазон календаря" type="text"
                       class="form-control" aria-label="Диапазон календаря"/>
            </div>

        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <button type="submit" name="action" value="search" class="btn btn-primary w-100 mb-2">Искать</button>
                <button onclick="resetFilter();" name="action" value="reset" class="btn btn-secondary w-100" type="button">Сбросить</button>
            </div>
        </div>
    </div>
</form>

<script>
    function resetFilter(){
        window.history.replaceState({}, '', window.location.pathname + '?VIEW_MODE=1&action=reset');
        window.location.reload();
    }

    document.addEventListener('DOMContentLoaded', function () {
        //todo: ограничить года
        $('input[name="filter_dates"]').daterangepicker({
            minYear: 1980,
            maxYear: 2030,
            locale: {
                format: 'DD.MM.YYYY'
            },
        });
    });
</script>