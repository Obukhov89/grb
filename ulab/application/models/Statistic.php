<?php

/**
 * Модель для работы с ГОСТами
 * Class Statistic
 */
class Statistic extends Model
{
    // сущности
    public array $entities = [
        // заявка
        'request' => [
            'title' => 'Заявка',
            'table' => 'ba_tz',
            'columns' => [
                'type_tz' => [
                    'title' => 'Тип заявки',
                    'select' => 'ba_tz.TYPE_ID as ba_tz_type_id, ba_tz_type.name as type_tz',
                    'order' => 'ba_tz.TYPE_ID',
                    'filter' => "ba_tz_type.name like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'ba_tz.TYPE_ID',
                    'link' => '<a class="chart_link" data-id="{ba_tz_type_id}" data-entity="request" href="#">{type_tz}</a>',
                    'dependency' => [
                        'ba_tz_type' => [
                            'join' => "inner join ba_tz_type on ba_tz_type.type_id = ba_tz.TYPE_ID"
                        ]
                    ]
                ],
                'count_total' => [
                    'title' => 'Всего заявок',
                    'select' => 'count(ba_tz.ID) as count_total',
                    'order' => 'count(ba_tz.ID)',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'count_won' => [
                    'title' => 'Кол-во успешных',
                    'select' => "sum(case when ba_tz.STAGE_ID = 'WON' then 1 else 0 end) as count_won",
                    'order' => "sum(case when ba_tz.STAGE_ID = 'WON' then 1 else 0 end)",
                    'default_order' => 'desc',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'count_lose' => [
                    'title' => 'Кол-во провальных',
                    'select' => "sum(case when ba_tz.STAGE_ID IN ('5', '6', '7', '8', '9', 'LOSE') then 1 else 0 end) as count_lose",
                    'order' => "sum(case when ba_tz.STAGE_ID IN ('5', '6', '7', '8', '9', 'LOSE') then 1 else 0 end)",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'sum_price' => [
                    'title' => 'Суммарная стоимость заявок',
                    'select' => "sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.price_discount else 0 end) as sum_price",
                    'order' => "sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.price_discount else 0 end)",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'sum_price_oplata' => [
                    'title' => 'Суммарная оплата заявок',
                    'select' => "sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.OPLATA else 0 end) as sum_price_oplata",
                    'order' => "sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.OPLATA else 0 end)",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
            ],
            'date_filter' => "ba_tz.DATE_CREATE_TIMESTAMP between '{dateStart}' AND '{dateEnd}'",
            'order' => 'ba_tz.id',
            'chart' => [
                'donut' => [
                    [
                        'value' => 'count_total',
                        'label' => 'type_tz',
                        'formatted' => 'кол-во заявок {value}'
                    ],
                    [
                        'value' => 'sum_price',
                        'label' => 'type_tz',
                        'formatted' => 'стоимость заявок {value}'
                    ]
                ],
                'bar' => [
                    'formatted' => 'Всего заявок',
                    'formatted_2' => 'Суммарная стоимость заявок',
                    'sql' => "SELECT ba_tz_type.name as label, MONTH(ba_tz.DATE_CREATE_TIMESTAMP) month,
                              count(ba_tz.ID) as value,
                              sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.price_discount else 0 end) value_2
                            FROM ba_tz
                            inner join ba_tz_type on ba_tz_type.type_id = ba_tz.TYPE_ID
                            where ba_tz.TYPE_ID = '{id}' AND YEAR(ba_tz.DATE_CREATE_TIMESTAMP) = YEAR(CURDATE())
                            group by MONTH(ba_tz.DATE_CREATE_TIMESTAMP)",
                    'days_sql' => "SELECT ba_tz_type.name as label, DAY(ba_tz.DATE_CREATE_TIMESTAMP) day, DATE(ba_tz.DATE_CREATE_TIMESTAMP) date,
                              count(ba_tz.ID) as value,
                              sum(case when ba_tz.STAGE_ID not IN ('5', '6', '7', '8', '9', 'LOSE') then ba_tz.price_discount else 0 end) value_2
                            FROM ba_tz
                            inner join ba_tz_type on ba_tz_type.type_id = ba_tz.TYPE_ID
                            where ba_tz.TYPE_ID = '{id}' AND MONTH(ba_tz.DATE_CREATE_TIMESTAMP) = '{month}' AND YEAR(ba_tz.DATE_CREATE_TIMESTAMP) = '{year}'
                            group by DAY(ba_tz.DATE_CREATE_TIMESTAMP)"
                ]
            ],
        ],
        // оборудование
        'oborud' => [
            'title' => 'Оборудование',
            'table' => 'ba_oborud',
            'columns' => [
                'oborud' => [
                    'title' => 'Оборудование',
                    'select' => "ba_oborud.OBJECT as oborud",
                    'order' => "ba_oborud.OBJECT",
                    'filter' => "ba_oborud.OBJECT like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'reg_num' => [
                    'title' => 'Инвентарный номер',
                    'select' => "ba_oborud.REG_NUM as reg_num",
                    'order' => "ba_oborud.REG_NUM",
                    'filter' => "ba_oborud.REG_NUM like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'in_stock' => [
                    'title' => 'В наличии',
                    'select' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет') AS in_stock",
                    'order' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет')",
                    'filter' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'decommission' => [
                    'title' => 'Списано',
                    'select' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет') AS decommission",
                    'order' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет')",
                    'filter' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'long_storage' => [
                    'title' => 'На длительном хранении',
                    'select' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет') AS long_storage",
                    'order' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет')",
                    'filter' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'checked' => [
                    'title' => 'Проверено, отмечено в оборудовании',
                    'select' => "IF(ba_oborud.CHECKED = 1, 'Да', 'Нет') AS checked",
                    'order' => "IF(ba_oborud.CHECKED = 1, 'Да', 'Нет')",
                    'filter' => "IF(ba_oborud.CHECKED = 1, 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'verified' => [
                    'title' => 'Проверено согласно статусу журнала',
                    'select' => "IF(CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет') AS verified",
                    'order' => "IF(CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет')",
                    'filter' => "IF(CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'metr_control' => [
                    'title' => 'Подлежит поверке',
                    'select' => "IF(ba_oborud.NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL, 'Да', 'Нет') as metr_control",
                    'order' => "IF(ba_oborud.NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL, 'Да', 'Нет')",
                    'filter' => "IF(ba_oborud.NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL, 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'in_oa' => [
                    'title' => 'В ОА',
                    'select' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет') as in_oa",
                    'order' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет')",
                    'filter' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'poverka_alarm' => [
                    'title' => 'С истёкшим сроком поверки',
                    'select' => "IF(DATEDIFF(POVERKA, CURDATE()) <= 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет') as poverka_alarm",
                    'order' => "IF(DATEDIFF(POVERKA, CURDATE()) <= 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет')",
                    'filter' => "IF(DATEDIFF(POVERKA, CURDATE()) <= 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL), 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
            ],
            'order' => 'ba_oborud.OBJECT',
        ],
        'oborud_total' => [
            'title' => 'Итого по оборудованию',
            'table' => 'ba_oborud',
            'columns' => [
                // Всё оборудование
                'count_total' => [
                    'title' => 'Кол-во оборудования',
                    'select' => 'count(DISTINCT ba_oborud.ID) as count_total',
                    'order' => 'count(DISTINCT ba_oborud.ID)',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "В наличии" - отмечен checkbox
                'in_stock' => [
                    'title' => 'В наличии',
                    'select' => 'COUNT(DISTINCT(case when ba_oborud.IN_STOCK = 1 then ba_oborud.ID end)) as in_stock',
                    'order' => 'count(DISTINCT ba_oborud.ID)',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_in_stock' => [
                    'title' => 'Нет в наличии',
                    'select' => 'COUNT(DISTINCT(case when ba_oborud.IN_STOCK <> 1 then ba_oborud.ID end)) as not_in_stock',
                    'order' => 'count(DISTINCT ba_oborud.ID)',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'metr_control' => [
                    'title' => 'Подлежит поверке',
                    'select' => 'COUNT(DISTINCT(case when ba_oborud.NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL then ba_oborud.ID end)) as metr_control',
                    'order' => 'COUNT(DISTINCT(case when ba_oborud.NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL then ba_oborud.ID end))',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "Не подлежит поверке" - отмечен checkbox
                'no_metr_control' => [
                    'title' => 'Не подлежит поверке',
                    'select' => 'COUNT(DISTINCT(case when ba_oborud.NO_METR_CONTROL = 1 then ba_oborud.ID end)) as no_metr_control',
                    'order' => 'count(DISTINCT ba_oborud.ID)',
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'count_decommission' => [
                    'title' => 'Списано',
                    'select' => "COUNT(DISTINCT(case when (ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP) then ba_oborud.ID end)) as count_decommission",
                    'order' => "COUNT(DISTINCT(case when (ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_count_decommission' => [
                    'title' => 'Не списано',
                    'select' => "COUNT(DISTINCT(case when (ba_oborud.SPISANIE = '' OR ba_oborud.SPISANIE IS NULL) AND (ba_oborud.DATE_SP = '' OR ba_oborud.DATE_SP IS NULL) then ba_oborud.ID end)) as not_count_decommission",
                    'order' => "COUNT(DISTINCT(case when (ba_oborud.SPISANIE = '' OR ba_oborud.SPISANIE IS NULL) AND (ba_oborud.DATE_SP = '' OR ba_oborud.DATE_SP IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'in_oa' => [
                    'title' => 'В ОА',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IN_AREA = 1 then ba_oborud.ID end)) as in_oa",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IN_AREA = 1 then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_oa' => [
                    'title' => 'Не в области акредитации',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IN_AREA <> 1 then ba_oborud.ID end)) as not_oa",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IN_AREA <> 1 then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "На длительном хранении" - отмечен checkbox
                'count_long_storage' => [
                    'title' => 'На длительном хранении',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL then ba_oborud.ID end)) as count_long_storage",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_count_long_storage' => [
                    'title' => 'Без длительного хранения',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL then ba_oborud.ID end)) as not_count_long_storage",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // ("До даты окончания повеки больше 60 дней" или "Не подлежит поверке") и "Оборудование проверено" и "В наличии" и ("Не на длительном хранении" и "Нет, не заполнено основание для списания")
                'norm' => [
                    'title' => 'Без замечаний',
                    'select' => "COUNT(DISTINCT(case when (DATEDIFF(POVERKA, CURDATE()) > 60 OR NO_METR_CONTROL = 1) AND CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end)) as norm",
                    'order' => "COUNT(DISTINCT(case when (DATEDIFF(POVERKA, CURDATE()) > 60 OR NO_METR_CONTROL = 1) AND CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // ("До даты окончания повеки меньше 60 и больше 0 дней") и "Подлежит поверки" и "Оборудование проверено" и "В наличии" и ("Не на длительном хранении" и "Нет, не заполнено основание для списания")
                'less_60' => [
                    'title' => 'До истечения срока поверки осталось менее 60 дней',
                    'select' => "COUNT(DISTINCT(case when DATEDIFF(POVERKA, CURDATE()) <= 60 AND DATEDIFF(POVERKA, CURDATE()) > 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND CHECKED = 1 AND IN_STOCK = 1 AND (LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end)) as less_60",
                    'order' => "COUNT(DISTINCT(case when DATEDIFF(POVERKA, CURDATE()) <= 60 AND DATEDIFF(POVERKA, CURDATE()) > 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND CHECKED = 1 AND IN_STOCK = 1 AND (LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "Дата окончания поверки равна или меньше текущей даты" и "Подлежит поверки" и "В наличии" и ("Не на длительном хранении" и "Нет, не заполнено основание для списания")
                'poverka_alarm' => [
                    'title' => 'С истёкшим сроком поверки',
                    'select' => "COUNT(DISTINCT(case when DATEDIFF(POVERKA, CURDATE()) <= 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end)) as poverka_alarm",
                    'order' => "COUNT(DISTINCT(case when DATEDIFF(POVERKA, CURDATE()) <= 0 AND (NO_METR_CONTROL <> 1 OR ba_oborud.NO_METR_CONTROL IS NULL) AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "Проверено" - отмечен checkbox
                'count_checked' => [
                    'title' => 'Проверено, отмечено в оборудовании',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.CHECKED = 1 then ba_oborud.ID end)) as count_checked",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.CHECKED = 1 then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_count_checked' => [
                    'title' => 'Не проверено, не отмечено в оборудовании',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.CHECKED <> 1 then ba_oborud.ID end)) as not_count_checked",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.CHECKED <> 1 then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'verified' => [
                    'title' => 'Проверено согласно статусу журнала',
                    'select' => "COUNT(DISTINCT(case when CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end)) as verified",
                    'order' => "COUNT(DISTINCT(case when CHECKED = 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                // "Оборудование НЕ проверено" и "В наличии" и ("Не на длительном хранении" и "Нет, не заполнено основание для списания")
                'not_verified' => [
                    'title' => 'Не проверено согласно статусу журнала',
                    'select' => "COUNT(DISTINCT(case when CHECKED <> 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end)) as not_verified",
                    'order' => "COUNT(DISTINCT(case when CHECKED <> 1 AND IN_STOCK = 1 AND (ba_oborud.LONG_STORAGE = 0 OR ba_oborud.LONG_STORAGE IS NULL) AND (SPISANIE = '' OR SPISANIE IS NULL) then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'si' => [
                    'title' => 'СИ',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'SI' then ba_oborud.ID end)) as si",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'SI' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'io' => [
                    'title' => 'ИО',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'IO' then ba_oborud.ID end)) as io",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'IO' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'vo' => [
                    'title' => 'ВО',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'VO' then ba_oborud.ID end)) as vo",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'VO' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'ts' => [
                    'title' => 'ТС',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'TS' then ba_oborud.ID end)) as ts",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'TS' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'so' => [
                    'title' => 'CO',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'SO' then ba_oborud.ID end)) as so",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'SO' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'react' => [
                    'title' => 'Реактивы',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'REACT' then ba_oborud.ID end)) as react",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'REACT' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'oopp' => [
                    'title' => 'Оборудование для отбора/подготовки проб',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'OOPP' then ba_oborud.ID end)) as oopp",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT = 'OOPP' then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'not_ident' => [
                    'title' => 'Без идентификации оборудования',
                    'select' => "COUNT(DISTINCT(case when ba_oborud.IDENT NOT IN ('SI', 'IO', 'VO', 'TS', 'SO', 'REACT', 'OOPP') OR ba_oborud.IDENT IS NULL then ba_oborud.ID end)) as not_ident",
                    'order' => "COUNT(DISTINCT(case when ba_oborud.IDENT NOT IN ('SI', 'IO', 'VO', 'TS', 'SO', 'REACT', 'OOPP') OR ba_oborud.IDENT IS NULL then ba_oborud.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
            ],
            'order' => 'ba_oborud.OBJECT',
        ],
        'oborud_use' => [
            'title' => 'Информация по применению оборудования',
            'table' => 'ba_oborud',
            'columns' => [
                'oborud' => [
                    'title' => 'Оборудование',
                    'select' => "ba_oborud.ID oborud_id, ba_oborud.OBJECT as oborud",
                    'order' => "ba_oborud.OBJECT",
                    'filter' => "ba_oborud.OBJECT like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'ba_oborud.ID',
                    'link' => '<a class="chart_link" data-id="{oborud_id}" data-entity="oborud_use" href="#">{oborud}</a>',
                ],
                'reg_num' => [
                    'title' => 'Инвентарный номер',
                    'select' => "ba_oborud.REG_NUM as reg_num",
                    'order' => "ba_oborud.REG_NUM",
                    'filter' => "ba_oborud.REG_NUM like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'uses' => [
                    'title' => 'Использований',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as uses",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end)",
                    'default_order' => 'desc',
                    'filter' => false,
                    'where' => "ulab_methods_oborud.id_oborud <> 0 AND ulab_methods_oborud.method_id <> 0",
                    'group' => false,
                ],
                'oborud_ident' => [
                    'title' => 'Идентификация',
                    'select' => "ba_oborud_ident.name as oborud_ident",
                    'order' => "ba_oborud_ident.name",
                    'filter' => "ba_oborud_ident.name like '%{dataFilter}%'",
                    'where' => "ba_oborud.IDENT is not null",
                    'group' => false,
                ],
                'in_oa' => [
                    'title' => 'В ОА',
                    'select' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет') as in_oa",
                    'order' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет')",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ]
            ],
            'dependency' => [
                'ba_oborud_ident' => [
                    'join' => "inner join ba_oborud_ident on ba_oborud_ident.ident_id = ba_oborud.IDENT"
                ],
                'ulab_methods_oborud' => [
                    'join' => "inner join ulab_methods_oborud on ulab_methods_oborud.id_oborud = ba_oborud.ID"
                ],
                'ulab_gost_to_probe' => [
                    'join' => "inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods_oborud.method_id"
                ],
                'ulab_start_trials' => [
                    'join' => "inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id"
                ],
            ],
            'date_filter' => "ulab_start_trials.date between '{dateStart}' AND '{dateEnd}'",
            'order' => 'ba_oborud.OBJECT',
            'chart' => [
                'donut' => [],
                'bar' => [
                    'formatted' => 'Кол-во использований',
                    'sql' => "SELECT
                                    CONCAT(ba_oborud.OBJECT, ' ',  ba_oborud.REG_NUM) as label,
                                    sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value,
                                    MONTH(ulab_start_trials.date) month
                                FROM ba_oborud
                                    inner join ulab_methods_oborud on ulab_methods_oborud.id_oborud = ba_oborud.ID
                                    inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods_oborud.method_id
                                    inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                                WHERE ba_oborud.ID = '{id}' AND YEAR(ulab_start_trials.date) = YEAR(CURDATE())
                                AND ulab_methods_oborud.id_oborud <> 0 AND ulab_methods_oborud.method_id <> 0
                                group by MONTH(ulab_start_trials.date)",
                    'days_sql' => "SELECT
                                    CONCAT(ba_oborud.OBJECT, ' ',  ba_oborud.REG_NUM) as label,
                                    sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value,
                                    DAY(ulab_start_trials.date) day, DATE(ulab_start_trials.date) date
                                FROM ba_oborud
                                    inner join ulab_methods_oborud on ulab_methods_oborud.id_oborud = ba_oborud.ID
                                    inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods_oborud.method_id
                                    inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                                WHERE ba_oborud.ID = '{id}' AND YEAR(ulab_start_trials.date) = '{year}' AND MONTH(ulab_start_trials.date) = '{month}'
                                AND ulab_methods_oborud.id_oborud <> 0 AND ulab_methods_oborud.method_id <> 0
                                group by DAY(ulab_start_trials.date)"
                ]
            ],
        ],
//        'oborud_unused' => [
//            'title' => 'Не используется оборудование за текущий год',
//            'table' => 'ba_oborud',
//            'columns' => [
//                'oborud' => [
//                    'title' => 'Оборудование',
//                    'select' => "ba_oborud.ID oborud_id, ba_oborud.OBJECT as oborud",
//                    'order' => "ba_oborud.OBJECT",
//                    'filter' => "ba_oborud.OBJECT like '%{dataFilter}%'",
//                    'where' => "ba_oborud.ID not in (select ba_oborud.ID
//                                   FROM ba_oborud
//                                inner join ba_oborud_ident on ba_oborud_ident.ident_id = ba_oborud.IDENT
//                                inner join ulab_methods_oborud on ulab_methods_oborud.id_oborud = ba_oborud.ID
//                                inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods_oborud.method_id
//                                inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
//                                   where YEAR(ulab_start_trials.date) = YEAR(CURDATE())
//                                   AND ulab_methods_oborud.id_oborud <> 0 AND ulab_methods_oborud.method_id <> 0 AND ba_oborud.IDENT is not null
//                                group by ba_oborud.ID)",
//                    'group' => 'ba_oborud.ID',
//                    'link' => '<span data-id="{oborud_id}">{oborud}</span>',
//                ],
//                'reg_num' => [
//                    'title' => 'Инвентарный номер',
//                    'select' => "ba_oborud.REG_NUM as reg_num",
//                    'order' => "ba_oborud.REG_NUM",
//                    'filter' => "ba_oborud.REG_NUM like '%{dataFilter}%'",
//                    'where' => false,
//                    'group' => false,
//                ],
//                'oborud_ident' => [
//                    'title' => 'Идентификация',
//                    'select' => "ba_oborud_ident.name as oborud_ident",
//                    'order' => "ba_oborud_ident.name",
//                    'filter' => "ba_oborud_ident.name like '%{dataFilter}%'",
//                    'where' => "ba_oborud.IDENT is not null",
//                    'group' => false,
//                ],
//                'in_stock' => [
//                    'title' => 'В наличии',
//                    'select' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет') AS in_stock",
//                    'order' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет')",
//                    'filter' => "IF(ba_oborud.IN_STOCK = 1, 'Да', 'Нет') like '%{dataFilter}%'",
//                    'where' => false,
//                    'group' => false,
//                ],
//                'decommission' => [
//                    'title' => 'Списано',
//                    'select' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет') AS decommission",
//                    'order' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет')",
//                    'filter' => "IF((ba_oborud.SPISANIE <> '' AND ba_oborud.SPISANIE IS NOT NULL) OR (ba_oborud.DATE_SP <> '' AND ba_oborud.DATE_SP IS NOT NULL AND CURDATE() >= ba_oborud.DATE_SP), 'Да', 'Нет') like '%{dataFilter}%'",
//                    'where' => false,
//                    'group' => false,
//                ],
//                'long_storage' => [
//                    'title' => 'На длительном хранении',
//                    'select' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет') AS long_storage",
//                    'order' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет')",
//                    'filter' => "IF(ba_oborud.LONG_STORAGE <> 0 AND ba_oborud.LONG_STORAGE IS NOT NULL, 'Да', 'Нет') like '%{dataFilter}%'",
//                    'where' => false,
//                    'group' => false,
//                ],
//                'in_oa' => [
//                    'title' => 'В ОА',
//                    'select' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет') as in_oa",
//                    'order' => "IF(ba_oborud.IN_AREA = 1, 'Да', 'Нет')",
//                    'filter' => false,
//                    'where' => false,
//                    'group' => false,
//                ]
//            ],
//            'dependency' => [
//                'ba_oborud_ident' => [
//                    'join' => "inner join ba_oborud_ident on ba_oborud_ident.ident_id = ba_oborud.IDENT"
//                ],
//            ],
//            'order' => 'ba_oborud.OBJECT',
//        ],
        // методики/испытания
        'methods' => [
            'title' => 'Методики/Испытания',
            'table' => 'ulab_methods',
            'columns' => [
                'gost' => [
                    'title' => 'ГОСТ, пункт',
                    'select' => "CONCAT(ulab_gost.reg_doc, ' ',  ulab_methods.clause) as gost",
                    'order' => "CONCAT(ulab_gost.reg_doc, ' ',  ulab_methods.clause)",
                    'filter' => "CONCAT(ulab_gost.reg_doc, ' ',  ulab_methods.clause) like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
                'method' => [
                    'title' => 'Методика',
                    'select' => "ulab_methods.id as method_id, ulab_methods.name as method",
                    'order' => "ulab_methods.name",
                    'filter' => "ulab_methods.name like '%{dataFilter}%'",
                    'link' => '<a class="chart_link" data-id="{method_id}" data-entity="methods" href="#">{method}</a>',
                    'where' => false,
                    'group' => false,
                ],
                'total' => [
                    'title' => 'Всего использовано',
                    'select' => "count(ulab_methods.id) as total",
                    'order' => "count(ulab_methods.id)",
                    'filter' => false,
                    'default_order' => 'desc',
                    'where' => "PROTOCOLS.NUMBER is not null",
                    'group' => "ulab_gost_to_probe.new_method_id",
                ],
                'total_sum_price' => [
                    'title' => 'Всего на сумму',
                    'select' => "sum(ulab_gost_to_probe.price) as total_sum_price",
                    'order' => "sum(ulab_gost_to_probe.price)",
                    'filter' => false,
                    'where' => "PROTOCOLS.NUMBER is not null",
                    'group' => "ulab_gost_to_probe.new_method_id",
                ],
                'laba_short_name' => [
                    'title' => 'Лаборатория',
                    'select' => "GROUP_CONCAT(DISTINCT  ba_laba.short_name) as laba_short_name",
                    'order' => "ba_laba.short_name",
                    'filter' => "ba_laba.short_name like '%{dataFilter}%'",
                    'where' => false,
                    'group' => false,
                ],
            ],
            'dependency' => [
                'ulab_gost' => [
                    'join' => "inner join `ulab_gost` on ulab_gost.id = ulab_methods.gost_id"
                ],
                'ulab_dimension' => [
                    'join' => "left join `ulab_dimension` on ulab_dimension.id = ulab_methods.unit_id"
                ],
                'ulab_measured_properties' => [
                    'join' => "left join `ulab_measured_properties` on ulab_measured_properties.id = ulab_methods.measured_properties_id"
                ],
                'ulab_measurement' => [
                    'join' => "left join `ulab_measurement` on ulab_measurement.id = ulab_methods.measurement_id"
                ],
                'ulab_gost_to_probe' => [
                    'join' => "inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods.id"
                ],
                'ulab_material_to_request' => [
                    'join' => "inner join ulab_material_to_request on ulab_gost_to_probe.material_to_request_id = ulab_material_to_request.id"
                ],
                'PROTOCOLS' => [
                    'join' => "inner join PROTOCOLS on ulab_material_to_request.protocol_id = PROTOCOLS.ID"
                ],
                'ulab_methods_lab' => [
                    'join' => "left join ulab_methods_lab on ulab_methods.id = ulab_methods_lab.method_id"
                ],
                'ba_laba' => [
                    'join' => "left join ba_laba on ba_laba.ID = ulab_methods_lab.lab_id"
                ],
            ],
            'date_filter' => "PROTOCOLS.DATE between '{dateStart}' AND '{dateEnd}'",
            'order' => 'ulab_methods.id',
            'chart' => [
                'donut' => '',
                'bar' => [
                    'formatted' => 'Всего использованно',
                    'formatted_2' => 'Стоимость выполненых методик',
                    'sql' => "SELECT CONCAT(ulab_gost.reg_doc, ' ',  ulab_methods.clause, ' ', ulab_methods.name) as label,
                              MONTH(PROTOCOLS.DATE) month,
                              count(ulab_methods.id) as value,
                              sum(ulab_gost_to_probe.price) as value_2
                            FROM ulab_methods
                            inner join `ulab_gost` on ulab_gost.id = ulab_methods.gost_id
                            inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods.id
                            inner join ulab_material_to_request on ulab_gost_to_probe.material_to_request_id = ulab_material_to_request.id
                            inner join PROTOCOLS on ulab_material_to_request.protocol_id = PROTOCOLS.ID
                            where ulab_methods.id = '{id}' and PROTOCOLS.NUMBER is not null AND YEAR(PROTOCOLS.DATE) = YEAR(CURDATE())
                            group by MONTH(PROTOCOLS.DATE)",
                    'days_sql' => "SELECT CONCAT(ulab_gost.reg_doc, ' ',  ulab_methods.clause, ' ', ulab_methods.name) as label,
                                DAY(PROTOCOLS.DATE) day,
                                DATE(PROTOCOLS.DATE) date,
                              count(ulab_methods.id) as value,
                              sum(ulab_gost_to_probe.price) as value_2
                            FROM ulab_methods
                            inner join `ulab_gost` on ulab_gost.id = ulab_methods.gost_id
                            inner join ulab_gost_to_probe on ulab_gost_to_probe.new_method_id = ulab_methods.id
                            inner join ulab_material_to_request on ulab_gost_to_probe.material_to_request_id = ulab_material_to_request.id
                            inner join PROTOCOLS on ulab_material_to_request.protocol_id = PROTOCOLS.ID
                            where ulab_methods.id = '{id}' and PROTOCOLS.NUMBER is not null AND YEAR(PROTOCOLS.DATE) = '{year}' AND MONTH(PROTOCOLS.DATE) = '{month}'
                            group by DAY(PROTOCOLS.DATE)"

                ]
            ],
        ],
        // сотрудники
        'users' => [
            'title' => 'Сотрудники',
            'table' => 'b_user',
            'columns' => [
                'user' => [
                    'title' => 'Сотрудники',
                    'select' => "b_user.ID user_id, CONCAT(b_user.NAME, ' ',  b_user.LAST_NAME) as user",
                    'order' => "CONCAT(b_user.NAME, ' ',  b_user.LAST_NAME)",
                    'filter' => "CONCAT(b_user.NAME, ' ',  b_user.LAST_NAME) like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_user.ID',
                    'link' => '<a class="chart_link" data-id="{user_id}" data-entity="users" href="#">{user}</a>',
                ],
                'active' => [
                    'title' => 'Действующий',
                    'select' => "IF(b_user.ACTIVE = 'Y', 'Да', 'Нет') AS active",
                    'order' => "IF(b_user.ACTIVE = 'Y', 'Да', 'Нет')",
                    'filter' => "IF(b_user.ACTIVE = 'Y', 'Да', 'Нет') like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_user.ID',
                ],
                'lab' => [
                    'title' => 'Лаборатория',
                    'select' => "ba_laba.short_name as lab",
                    'order' => "ba_laba.short_name",
                    'filter' => "ba_laba.short_name like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_user.ID',
                ],
                'work_position' => [
                    'title' => 'Должность',
                    'select' => "b_user.WORK_POSITION as work_position",
                    'order' => "b_user.WORK_POSITION",
                    'filter' => "b_user.WORK_POSITION like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_user.ID',
                ],
                'count_complete' => [
                    'title' => 'Кол-во выполненных методик',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as count_complete",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end)",
                    'filter' => false,
                    'default_order' => 'desc',
                    'where' => false,
                    'group' => 'b_user.ID',
                ],
                'methods_price' => [
                    'title' => 'Стоимость выполненных методик',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end) as methods_price",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end)",
                    'filter' => false,
                    'where' => false,
                    'group' => 'b_user.ID',
                ],
            ],
            'dependency' => [
                'b_uts_user' => [
                    'join' => "inner join b_uts_user on b_uts_user.VALUE_ID = b_user.ID"
                ],
                'ba_laba' => [
//                    'join' => "inner join ba_laba on ba_laba.id_dep = b_uts_user.UF_DEPARTMENT"
                    'join' => "inner join ba_laba on ba_laba.short_name = b_user.WORK_DEPARTMENT"
                ],
                'ulab_gost_to_probe' => [
                    'join' => "left join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_user.ID" // DEPARTMENT_ID
                ],
                'ulab_start_trials' => [
                    'join' => "left join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id"
                ],
            ],
            'date_filter' => "ulab_start_trials.date between '{dateStart}' AND '{dateEnd}'",
            'order' => 'b_user.ID',
            'chart' => [
                'donut' => [
                    [
                        'value' => 'count_complete',
                        'label' => 'user',
                        'formatted' => 'кол-во методик {value}'
                    ],
                    [
                        'value' => 'methods_price',
                        'label' => 'user',
                        'formatted' => 'стоимость методик {value}'
                    ]
                ],
                'bar' => [
                    'formatted' => 'Кол-во выполненых методик',
                    'formatted_2' => 'Стоимость выполненых методик',
                    'sql' => "SELECT CONCAT(b_user.NAME, ' ',  b_user.LAST_NAME) as label, MONTH(ulab_start_trials.date) month,
                        sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value,
                        sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end) as value_2
                    FROM b_user
                        inner join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_user.ID
                        inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                    where b_user.ID = '{id}' AND YEAR(ulab_start_trials.date) = YEAR(CURDATE())
                    group by MONTH(ulab_start_trials.date)",
                    'days_sql' => "SELECT CONCAT(b_user.NAME, ' ',  b_user.LAST_NAME) as label, DAY(ulab_start_trials.date) day, DATE(ulab_start_trials.date) date,
                        sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value,
                        sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end) as value_2
                    FROM b_user
                        inner join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_user.ID
                        inner join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                    where b_user.ID = '{id}' AND YEAR(ulab_start_trials.date) = '{year}' AND MONTH(ulab_start_trials.date) = '{month}'
                    group by DAY(ulab_start_trials.date)"

                ]
            ],
        ],
        'users_total' => [
            'title' => 'Итого по сотрудникам',
            'table' => 'b_user',
            'columns' => [
                'count_active' => [
                    'title' => 'Кол-во действующих пользователей',
                    'select' => "COUNT(DISTINCT(case when b_user.ACTIVE = 'Y' then b_user.ID end)) as count_active",
                    'order' => "COUNT(DISTINCT(case when b_user.ACTIVE = 'Y' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'count_not_active' => [
                    'title' => 'Кол-во уволенных сотрудников',
                    'select' => "COUNT(DISTINCT(case when b_user.ACTIVE = 'N' then b_user.ID end)) as count_not_active",
                    'order' => "COUNT(DISTINCT(case when b_user.ACTIVE = 'N' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'register' => [
                    'title' => 'Зарегистрированных сотрудников за этот год',
                    'select' => "COUNT(DISTINCT(case when YEAR(b_user.DATE_REGISTER) = YEAR(CURRENT_DATE()) AND b_user.NAME IS NOT NULL AND b_user.LAST_NAME IS NOT NULL AND b_user.NAME <> '' AND b_user.LAST_NAME <> '' AND b_user.NAME <> 'test' AND b_user.LAST_NAME <> 'test' then b_user.ID end)) as register",
                    'order' => "COUNT(DISTINCT(case when YEAR(b_user.DATE_REGISTER) = YEAR(CURRENT_DATE()) AND b_user.NAME IS NOT NULL AND b_user.LAST_NAME IS NOT NULL AND b_user.NAME <> '' AND b_user.LAST_NAME <> '' AND b_user.NAME <> 'test' AND b_user.LAST_NAME <> 'test' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'dsl' => [
                    'title' => 'ДСЛ',
                    'select' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:55;%' then b_user.ID end)) as dsl",
                    'order' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:55;%' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                    'dependency' => [
                        'b_uts_user' => [
                            'join' => "left join b_uts_user on b_uts_user.VALUE_ID = b_user.ID"
                        ],
                    ]
                ],
                'lsm' => [
                    'title' => 'ЛСМ',
                    'select' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:57;%' then b_user.ID end)) as lsm",
                    'order' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:57;%' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                    'dependency' => [
                        'b_uts_user' => [
                            'join' => "left join b_uts_user on b_uts_user.VALUE_ID = b_user.ID"
                        ],
                    ]
                ],
                'lfmi' => [
                    'title' => 'ЛФМИ',
                    'select' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:56;%' then b_user.ID end)) as lfmi",
                    'order' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:56;%' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                    'dependency' => [
                        'b_uts_user' => [
                            'join' => "left join b_uts_user on b_uts_user.VALUE_ID = b_user.ID"
                        ],
                    ]
                ],
                'lfhi' => [
                    'title' => 'ЛФХИ',
                    'select' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:54;%' then b_user.ID end)) as lfhi",
                    'order' => "COUNT(DISTINCT(case when b_uts_user.UF_DEPARTMENT like '%:54;%' then b_user.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                    'dependency' => [
                        'b_uts_user' => [
                            'join' => "left join b_uts_user on b_uts_user.VALUE_ID = b_user.ID"
                        ],
                    ]
                ],
            ],
            'order' => 'b_user.ID',
        ],
        // лаборатории
        'lab' => [
            'title' => 'Лаборатории',
            'table' => 'ba_laba',
            'columns' => [
                'lab' => [
                    'title' => 'Лаборатория',
                    'select' => "ba_laba.id laba_id, ba_laba.short_name as lab",
                    'order' => "ba_laba.short_name",
                    'filter' => "ba_laba.short_name like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'ba_laba.ID',
                    'link' => '<a class="chart_link" data-id="{laba_id}" data-entity="lab" href="#">{lab}</a>',
                ],
                'count_complete' => [
                    'title' => 'Кол-во выполненных методик',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as count_complete",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end)",
                    'filter' => false,
                    'default_order' => 'desc',
                    'where' => "ba_laba.id_dep IS NOT NULL",
                    'group' => 'ba_laba.ID',
                ],
				'count_probe' => [
					'title' => 'Кол-во испытанных проб',
					'select' => "count(distinct (case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.material_to_request_id end)) as count_probe",
					'order' => "count(distinct (case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.material_to_request_id end))",
					'filter' => false,
					'default_order' => 'desc',
					'where' => false,
					'group' => false,
				],
                'methods_price' => [
                    'title' => 'Стоимость выполненных методик',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end) as methods_price",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then ulab_gost_to_probe.price else 0 end)",
                    'filter' => false,
                    'where' => "ba_laba.id_dep IS NOT NULL",
                    'group' => 'ba_laba.ID',
                ],
                'count_protocols' => [
                    'title' => 'Количество протоколов',
                    'select' => "count(distinct ulab_material_to_request.protocol_id) as count_protocols",
                    'order' => "count(distinct ulab_material_to_request.protocol_id)",
                    'filter' => false,
                    'where' => "",
                    'group' => 'ba_laba.ID',
                    'dependency' => [
                        'ulab_material_to_request' => [
                            'join' => "inner join ulab_material_to_request on ulab_material_to_request.id = ulab_gost_to_probe.material_to_request_id and ulab_material_to_request.protocol_id > 0"
                        ],
                    ]
                ],
            ],
            'dependency' => [
                'b_uts_user' => [
                    'join' => "left join b_uts_user on b_uts_user.UF_DEPARTMENT like CONCAT('%:', ba_laba.id_dep, ';%')"
                ],
                'ulab_gost_to_probe' => [
                    'join' => "left join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_uts_user.VALUE_ID"
                ],
                'ulab_start_trials' => [
                    'join' => "left join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id"
                ],
            ],
            'date_filter' => "ulab_start_trials.date between '{dateStart}' AND '{dateEnd}'",
            'order' => 'ba_laba.ID',
            'chart' => [
                'donut' => [
                    [
                        'value' => 'count_complete',
                        'label' => 'lab',
                        'formatted' => 'кол-во методик {value}'
                    ],
                    [
                        'value' => 'methods_price',
                        'label' => 'lab',
                        'formatted' => 'стоимость методик {value}'
                    ]
                ],
                'bar' => [
                    'formatted' => 'Кол-во выполненых методик',
                    'sql' => "select ba_laba.short_name label,
                        MONTH(ulab_start_trials.date) month, sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value
                            from ba_laba
                                left join b_uts_user on b_uts_user.UF_DEPARTMENT like CONCAT('%:', ba_laba.id_dep, ';%')
                                left join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_uts_user.VALUE_ID
                                left join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                            where ba_laba.id = '{id}' AND YEAR(ulab_start_trials.date) = YEAR(CURDATE()) AND ba_laba.id_dep IS NOT NULL
                            group by MONTH(ulab_start_trials.date)",
                    'days_sql' => "select ba_laba.short_name label,
                        DAY(ulab_start_trials.date) day, DATE(ulab_start_trials.date) date, sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as value
                            from ba_laba
                                left join b_uts_user on b_uts_user.UF_DEPARTMENT like CONCAT('%:', ba_laba.id_dep, ';%')
                                left join ulab_gost_to_probe on ulab_gost_to_probe.assigned_id = b_uts_user.VALUE_ID
                                left join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id
                            where ba_laba.id = '{id}' AND YEAR(ulab_start_trials.date) = '{year}' AND MONTH(ulab_start_trials.date) = '{month}' AND ba_laba.id_dep IS NOT NULL
                            group by DAY(ulab_start_trials.date)"

                ]
            ],
        ],
        // клиент
        'company' => [
            'title' => 'Клиент',
            'table' => 'b_crm_company',
            'columns' => [
                'company' => [
                    'title' => 'Клиент',
                    'select' => "b_crm_company.ID company_id, b_crm_company.TITLE as company",
                    'order' => "b_crm_company.TITLE",
                    'filter' => "b_crm_company.TITLE like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_crm_company.ID',
                    'link' => '<a class="chart_link" data-id="{company_id}" data-entity="company" href="#">{company}</a>'
                ],
                'protocol' => [
                    'title' => 'Выдано протоколов',
                    'select' => "COUNT(DISTINCT(case when PROTOCOLS.NUMBER IS NOT NULL AND PROTOCOLS.INVALID <> 1 then PROTOCOLS.ID end)) as protocol",
                    'order' => "COUNT(DISTINCT(case when PROTOCOLS.NUMBER IS NOT NULL AND PROTOCOLS.INVALID <> 1 then PROTOCOLS.ID end))",
                    'filter' => false,
                    'where' => false,
                    'group' => false,
                ],
                'count_complete' => [
                    'title' => 'Завершенных испытаний по методикам',
                    'select' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end) as count_complete",
                    'order' => "sum(case when ulab_start_trials.state = 'complete' then 1 else 0 end)",
                    'filter' => false,
                    'default_order' => 'desc',
                    'where' => false,
                    'group' => 'b_crm_company.ID',
                ],
            ],
            'dependency' => [
                'b_crm_deal' => [
                    'join' => "inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID"
                ],
                'ulab_material_to_request' => [
                    'join' => "left join ulab_material_to_request ON ulab_material_to_request.deal_id = b_crm_deal.ID"
                ],
                'PROTOCOLS' => [
                    'join' => "left join PROTOCOLS ON PROTOCOLS.ID = ulab_material_to_request.protocol_id"
                ],
                'ulab_gost_to_probe' => [
                    'join' => "left join ulab_gost_to_probe on ulab_gost_to_probe.material_to_request_id = ulab_material_to_request.id"
                ],
                'ulab_start_trials' => [
                    'join' => "left join ulab_start_trials on ulab_start_trials.ugtp_id = ulab_gost_to_probe.id"
                ],
            ],
            'date_filter' => "DATE_FORMAT(b_crm_deal.DATE_CREATE, '%Y-%m-%d') >= '{dateStart}' AND DATE_FORMAT(b_crm_deal.DATE_CREATE, '%Y-%m-%d') <= '{dateEnd}'",
            'order' => 'b_crm_company.ID',
            'chart' => [
                'donut' => [],
                'bar' => [
                    'formatted' => 'Выдано протоколов',
                    'sql' => "select
                                    b_crm_company.TITLE as label,
                                    COUNT(DISTINCT(case when PROTOCOLS.NUMBER IS NOT NULL AND PROTOCOLS.INVALID <> 1 then PROTOCOLS.ID end)) as value,
                                    MONTH(b_crm_deal.DATE_CREATE) as month
                                from b_crm_company
                                    inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID
                                    left join ulab_material_to_request ON ulab_material_to_request.deal_id = b_crm_deal.ID
                                    left join PROTOCOLS ON PROTOCOLS.ID = ulab_material_to_request.protocol_id
                                where b_crm_company.ID = '{id}' AND YEAR(b_crm_deal.DATE_CREATE) = YEAR(CURDATE())
                                group by MONTH(b_crm_deal.DATE_CREATE)",
                    'days_sql' => "select
                                    b_crm_company.TITLE as label,
                                    COUNT(DISTINCT(case when PROTOCOLS.NUMBER IS NOT NULL AND PROTOCOLS.INVALID <> 1 then PROTOCOLS.ID end)) as value,
                                    DAY(b_crm_deal.DATE_CREATE) as day, DATE(b_crm_deal.DATE_CREATE) as date
                                from b_crm_company
                                    inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID
                                    left join ulab_material_to_request ON ulab_material_to_request.deal_id = b_crm_deal.ID
                                    left join PROTOCOLS ON PROTOCOLS.ID = ulab_material_to_request.protocol_id
                                where b_crm_company.ID = '{id}' AND YEAR(b_crm_deal.DATE_CREATE) = '{year}' AND MONTH(b_crm_deal.DATE_CREATE) = '{month}'
                                group by DAY(b_crm_deal.DATE_CREATE)"

                ]
            ],
        ],
        'company_use' => [
            'title' => 'Информация по оплате клиента',
            'table' => 'b_crm_company',
            'columns' => [
                'company' => [
                    'title' => 'Клиент',
                    'select' => "b_crm_company.ID company_id, b_crm_company.TITLE as company",
                    'order' => "b_crm_company.TITLE",
                    'filter' => "b_crm_company.TITLE like '%{dataFilter}%'",
                    'where' => false,
                    'group' => 'b_crm_company.ID',
                    'link' => '<a class="chart_link" data-id="{company_id}" data-entity="company_use" href="#">{company}</a>',
                ],
                'oplata' => [
                    'title' => 'Оплата',
                    'select' =>"sum(ba_tz.OPLATA) as oplata",
                    'order' => "sum(ba_tz.OPLATA)",
                    'filter' => false,
                    'default_order' => 'desc',
                    'where' => "ba_tz.OPLATA <> 0 AND ba_tz.OPLATA IS NOT NULL",
                    'group' => 'b_crm_company.ID',
                ],
            ],
            'dependency' => [
                'b_crm_deal' => [
                    'join' => "inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID"
                ],
                'ba_tz' => [
                    'join' => "left join ba_tz ON ba_tz.ID_Z = b_crm_deal.ID"
                ],
            ],
            'date_filter' => "DATE_FORMAT(b_crm_deal.DATE_CREATE, '%Y-%m-%d') >= '{dateStart}' AND DATE_FORMAT(b_crm_deal.DATE_CREATE, '%Y-%m-%d') <= '{dateEnd}'",
            'order' => 'b_crm_company.ID',
            'chart' => [
                'donut' => [],
                'bar' => [
                    'formatted' => 'Оплата',
                    'sql' => "select
                                    b_crm_company.TITLE as label,
                                    sum(ba_tz.OPLATA) as value,
                                    MONTH(b_crm_deal.DATE_CREATE) as month
                                from b_crm_company
                                    inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID
                                    left join ba_tz ON ba_tz.ID_Z = b_crm_deal.ID
                                where b_crm_company.ID = '{id}' AND YEAR(b_crm_deal.DATE_CREATE) = YEAR(CURDATE())
                                group by MONTH(b_crm_deal.DATE_CREATE)",
                    'days_sql' => "select
                                    b_crm_company.TITLE as label,
                                    sum(ba_tz.OPLATA) as value,
                                    DAY(b_crm_deal.DATE_CREATE) as day,
                                    DATE(b_crm_deal.DATE_CREATE) as date,
                                from b_crm_company
                                    inner join b_crm_deal on b_crm_deal.COMPANY_ID = b_crm_company.ID
                                    left join ba_tz ON ba_tz.ID_Z = b_crm_deal.ID
                                where b_crm_company.ID = '{id}' AND YEAR(b_crm_deal.DATE_CREATE) = '{year}' AND MONTH(b_crm_deal.DATE_CREATE) = '{month}'
                                group by DAY(b_crm_deal.DATE_CREATE)"

                ]
            ],
        ],
    ];


    /**
     * @param $entityKey
     * @param $id
     * @return array|int[]
     */
    public function getStatisticEntity($entityKey, $id)
    {
        $result = [];

        if ( !array_key_exists($entityKey, $this->entities) || empty($id) ) {
            return $result;
        }

        $dataEntity = $this->entities[$entityKey];
        $sql = $dataEntity['chart']['bar']['sql'] ?? '';

        if ( empty($sql) ) {
            return $result;
        }

        $sql = str_replace('{id}', $id, $sql);
        $data = $this->DB->Query($sql);

        $result['label'] = '';
        $result['value'][0] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $result['formatted'][0] = $dataEntity['chart']['bar']['formatted'] ?? 'Данные';

        while ($row = $data->Fetch()) {
            $result['label'] = $row['label'];
            $result['value'][0][$row['month'] - 1] = $row['value'];

            if (isset($row['value_2'])) {
                if (empty($result['value'][1])) {
                    $result['value'][1] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }

                $result['value'][1][$row['month'] - 1] = $row['value_2'];
                $result['formatted'][1] = $dataEntity['chart']['bar']['formatted_2'] ?? 'Данные 2';

                $result['double'] = true;
            }else {
                $result['double'] = false;
            }
        }

        return $result;
    }

    /**
     * @param $entityKey
     * @param $columns
     * @return array
     */
    public function getColumnsEntity($entityKey, $columns)
    {
        if ( !array_key_exists($entityKey, $this->entities) ) {
            return [];
        }

        $dataEntity = $this->entities[$entityKey];

        if (empty($columns)) {
            return $dataEntity['columns'];
        }

        $resultColumns = [];

//        $this->pre($entityKey);
//        $this->pre($columns);

        foreach ($columns as $columnKey) {
            if ( array_key_exists($columnKey, $dataEntity['columns']) ) {
                $resultColumns[$columnKey] = $dataEntity['columns'][$columnKey];
            }
        }

//        $this->pre($resultColumns);

        return $resultColumns;
    }


    /**
     * конструктор журнал
     * @param array $filter
     * @return array
     */
    public function getStatisticConstructorJournal($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => '',
            'dir' => 'DESC'
        ];

        $entityData = $this->entities[$filter['entity']['key']];



        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if ( !empty($entityData['date_filter']) && isset($filter['search']['dateStart']) ) {
                    $strFilter = str_replace('{dateStart}', $filter['search']['dateStart'], $entityData['date_filter']);
                    $strFilter = str_replace('{dateEnd}', $filter['search']['dateEnd'], $strFilter);
                    $where .= "{$strFilter} AND ";
                }

                foreach ($filter['search'] as $key => $data) {
                    if ( isset($entityData['columns'][$key]) && isset($entityData['columns'][$key]['filter']) && $entityData['columns'][$key]['filter'] !== false ) {
                        $strFilter = str_replace('{dataFilter}', $data, $entityData['columns'][$key]['filter']);
                        $where .= "{$strFilter} AND ";
                    }
                }
            }
        }



        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            $order['by'] = $entityData['columns'][$filter['order']['by']]['order']?? $entityData['order'];

        }

        // работа с пагинацией
        if (isset($filter['paginate'])) {
            $offset = 0;
            // количество строк на страницу
            if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                $length = $filter['paginate']['length'];

                if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                    $offset = $filter['paginate']['start'];
                }
                $limit = "LIMIT {$offset}, {$length}";
            }
        }


        $result = [];

        $from = $entityData['table'];
        $groupBy = '';
        $fields = [];
        $groups = [];
        $joins = [];

        foreach ($filter['entity']['column'] as $column) {
            $fields[] = $entityData['columns'][$column]['select'];

            if ( !empty($entityData['columns'][$column]['where']) ) {
                $where .= "{$entityData['columns'][$column]['where']} AND ";
            }

            if ( isset($entityData['columns'][$column]['group']) && $entityData['columns'][$column]['group'] !== false ) {
                $groups[] = $entityData['columns'][$column]['group'];
            }

            if ( isset($entityData['dependency']) ) {
                foreach ($entityData['dependency'] as $table => $joinStr) {
                    $joins[$table] = $joinStr['join'];
                }
            }

            if ( isset($entityData['columns'][$column]['dependency']) ) {
                foreach ($entityData['columns'][$column]['dependency'] as $table => $joinStr) {
                    $joins[$table] = $joinStr['join'];
                }
            }
        }
        $select = implode(', ', $fields);
        $join = implode(' ', $joins);
        $where .= "1 ";

        if ( !empty($groups) ) {
            $strGroup = implode(', ', $groups);
            $groupBy = "group by {$strGroup}";
        }

//        if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//            $_SESSION['message_warning'] = "SELECT
//                            {$select}
//                        FROM {$from}
//                        {$join}
//                        WHERE {$where}
//                        {$groupBy}
//                        ORDER BY  {$order['by']} {$order['dir']} {$limit}";
//        }

        $data = $this->DB->Query(
            "SELECT 
                        {$select}
                    FROM {$from}
                    {$join}
                    WHERE {$where}
                    {$groupBy}
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                        {$select}
                    FROM {$from}
                    {$join} 
                    WHERE 1
                    {$groupBy}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT 
                        {$select}
                    FROM {$from} 
                    {$join}
                    WHERE {$where}
                    {$groupBy}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['chart'] = $entityData['chart'] ?? [];

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


	public function getReportByUsers($dateIn, $dateOut)
	{
		$deal_arr = [];
		$deal_new = [];

		$user = new User();

		$dateStrIn = strtotime($dateIn);
		$dateStrOut = strtotime($dateOut);

		$tmp = [];
		$order = ['LAST_NAME' => 'asc'];
		$bitrixFilter = ['ACTIVE' => 'Y'];
		$bitrixParams = [];
		$users = CUser::GetList($order, $tmp, $bitrixFilter, $bitrixParams);

		$userDep = [];

		$department = [
			'54' => 'ЛФХИ',
			'55' => 'ДСЛ',
			'56' => 'ЛФМИ',
			'57' => 'ЛСМ',
		];

		while ($row = $users->Fetch()) {
			$userData = CUser::GetByID($row['ID'])->Fetch();

			$name = trim($userData['NAME']);
			$lastName = trim($userData['LAST_NAME']);
			$shortName = StringHelper::shortName($name);

			if (!empty($userData['UF_DEPARTMENT'][0] && array_key_exists($userData['UF_DEPARTMENT'][0], $department))) {
				$userDep[$row['ID']] = [
					'user_id'       => $row['ID'],
					'name'          => $name,
					'last_name'     => $lastName,
					'user_name'     => "{$lastName} {$name}",
					'short_name'    => "{$shortName}. {$lastName}",
					'is_main'       => 0,
					'department'    => $userData['UF_DEPARTMENT'][0],
					'department_name'    => $user->getDepartmentName($userData['UF_DEPARTMENT'][0]),
				];
			}
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) new_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ID
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['new_deal'] = $row['new_deal'];
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) reject_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ID
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.STAGE_ID IN ('8','LOSE','7','6','5','9','10','11','12','13') AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['reject_deal'] = $row['reject_deal'];
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) won_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ID
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.STAGE_ID IN ('2','WON') AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['won_deal'] = $row['won_deal'];
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) with_act_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ACT_NUM
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.ACT_NUM != '' AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['with_act_deal'] = $row['with_act_deal'];
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) full_pay_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ACT_NUM
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.OPLATA >= b.PRICE AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['full_pay_deal'] = $row['full_pay_deal'];
		}

		$deal = $this->DB->Query("SELECT DISTINCT count(ass.user_id) partyally_pay_deal, ass.user_id, u.LAST_NAME, u.NAME, b.ACT_NUM
									FROM ba_tz b
									LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
									LEFT JOIN b_user as u ON u.ID = ass.user_id
									WHERE b.OPLATA < b.PRICE AND b.OPLATA!= 0 AND b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'
									GROUP BY ass.user_id");

		while ($row = $deal->Fetch()) {
			if (!$userDep[$row['user_id']]) continue;
			$userDep[$row['user_id']]['partyally_pay_deal'] = $row['partyally_pay_deal'];
		}

		$protocol = $this->DB->Query("SELECT p.VERIFY, p.NUMBER
									FROM ba_tz b
									LEFT JOIN PROTOCOLS as p ON p.ID_TZ = b.ID
									WHERE b.REQUEST_TITLE <> ''
									AND b.DATE_CREATE_TIMESTAMP >= '{$dateIn}' AND b.DATE_CREATE_TIMESTAMP <='{$dateOut}'");

		while ($row = $protocol->Fetch()) {
			$protVerifyID = unserialize($row['VERIFY']);
			foreach ($protVerifyID as $id) {
				if (!$userDep[$id]) continue;
				$userDep[$id]['protocol'] += 1;
				if (!empty($row['NUMBER'])) {
					$userDep[$id]['protocol_num'] += 1;
				}
			}
		}

		return $userDep;
	}

	/**
	 * @return array
	 */
	public function getRadiologyRequest()
	{
		$request = new Request();
		$responce = [];

		$results = $this->DB->Query("SELECT b.`REQUEST_TITLE`, b.`NUM_ACT_TABLE`, b.`DATE_ACT`,b.`ID_Z`, b.`date_radiology` 
										FROM `gost_to_probe` g 
										LEFT JOIN `probe_to_materials` p ON p.`id` = g.`probe_id`
										LEFT JOIN `MATERIALS_TO_REQUESTS` m ON m.`ID` = p.`material_request_id`																				
										LEFT JOIN `ba_tz` b ON b.`ID_Z` = m.`ID_DEAL`																				
										WHERE g.`gost_method` IN (5668, 961) AND b.ID_Z < 9763 GROUP BY b.`REQUEST_TITLE` ORDER BY b.`ID_Z` ");

		while ($row = $results->Fetch()) {
			$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveRadiologyProtocol/' . $row['ID_Z'];
			$row['protocols'] = $request->getFilesFromDir($dir);
			$responce[] = $row;


		}

		return $responce;
	}
	/**
	 * @return array
	 */
	public function getRadiologyRequestNew()
	{
		$request = new Request();
		$responce = [];

		$results = $this->DB->Query("SELECT b.`REQUEST_TITLE`, b.`NUM_ACT_TABLE`, b.`DATE_ACT`,b.`ID_Z`, b.`date_radiology` 
										FROM `ulab_gost_to_probe` g 
										LEFT JOIN `ulab_material_to_request` m ON m.`id` = g.`material_to_request_id`																				
										LEFT JOIN `ba_tz` b ON b.`ID_Z` = m.`deal_id`																				
										WHERE g.`method_id` IN (2675) AND b.ID_Z > 9763 GROUP BY b.`REQUEST_TITLE` ORDER BY b.`ID_Z` ");

		while ($row = $results->Fetch()) {
			$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveRadiologyProtocol/' . $row['ID_Z'];
			$row['protocols'] = $request->getFilesFromDir($dir);
			$responce[] = $row;

		}

		return $responce;
	}



	/**
	 * @return array
	 */
	public function getMineralogyRequest()
	{
		$request = new Request();
		$responce = [];

		$results = $this->DB->Query("SELECT b.`REQUEST_TITLE`, b.`NUM_ACT_TABLE`, b.`DATE_ACT`,b.`ID_Z`, b.`date_mineralogy` FROM `gost_to_probe` g 
										LEFT JOIN `probe_to_materials` p ON p.`id` = g.`probe_id`
										LEFT JOIN `MATERIALS_TO_REQUESTS` m ON m.`ID` = p.`material_request_id`																				
										LEFT JOIN `ba_tz` b ON b.`ID_Z` = m.`ID_DEAL`																				
										WHERE `gost_method` in (879,1076,4384,4420,5182,10173,10446,10440,9965,1213,485,960,966,693 ) GROUP BY b.`REQUEST_TITLE` ORDER BY b.`ID_Z` DESC ");

		while ($row = $results->Fetch()) {
			$dir = $dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveMineralogyProtocol/' . $row['ID_Z'];
			$row['protocols'] = $request->getFilesFromDir($dir);
			$responce[] = $row;


		}

		return $responce;
	}

	public function UserByDealAssigned($deal_id)
	{
		$result = [];
		$assignedToDeal = $this->DB->Query("SELECT `user_id` FROM `assigned_to_request` WHERE `deal_id` = {$deal_id}");
		while ($assigned = $assignedToDeal->Fetch())
		{
			$result[] = $assigned['user_id'];
		}

		return $result;
	}

	public function setRadiologyDate($id, $date)
	{
		$upd = $this->DB->Query("UPDATE `ba_tz` SET `date_radiology` = '{$date}' WHERE `ID_Z` = {$id}");

		return $upd;
	}

	public function deleteProtocolRadiology($href)
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . $href;
		unlink($path);

		return !is_file($path);
	}

	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticHeaderByMonth($monthReport)
	{
		$user = new User();
		$protocolModel = new Protocol();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];

		$protocol = $this->DB->Query("SELECT * FROM `PROTOCOLS` p WHERE month(p.DATE) = {$month} AND year(p.DATE) = {$year}");
		$count = $protocol->SelectedRowsCount();

        $arr = [];
        while ($prot = $protocol->Fetch()) {
            $labVerify = unserialize($prot['VERIFY']);
            foreach ($labVerify as $assign) {
                $arr['laba'][$prot['ID']][] = $user->getDepartmentByUserId($assign);
            }

            if (!empty($prot['NUMBER'])) {
                $arr['won'][] = $prot;
                foreach ($labVerify as $assign) {
                    $arr['laba_prot_won'][$prot['ID']]['laba_id'][] = $user->getDepartmentByUserId($assign);
                    $arr['laba_prot_won'][$prot['ID']]['price'] = $protocolModel->getProtocolPrice($prot['ID'])['allPrice'];
                }

                $aaa = array_unique($arr['laba_prot_won'][$prot['ID']]['laba_id']);
                $arr['laba_prot_won'][$prot['ID']]['laba_id'] = $aaa;

            } else {
                $arr['in_work'][] = $prot;
                foreach ($labVerify as $assign) {
                    $arr['laba_prot_work'][$prot['ID']][] = $user->getDepartmentByUserId($assign);
                }
            }

            $arr['id_tz'][] = $prot['ID_TZ'];

            $newArr[] = array_unique($arr['laba'][$prot['ID']]);
            $protocolWon = $arr['laba_prot_won'];
            $protocolWork[] = array_unique($arr['laba_prot_work'][$prot['ID']]);
        }

        //Вывод всех протоколов по лабораториям
        foreach ($newArr as $lab) {
            foreach ($lab as $item) {
                $labAll[] = $item;
            }
        }
        $labOneWon[54]['count'] = 0;
        $labOneWon[55]['count'] = 0;
        $labOneWon[56]['count'] = 0;
        $labOneWon[57]['count'] = 0;

        //Вывод протоколов готовых по лабораториям


		foreach ($protocolWon as $k => $lab) {
			foreach ($lab['laba_id'] as $item) {
				$LabProtocolPrice = $protocolModel->getProtocolPrice($k);
				$labOneWon[$item]['count']++;
				$labOneWon[$item]['price'][$k] = $LabProtocolPrice[$item];
			}
		}



        //Вывод протоколов в работе по лабораториям
        foreach ($protocolWork as $lab) {
            foreach ($lab as $item) {
                $labOneWork[] = $item;
            }
        }

        //разбивка массива по лабораториям
        $labDop = $this->protDop($newArr);
        $labOneWonDop = $this->protDop($protocolWon);

        $labOneWorkDop = $this->protDop($protocolWork);

        $arrToLab = array_count_values($labAll);
        $arrToLabOneWon = array_count_values($labOneWon);
        $arrToLabOneWork = array_count_values($labOneWork);

        $arr['id_tz'] = array_unique($arr['id_tz']);


        $methodsArr = [];

        $methods = $this->DB->Query("SELECT p.NUMBER, mtr.id, ptm.id, gtp.id, ugtp.`method_id`, gtp.`price`, gtp.`assigned`, u2d.UF_DEPARTMENT as lab
									FROM `PROTOCOLS` p
									inner JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
									inner JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON umtr.`mtr_id` = mtr.`ID`
									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									LEFT JOIN `b_uts_user` u2d ON gtp.`assigned` = u2d.`VALUE_ID`
									WHERE month(p.DATE) = 9 AND year(p.DATE) = {$year} AND p.NUMBER is not NULL AND gtp.`assigned` != 0 
									group by gtp.id");

        while ($method = $methods->Fetch()) {
            $methodsArr['count_method'][] = [
                'gost' => $method['method_id'],
                'price' => $method['price']
            ];
			$labAllTest[$method['lab']][] = [
				'gost' => $method['method_id'],
				'price' => $method['price']
			];

            $methodAllGost[] = $method['method_id'];

        }


		$labAllTestLaba[54] = 0;
		$labAllTestLaba[55] = 0;
		$labAllTestLaba[56] = 0;
		$labAllTestLaba[57] = 0;

        foreach ($labAllTest as $labId => $val) {
			$labAllTestLaba[$labId] = count($val);
		}

		$labAllTestPriceLaba = [];
        foreach ($labOneWon as $labId => $val) {
			$labAllTestPriceLaba[$labId][] = $val['price'];
			$arrSum[$labId] = array_sum($val['price']);
		}

		Registry::set('count', $labAllTestLaba);
		Registry::set('price', $arrSum);

        $allTests = count($methodAllGost);

        $labsAllMethod = array_count_values($methodAllGost);
        $arrGost = implode(',', $methodAllGost);
        $infoGostArr = $this->DB->Query("SELECT * FROM `ba_gost` WHERE `ID` IN ({$arrGost})");


        $uniqGost = array_unique($methodAllGost);


        $labIds[54] = [];
        $labIds[55] = [];
        $labIds[56] = [];
        $labIds[57] = [];

        while ($infoGost = $infoGostArr->Fetch()) {
            if ( $infoGost['LFHI'] ) {
                $labIds[54][] = $infoGost['ID'];
                $labsCountMethodArr[54][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['LFMI'] ) {
                $labIds[56][] = $infoGost['ID'];
                $labsCountMethodArr[56][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['DSL'] ) {
                $labIds[55][] = $infoGost['ID'];
                $labsCountMethodArr[55][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['LSM'] ) {
                $labIds[57][] = $infoGost['ID'];
                $labsCountMethodArr[57][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['OSK'] ) {
                $labIds[58][] = $infoGost['ID'];
                $labsCountMethodArr[58][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
            }
        }



        $arr['count_value'] = array_count_values($methodsArr['count_method']);
        arsort($arr['count_value']);
        foreach ($arr['count_value'] as $key => $item) {
            if ($item >= 20) {
                $arr['count_value_50'][$key] = $item;
            }
        }

        $labsCountMethod = [];

        foreach ($labIds as $k => $val) {
            $CountMethod[$k] = count(array_unique($val));
            $CountAllMethod[$k] = count($val);
        }



        foreach ($labsCountMethodArr as $k => $val) {
            $labsCountMethodMore40[$k] = $this->countUniq($val);
        }

        $data['count_value'] = count($arr['count_value_50']);
        $data['count_tests'] = $allTests;
        $data['count_method'] = count($uniqGost);
        $data['labs'] = $arrToLab;
        $data['labs_won'] = $labOneWon;
        $data['labs_work'] = $arrToLabOneWork;
        $data['labs_dop'] = $labDop;
        $data['labs_Won_dop'] = $labOneWonDop;
        $data['labs_Work_dop'] = $labOneWorkDop;
        $data['methodic_in_labs'] = $CountMethod;
        $data['all_methodic_in_labs'] = $labAllTestLaba;
        $data['methodic_in_labs_more'] = $labsCountMethodMore40;
        $data['count'] = $count;

        $data['won'] = count($arr['won']);
        $data['in_work'] = count($arr['in_work']);

        return $data;
	}

	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticHeaderByMonthNew($monthReport)
	{
		$user = new User();
		$protocolModel = new Protocol();
		$labModel = new Lab();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];
		$labOneWon[54]['count'] = 0;
		$labOneWon[55]['count'] = 0;
		$labOneWon[56]['count'] = 0;
		$labOneWon[57]['count'] = 0;

		$labCoWon[54]['count'] = 0;
		$labCoWon[55]['count'] = 0;
		$labCoWon[56]['count'] = 0;
		$labCoWon[57]['count'] = 0;

		$protocol = $this->DB->Query("SELECT p.ID, p.NUMBER, SUM(ugtp.price) as prot_sum, 
										concat('[', group_concat(distinct l.id_dep separator ','), ']') as labs
										FROM `PROTOCOLS` p 
										LEFT JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
										LEFT JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
										LEFT JOIN `ulab_methods_lab` u2d ON ugtp.`method_id` = u2d.`method_id`
										LEFT JOIN `ba_laba` as l ON u2d.lab_id = l.ID
										WHERE p.NUMBER is not null and month(p.DATE) = {$month}
										AND year(p.DATE) = {$year} AND is_non_actual <> 1
										group by p.ID");
		$count = $protocol->SelectedRowsCount();
		$data['coWon']['all']['count'] = 0;
		$data['won']['all']['count'] = 0;
        $arr = [];

        while ($prot = $protocol->Fetch()) {
			$prot['labs'] = json_decode($prot['labs'], true);

			if (count($prot['labs']) == 1) {
				$data['won']['all']['count']++;
				$data['won'][$prot['labs'][0]]['count']++;
				$data['won'][$prot['labs'][0]][$prot['ID']] = $prot['prot_sum'];
			} elseif (count($prot['labs']) > 1) {
				$data['coWon']['all']['count']++;
				foreach ($prot['labs'] as $lab) {
					$data['coWon'][$lab]['count']++;
					$labCoWon[$lab]['price'][$prot['ID']] = $labModel->getProtocolPrice;
				}
			};

            $labVerify = unserialize($prot['VERIFY']);
            foreach ($labVerify as $assign) {
                $arr['laba'][$prot['ID']][] = $user->getDepartmentByUserId($assign);
            }

            if (!empty($prot['NUMBER'])) {
                $arr['won'][] = $prot;
                    $arr['laba_prot_won'][$prot['ID']]['price'] = $protocolModel->getProtocolPriceNew($prot['ID'])['allPrice'];
                    $arr['laba_prot_won'][$prot['ID']]['laba_id'] = $protocolModel->getProtocolPriceNew($prot['ID'])['lab'];


            } else {
                $arr['in_work'][] = $prot;
                foreach ($labVerify as $assign) {
                    $arr['laba_prot_work'][$prot['ID']][] = $user->getDepartmentByUserId($assign);
                }
            }

            $arr['id_tz'][] = $prot['ID_TZ'];

            $newArr[] = array_unique($arr['laba'][$prot['ID']]);
            $protocolWon = $arr['laba_prot_won'];
            $protocolWork[] = array_unique($arr['laba_prot_work'][$prot['ID']]);
        }
//		$this->pre($data);

        //Вывод всех протоколов по лабораториям
        foreach ($newArr as $lab) {
            foreach ($lab as $item) {
                $labAll[] = $item;
            }
        }
        $labOneWon[54]['count'] = 0;
        $labOneWon[55]['count'] = 0;
        $labOneWon[56]['count'] = 0;
        $labOneWon[57]['count'] = 0;

        //Вывод протоколов готовых по лабораториям


		foreach ($protocolWon as $k => $lab) {
			foreach ($lab['laba_id'] as $item) {
				$LabProtocolPrice = $protocolModel->getProtocolPrice($k);
				$labOneWon[$item]['count']++;
				$labOneWon[$item]['price'][$k] = $LabProtocolPrice[$item];
			}
		}



        //Вывод протоколов в работе по лабораториям
        foreach ($protocolWork as $lab) {
            foreach ($lab as $item) {
                $labOneWork[] = $item;
            }
        }

        //разбивка массива по лабораториям
        $labDop = $this->protDop($newArr);
        $labOneWonDop = $this->protDop($protocolWon);

        $labOneWorkDop = $this->protDop($protocolWork);

        $arrToLab = array_count_values($labAll);
        $arrToLabOneWon = array_count_values($labOneWon);
        $arrToLabOneWork = array_count_values($labOneWork);

        $arr['id_tz'] = array_unique($arr['id_tz']);


        $methodsArr = [];

        $methods = $this->DB->Query("SELECT p.NUMBER, mtr.id, ptm.id, gtp.id, ugtp.`method_id`, gtp.`price`, gtp.`assigned`, u2d.UF_DEPARTMENT as lab
									FROM `PROTOCOLS` p
									inner JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
									inner JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON umtr.`mtr_id` = mtr.`ID`
									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									LEFT JOIN `b_uts_user` u2d ON gtp.`assigned` = u2d.`VALUE_ID`
									WHERE month(p.DATE) = 9 AND year(p.DATE) = {$year} AND p.NUMBER is not NULL AND gtp.`assigned` != 0 
									group by gtp.id");

        while ($method = $methods->Fetch()) {
            $methodsArr['count_method'][] = [
                'gost' => $method['method_id'],
                'price' => $method['price']
        ];
			$labAllTest[$method['lab']][] = [
				'gost' => $method['method_id'],
				'price' => $method['price']
			];

            $methodAllGost[] = $method['method_id'];

        }

		$labAllTestLaba[54] = 0;
		$labAllTestLaba[55] = 0;
		$labAllTestLaba[56] = 0;
		$labAllTestLaba[57] = 0;

        foreach ($labAllTest as $labId => $val) {
			$labAllTestLaba[$labId] = count($val);
		}

		$labAllTestPriceLaba = [];
        foreach ($labOneWon as $labId => $val) {
			$labAllTestPriceLaba[$labId][] = $val['price'];
			$arrSum[$labId] = array_sum($val['price']);
		}

		Registry::set('count', $labAllTestLaba);
		Registry::set('price', $arrSum);

        $allTests = count($methodAllGost);

        $labsAllMethod = array_count_values($methodAllGost);
        $arrGost = implode(',', $methodAllGost);
        $infoGostArr = $this->DB->Query("SELECT * FROM `ba_gost` WHERE `ID` IN ({$arrGost})");


        $uniqGost = array_unique($methodAllGost);


        $labIds[54] = [];
        $labIds[55] = [];
        $labIds[56] = [];
        $labIds[57] = [];

        while ($infoGost = $infoGostArr->Fetch()) {
            if ( $infoGost['LFHI'] ) {
                $labIds[54][] = $infoGost['ID'];
                $labsCountMethodArr[54][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['LFMI'] ) {
                $labIds[56][] = $infoGost['ID'];
                $labsCountMethodArr[56][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['DSL'] ) {
                $labIds[55][] = $infoGost['ID'];
                $labsCountMethodArr[55][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['LSM'] ) {
                $labIds[57][] = $infoGost['ID'];
                $labsCountMethodArr[57][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
                continue;
            }
            if ( $infoGost['OSK'] ) {
                $labIds[58][] = $infoGost['ID'];
                $labsCountMethodArr[58][$infoGost['ID']] = $labsAllMethod[$infoGost['ID']];
            }
        }



        $arr['count_value'] = array_count_values($methodsArr['count_method']);
        arsort($arr['count_value']);
        foreach ($arr['count_value'] as $key => $item) {
            if ($item >= 20) {
                $arr['count_value_50'][$key] = $item;
            }
        }

        $labsCountMethod = [];

        foreach ($labIds as $k => $val) {
            $CountMethod[$k] = count(array_unique($val));
            $CountAllMethod[$k] = count($val);
        }



        foreach ($labsCountMethodArr as $k => $val) {
            $labsCountMethodMore40[$k] = $this->countUniq($val);
        }



        $data['count_value'] = count($arr['count_value_50']);
        $data['count_tests'] = $allTests;
        $data['count_method'] = count($uniqGost);
        $data['labs'] = $arrToLab;
        $data['labs_won'] = $labOneWon;
        $data['labs_work'] = $arrToLabOneWork;
        $data['labs_dop'] = $labDop;
        $data['labs_Won_dop'] = $labOneWonDop;
        $data['labs_Work_dop'] = $labOneWorkDop;
        $data['methodic_in_labs'] = $CountMethod;
        $data['all_methodic_in_labs'] = $labAllTestLaba;
        $data['methodic_in_labs_more'] = $labsCountMethodMore40;
        $data['count'] = $count;

//        $data['won'] = count($arr['won']);
//        $data['in_work'] = count($arr['in_work']);

        return $data;
	}

	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticStaffByMonth($monthReport)
	{
		$user = new User();
		$request = new Request();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];

		$userArr = $user->getUserByDepartment();

		$getAllCount = Registry::get('count');
		$getAllPrice = Registry::get('price');

		$assToGost = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, gtp.`price`, p.NUMBER 
									FROM `PROTOCOLS` p
									INNER JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
									INNER JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON umtr.`mtr_id` = mtr.`ID`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									WHERE month(p.DATE) = {$month} AND year(p.DATE) = {$year} AND p.NUMBER is not NULL
									group by gtp.id");

		while ($idAssToGost = $assToGost->Fetch()) {
			$protocolNum = [];
			$gostList[] = $idAssToGost;
		}

		foreach ($userArr as $key => $item) {
			if ($key == 58) {
				continue;
			}

			foreach ($item as $k => $val) {
				$gostByUser = [];
				$priceGostByUser = [];
				foreach ($gostList as $gost) {
					if ($gost['assigned'] == $k) {
						$gostByUser[] = $gost['gost_method'];
						$priceGostByUser[] = $gost['price'];
						$protocolNum[$k][] = $gost['NUMBER'];
					}
				}
				$procentMethod = $getAllCount[$key] != 0 ? round((count($gostByUser)/$getAllCount[$key]) * 100, 0) : '-';
				$procentPrice = $getAllPrice[$key] != 0 ? round((array_sum($priceGostByUser)/$getAllPrice[$key]) * 100, 1) : '-';
				$userByLab[$key][$k] = [
					'test_by_user' => count($gostByUser),
					'procent_by_test' => $procentMethod,
					'procent_by_price' => $procentPrice,
					'id' => $val['user_id'],
                    'short_name' => $val['short_name'],
                    'price_test' => StringHelper::priceFormatRus(array_sum($priceGostByUser)),
                    'protocol' => array_unique($protocolNum[$k]),
				];
			}
		}
		$gostList = [];
		$assToGostInWork = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, b.`ID_Z`,b.ID, b.`REQUEST_TITLE`, ugtp.id 
                                    FROM `ba_tz` b 
									LEFT JOIN `ulab_material_to_request` umtp ON umtp.`deal_id` = b.`ID_Z`
									LEFT JOIN `ulab_gost_to_probe` ugtp ON umtp.`id` = ugtp.`material_to_request_id` 
									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON umtp.`mtr_id` = mtr.`ID`
									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									LEFT JOIN PROTOCOLS p ON umtp.`protocol_id` = p.`ID`
									WHERE year(b.DATE_CREATE_TIMESTAMP) <= {$year} AND month(b.DATE_CREATE_TIMESTAMP) <= {$month}
									and gtp.`assigned` != 0 and gtp.`assigned` is not null and umtp.`protocol_id` = ''
									and ACT_NUM is not null and b.STAGE_ID != 'LOSE' and b.STAGE_ID < 5 and (b.PRICE - b.OPLATA = 0) group by gtp.id");

		while ($idAssToGostInWork = $assToGostInWork->Fetch()) {
			$gostList[] = $idAssToGostInWork;
		}

		foreach ($userArr as $key => $item) {
			if ($key == 58) {
				continue;
			}

			foreach ($item as $k => $val) {
				$gostByUserInWork = [];
				foreach ($gostList as $gost) {
					if ($gost['assigned'] == $k) {
						$gostByUserInWork[] = $gost['gost_method'];
					}
				}

				$userByLab[$key][$k]['request_in_work'] = count($gostByUserInWork);
			}
		}

//        $methods = $this->DB->Query("SELECT ptm.*, gtp.`gost_method`, gtp.`assigned`, mtr.`ID_DEAL` FROM `ba_tz` b
//									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
//									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
//									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
//									  WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} AND year(b.DATE_CREATE_TIMESTAMP) = {$year}");
//
//        $gostAssignedRequest = [];
//
//        while ($row = $methods->Fetch()) {
//            $gostAssignedRequest[] = $row;
//            $results[] = unserialize($row['RESULTS']);
//        }
//        $userByLab = [];
//        foreach ($userArr as $key => $item) {
//            foreach ($item as $k => $val) {
//                $methodByUser = [];
//                $requestByUser = [];
//                $probeByUser = [];
//                $probeByUserActive = [];
//                $probeByUserNonProbe = [];
//				$requestDoneByUser = [];
//                foreach ($gostAssignedRequest as $gosts) {
//                    if ($gosts['assigned'] == $k) {
//                        //Участие в ГОСТах
//                        $methodByUser[] = $gosts['gost_method'];
//                        //Участие в заявках
//                        $requestByUser[] = $gosts['ID_DEAL'];
//                        //Испытано проб
//                        // всего
//                        $probeByUser[] = $gosts['id'];
//                        if (!empty($gosts['cipher'])) {
//                            //проб с номером
//                            $probeByUserActive[] = $gosts['id'];
//                        } else {
//                            //проб не принятых
//                            $probeByUserNonProbe[] = $gosts['id'];
//                        }
//                    }
//                }
//
//                $probeByUserActiveUnique = array_unique($probeByUserActive);
//                $probeByUserNonProbeUnique = array_unique($probeByUserNonProbe);
//                $probeByUserUnique = array_unique($probeByUser);
//                $requestByUserUnique = array_unique($requestByUser);
//				foreach ($requestByUserUnique as $item) {
//					$stageRequest = $request->getStageRequestById($item);
//					//Участие в завершенных заявках
//					if ($stageRequest == 2 || $stageRequest == 4 || $stageRequest == 'WON') {
//						$requestDoneByUser[] = $item;
//					}
//				}
//
//
//                $userByLab[$key][$k] = [
//                    'id' => $val['user_id'],
//                    'short_name' => $val['short_name'],
//                    'count_requests' => count($requestByUserUnique),
//                    'count_gosts' => count($methodByUser),
//                    'active_probe' => count($probeByUserActiveUnique),
//                    'non_probe' => count($probeByUserNonProbeUnique),
//                    'all_probe' => count($probeByUserUnique),
//                    'stage_request' => count($requestDoneByUser),
//                ];
//            }
//        }

        $data['user'] = $userByLab;
        return $data;
    }
	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticStaffByMonthNew($monthReport)
	{
		$user = new User();
		$request = new Request();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];

		$userArr = $user->getUserByDepartment();

		$getAllCount = Registry::get('count');
		$getAllPrice = Registry::get('price');

		$assToGost = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, gtp.`price`, p.NUMBER 
									FROM `PROTOCOLS` p
									INNER JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
									INNER JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON umtr.`mtr_id` = mtr.`ID`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									WHERE month(p.DATE) = {$month} AND year(p.DATE) = {$year} AND p.NUMBER is not NULL
									group by gtp.id");

		while ($idAssToGost = $assToGost->Fetch()) {
			$protocolNum = [];
			$gostList[] = $idAssToGost;
		}

		foreach ($userArr as $key => $item) {
			if ($key == 58) {
				continue;
			}

			foreach ($item as $k => $val) {
				$gostByUser = [];
				$priceGostByUser = [];
				foreach ($gostList as $gost) {
					if ($gost['assigned'] == $k) {
						$gostByUser[] = $gost['gost_method'];
						$priceGostByUser[] = $gost['price'];
						$protocolNum[$k][] = $gost['NUMBER'];
					}
				}
				$procentMethod = $getAllCount[$key] != 0 ? round((count($gostByUser)/$getAllCount[$key]) * 100, 0) : '-';
				$procentPrice = $getAllPrice[$key] != 0 ? round((array_sum($priceGostByUser)/$getAllPrice[$key]) * 100, 1) : '-';
				$userByLab[$key][$k] = [
					'test_by_user' => count($gostByUser),
					'procent_by_test' => $procentMethod,
					'procent_by_price' => $procentPrice,
					'id' => $val['user_id'],
                    'short_name' => $val['short_name'],
                    'price_test' => StringHelper::priceFormatRus(array_sum($priceGostByUser)),
                    'protocol' => array_unique($protocolNum[$k]),
				];
			}
		}
		$gostList = [];
		$assToGostInWork = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, b.`ID_Z`,b.ID, b.`REQUEST_TITLE`, ugtp.id 
                                    FROM `ba_tz` b 
									LEFT JOIN `ulab_material_to_request` umtp ON umtp.`deal_id` = b.`ID_Z`
									LEFT JOIN `ulab_gost_to_probe` ugtp ON umtp.`id` = ugtp.`material_to_request_id` 
									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON umtp.`mtr_id` = mtr.`ID`
									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									LEFT JOIN PROTOCOLS p ON umtp.`protocol_id` = p.`ID`
									WHERE year(b.DATE_CREATE_TIMESTAMP) <= {$year} AND month(b.DATE_CREATE_TIMESTAMP) <= {$month}
									and gtp.`assigned` != 0 and gtp.`assigned` is not null and umtp.`protocol_id` = ''
									and ACT_NUM is not null and b.STAGE_ID != 'LOSE' and b.STAGE_ID < 5 and (b.PRICE - b.OPLATA = 0) group by gtp.id");

		while ($idAssToGostInWork = $assToGostInWork->Fetch()) {
			$gostList[] = $idAssToGostInWork;
		}

		foreach ($userArr as $key => $item) {
			if ($key == 58) {
				continue;
			}

			foreach ($item as $k => $val) {
				$gostByUserInWork = [];
				foreach ($gostList as $gost) {
					if ($gost['assigned'] == $k) {
						$gostByUserInWork[] = $gost['gost_method'];
					}
				}

				$userByLab[$key][$k]['request_in_work'] = count($gostByUserInWork);
			}
		}

//        $methods = $this->DB->Query("SELECT ptm.*, gtp.`gost_method`, gtp.`assigned`, mtr.`ID_DEAL` FROM `ba_tz` b
//									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
//									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
//									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
//									  WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} AND year(b.DATE_CREATE_TIMESTAMP) = {$year}");
//
//        $gostAssignedRequest = [];
//
//        while ($row = $methods->Fetch()) {
//            $gostAssignedRequest[] = $row;
//            $results[] = unserialize($row['RESULTS']);
//        }
//        $userByLab = [];
//        foreach ($userArr as $key => $item) {
//            foreach ($item as $k => $val) {
//                $methodByUser = [];
//                $requestByUser = [];
//                $probeByUser = [];
//                $probeByUserActive = [];
//                $probeByUserNonProbe = [];
//				$requestDoneByUser = [];
//                foreach ($gostAssignedRequest as $gosts) {
//                    if ($gosts['assigned'] == $k) {
//                        //Участие в ГОСТах
//                        $methodByUser[] = $gosts['gost_method'];
//                        //Участие в заявках
//                        $requestByUser[] = $gosts['ID_DEAL'];
//                        //Испытано проб
//                        // всего
//                        $probeByUser[] = $gosts['id'];
//                        if (!empty($gosts['cipher'])) {
//                            //проб с номером
//                            $probeByUserActive[] = $gosts['id'];
//                        } else {
//                            //проб не принятых
//                            $probeByUserNonProbe[] = $gosts['id'];
//                        }
//                    }
//                }
//
//                $probeByUserActiveUnique = array_unique($probeByUserActive);
//                $probeByUserNonProbeUnique = array_unique($probeByUserNonProbe);
//                $probeByUserUnique = array_unique($probeByUser);
//                $requestByUserUnique = array_unique($requestByUser);
//				foreach ($requestByUserUnique as $item) {
//					$stageRequest = $request->getStageRequestById($item);
//					//Участие в завершенных заявках
//					if ($stageRequest == 2 || $stageRequest == 4 || $stageRequest == 'WON') {
//						$requestDoneByUser[] = $item;
//					}
//				}
//
//
//                $userByLab[$key][$k] = [
//                    'id' => $val['user_id'],
//                    'short_name' => $val['short_name'],
//                    'count_requests' => count($requestByUserUnique),
//                    'count_gosts' => count($methodByUser),
//                    'active_probe' => count($probeByUserActiveUnique),
//                    'non_probe' => count($probeByUserNonProbeUnique),
//                    'all_probe' => count($probeByUserUnique),
//                    'stage_request' => count($requestDoneByUser),
//                ];
//            }
//        }

        $data['user'] = $userByLab;
        return $data;
    }

	/**
	 * @param $monthReport
	 * @return array
	 */
    public function getStatisticMFCByMonth($monthReport)
	{
		$userModel = new User();
		$requestModel = new Request();
		$companyModel = new Company();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID FROM `ba_tz` b 
									WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} 
									AND year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.TYPE_ID = 'SALE'");

		while ($request = $requestArr->Fetch()) {
			$result['all'][] = $request['ID_Z'];
			$aaa[] = $request['ID_Z'];
			$stageRequest = $requestModel->getStageRequestById($request['ID_Z']);
			if ($stageRequest == 'WON') {
				$result['request_won'][] = $request['ID_Z'];
			} elseif ($stageRequest == 'LOSE' || $stageRequest == '7' || $stageRequest == '8' || $stageRequest == '9' ||
				$stageRequest == '10' || $stageRequest == '11' || $stageRequest == '12' || $stageRequest == '13') {
				$result['request_lose'][] = $request['ID_Z'];
			}
			$labaArr = explode(',', $request['LABA_ID']);
			if (count($labaArr) > 1 ) {
				$result['laba_non_uniq'][] = $request['ID_Z'];
			} elseif (count($labaArr) == 1) {
				$result['laba_uniq'][] = $request['ID_Z'];
			}

			$companyInfo = $companyModel->getById($request['COMPANY_ID']);
			if (strtotime($companyInfo['DATE_CREATE']) >= strtotime($year . '-' . $month . '-01') && strtotime($companyInfo['DATE_CREATE']) <= strtotime($year . '-' . $month . '-31')) {
				$result['new_company'][] = $companyInfo['ID'];
			}

		}

		$actBase = $this->DB->Query("SELECT * FROM ACT_BASE WHERE month(ACT_DATE) = {$month} 
									AND year(ACT_DATE) = {$year}");

		while ($act = $actBase->Fetch()) {
			$result['new_act'][] = $act['ACT_NUM'];
			$stageRequest = $requestModel->getStageRequestById($act['ID_Z']);
			if ($stageRequest == 'WON') {
				$result['act_won'][] = $request['ID_Z'];
			} elseif ($stageRequest == 'LOSE' || $stageRequest == '7' || $stageRequest == '8' || $stageRequest == '9' ||
				$stageRequest == '10' || $stageRequest == '11' || $stageRequest == '12' || $stageRequest == '13') {
				$result['act_lose'][] = $request['ID_Z'];
			}
		}

		$dogovorArr = $this->DB->Query("SELECT * FROM DOGOVOR WHERE month(DATE) = {$month} 
									AND year(DATE) = {$year}");

		while ($dogovor = $dogovorArr->Fetch()) {
			$result['dogovors'][] = $dogovor['NUMBER'];
			if (!empty($dogovor['PDF'])) {
				$result['dogovors_complete'][] = $dogovor['NUMBER'];
			}
		}

		$tzArr = $this->DB->Query("SELECT * FROM TZ_DOC WHERE month(DATE) = {$month} 
									AND year(DATE) = {$year}");

		while ($tz = $tzArr->Fetch()) {
			$result['tz'][] = $tz['ID'];
		}

		$invoiceArr = $this->DB->Query("SELECT i.DATE, i.ID, b.OPLATA, b.PRICE FROM INVOICE i, ba_tz b WHERE month(i.DATE) = {$month} 
									AND year(i.DATE) = {$year} AND b.ID = i.TZ_ID");
		$result['invoice_price'] = 0;
		$result['invoice_pay_win'] = 0;
		while ($invoice = $invoiceArr->Fetch()) {
			$result['invoice'][] = $invoice['ID'];
			$result['invoice_price'] += $invoice['PRICE'];
			if ($invoice['PRICE'] == $invoice['OPLATA']) {
				$result['invoice_pay_win'] += $invoice['OPLATA'];
			}
		}



		$data['request'] = count($result['all']);
		$data['request_won'] = count($result['request_won']);
		$data['request_lose'] = count($result['request_lose']);
		$data['laba_non_uniq'] = count($result['laba_non_uniq']);
		$data['laba_uniq'] = count($result['laba_uniq']);
		$data['new_company'] = count($result['new_company']);
		$data['new_act'] = count($result['new_act']);
		$data['act_won'] = count($result['act_won']);
		$data['act_work'] = count($result['new_act']) - count($result['act_won']);
		$data['dogovors_all'] = count($result['dogovors']);
		$data['dogovors_complete'] = count($result['dogovors_complete']);
		$data['dogovors_null'] = count($result['dogovors']) - count($result['dogovors_complete']);
		$data['invoice'] = count($result['invoice']);
		$data['invoice_pay_win'] = number_format($result['invoice_pay_win'], 2, ',', ' ') . " руб.";
		$data['invoice_price'] = number_format($result['invoice_price'], 2, ',', ' ') . " руб.";
		$data['tz'] = count($result['tz']);

		return $data;
	}
	/**
	 * @param $monthReport
	 * @return array
	 */
    public function getStatisticMFCByMonthNew($monthReport)
	{
		$userModel = new User();
		$requestModel = new Request();
		$companyModel = new Company();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y',  strtotime($monthReport));

		$data = [];

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID FROM `ba_tz` b 
									WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} 
									AND year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.TYPE_ID = 'SALE'");

		while ($request = $requestArr->Fetch()) {
			$result['all'][] = $request['ID_Z'];
			$aaa[] = $request['ID_Z'];
			$stageRequest = $requestModel->getStageRequestById($request['ID_Z']);
			if ($stageRequest == 'WON') {
				$result['request_won'][] = $request['ID_Z'];
			} elseif ($stageRequest == 'LOSE' || $stageRequest == '7' || $stageRequest == '8' || $stageRequest == '9' ||
				$stageRequest == '10' || $stageRequest == '11' || $stageRequest == '12' || $stageRequest == '13') {
				$result['request_lose'][] = $request['ID_Z'];
			}
			$labaArr = explode(',', $request['LABA_ID']);
			if (count($labaArr) > 1 ) {
				$result['laba_non_uniq'][] = $request['ID_Z'];
			} elseif (count($labaArr) == 1) {
				$result['laba_uniq'][] = $request['ID_Z'];
			}

			$companyInfo = $companyModel->getById($request['COMPANY_ID']);
			if (strtotime($companyInfo['DATE_CREATE']) >= strtotime($year . '-' . $month . '-01') && strtotime($companyInfo['DATE_CREATE']) <= strtotime($year . '-' . $month . '-31')) {
				$result['new_company'][] = $companyInfo['ID'];
			}

		}

		$actBase = $this->DB->Query("SELECT * FROM ACT_BASE WHERE month(ACT_DATE) = {$month} 
									AND year(ACT_DATE) = {$year}");

		while ($act = $actBase->Fetch()) {
			$result['new_act'][] = $act['ACT_NUM'];
			$stageRequest = $requestModel->getStageRequestById($act['ID_Z']);
			if ($stageRequest == 'WON') {
				$result['act_won'][] = $request['ID_Z'];
			} elseif ($stageRequest == 'LOSE' || $stageRequest == '7' || $stageRequest == '8' || $stageRequest == '9' ||
				$stageRequest == '10' || $stageRequest == '11' || $stageRequest == '12' || $stageRequest == '13') {
				$result['act_lose'][] = $request['ID_Z'];
			}
		}

		$dogovorArr = $this->DB->Query("SELECT * FROM DOGOVOR WHERE month(DATE) = {$month} 
									AND year(DATE) = {$year}");

		while ($dogovor = $dogovorArr->Fetch()) {
			$result['dogovors'][] = $dogovor['NUMBER'];
			if (!empty($dogovor['PDF'])) {
				$result['dogovors_complete'][] = $dogovor['NUMBER'];
			}
		}

		$tzArr = $this->DB->Query("SELECT * FROM TZ_DOC WHERE month(DATE) = {$month} 
									AND year(DATE) = {$year}");

		while ($tz = $tzArr->Fetch()) {
			$result['tz'][] = $tz['ID'];
		}

		$invoiceArr = $this->DB->Query("SELECT i.DATE, i.ID, b.OPLATA, b.PRICE FROM INVOICE i, ba_tz b WHERE month(i.DATE) = {$month} 
									AND year(i.DATE) = {$year} AND b.ID = i.TZ_ID");
		$result['invoice_price'] = 0;
		$result['invoice_pay_win'] = 0;
		while ($invoice = $invoiceArr->Fetch()) {
			$result['invoice'][] = $invoice['ID'];
			$result['invoice_price'] += $invoice['PRICE'];
			if ($invoice['PRICE'] == $invoice['OPLATA']) {
				$result['invoice_pay_win'] += $invoice['OPLATA'];
			}
		}



		$data['request'] = count($result['all']);
		$data['request_won'] = count($result['request_won']);
		$data['request_lose'] = count($result['request_lose']);
		$data['laba_non_uniq'] = count($result['laba_non_uniq']);
		$data['laba_uniq'] = count($result['laba_uniq']);
		$data['new_company'] = count($result['new_company']);
		$data['new_act'] = count($result['new_act']);
		$data['act_won'] = count($result['act_won']);
		$data['act_work'] = count($result['new_act']) - count($result['act_won']);
		$data['dogovors_all'] = count($result['dogovors']);
		$data['dogovors_complete'] = count($result['dogovors_complete']);
		$data['dogovors_null'] = count($result['dogovors']) - count($result['dogovors_complete']);
		$data['invoice'] = count($result['invoice']);
		$data['invoice_pay_win'] = number_format($result['invoice_pay_win'], 2, ',', ' ') . " руб.";
		$data['invoice_price'] = number_format($result['invoice_price'], 2, ',', ' ') . " руб.";
		$data['tz'] = count($result['tz']);

		return $data;
	}

	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticFinanceByMonth($monthReport)
	{
		$userModel = new User();
		$requestModel = new Request();
		$companyModel = new Company();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y', strtotime($monthReport));

		$data = [];
		$requestList = [];
		$totalPrice = 0;
		$oplata = [];
		$setOplata = 0;
		$unOplata = [];
		$paid = [];
		$paidTotal = [];
		$paidTotalByYear = [];
		$labIds[54] = [];
		$labIds[55] = [];
		$labIds[56] = [];
		$labIds[57] = [];
		$loseRequest = [
			'LOSE',
			'5',
			'6',
			'7',
			'8',
			'9',
			'10',
			'11',
			'12',
			'EXECUTING'
		];

		$labIdsByYear[54] = [];
		$labIdsByYear[55] = [];
		$labIdsByYear[56] = [];
		$labIdsByYear[57] = [];

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID, b.PRICE  FROM `ba_tz` b 
									WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} 
									AND year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.STAGE_ID NOT IN ('LOSE','5','6','7','8','9','10','11','12')
									AND b.TYPE_ID = 'SALE'");

		while ($request = $requestArr->Fetch()) {
				$requestList[] = $request['ID_Z'];
		}
		$requestStr = implode(',', $requestList);

		$assToGost = $this->DB->Query("SELECT gtp.price, bg.LFHI, bg.LFMI, bg.LSM, bg.DSL, b.PRICE, b.OPLATA, b.ID
									FROM `ba_tz` b
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									INNER JOIN `ba_gost` bg ON gtp.gost_method = bg.ID
									WHERE b.ID_Z IN ({$requestStr})");

		while ($reqByLab = $assToGost->Fetch()) {

			$data['labs'][] = $reqByLab;
			if ($reqByLab['LFHI'] == 1) {
				$labIds[54]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[54]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[54]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[54]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[54]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[54]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			} elseif ($reqByLab['LFMI'] == 1) {
				$labIds[56]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[56]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[56]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[56]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[56]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[56]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			} elseif ($reqByLab['LSM'] == 1) {
				$labIds[57]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[57]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[57]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[57]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[57]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[57]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			}elseif ($reqByLab['DSL'] == 1) {
				$labIds[55]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[55]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[55]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[55]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[55]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[55]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			}
		}

		foreach ($oplata as $k => $val) {
			$setOplata += $val['oplata'];
			if ($val['oplata'] == 0) {
				$unOplata[] = $val['price'] - $val['oplata'];
				$unOplataID[] = [
					'id' =>$k,
					'price' =>$val['price']
					];
			} elseif ($val['oplata'] >= $val['price']) {
				$paidTotal[] = $val['price'];
				$paidTotalID[] = [
					'id' =>$k
				];
			} else {
				$partiallyPaidTotal[] = $val['oplata'];
			}
		}

		$data['test'] = $requestList;
		$data['total'] =  number_format($totalPrice, 2, ',', ' ');
		$data['totalLFMI'] =  number_format(array_sum($labIds[56]['totalPrice']), 2, ',', ' ');
		$data['totalLFHI'] =  number_format(array_sum($labIds[54]['totalPrice']), 2, ',', ' ');
		$data['totalLSM'] =  number_format(array_sum($labIds[57]['totalPrice']), 2, ',', ' ');
		$data['totalDSL'] =  number_format(array_sum($labIds[55]['totalPrice']), 2, ',', ' ');
		$data['notPaidTotal'] =  count($unOplata);
		$data['notPaidTotalLFMI'] =  count($labIds[56]['notPaidTotal']);
		$data['notPaidTotalLFHI'] =  count($labIds[54]['notPaidTotal']);
		$data['notPaidTotalLSM'] =  count($labIds[57]['notPaidTotal']);
		$data['notPaidTotalDSL'] =  count($labIds[55]['notPaidTotal']);
		$data['paidTotal'] =  number_format(array_sum($paidTotal), 2, ',', ' ');
		$data['paidTotalLFMI'] =  number_format(array_sum($labIds[56]['paidTotal']), 2, ',', ' ');
		$data['paidTotalLFHI'] =  number_format(array_sum($labIds[54]['paidTotal']), 2, ',', ' ');
		$data['paidTotalDSL'] =  number_format(array_sum($labIds[55]['paidTotal']), 2, ',', ' ');
		$data['paidTotalLSM'] =  number_format(array_sum($labIds[57]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotal'] =  number_format($totalPrice - array_sum($paidTotal), 2, ',', ' ');
		$data['notPaidSumTotalLFMI'] =  number_format(array_sum($labIds[56]['totalPrice']) - array_sum($labIds[56]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalLFHI'] =  number_format(array_sum($labIds[54]['totalPrice']) - array_sum($labIds[54]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalLSM'] =  number_format(array_sum($labIds[57]['totalPrice']) - array_sum($labIds[57]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalDSL'] =  number_format(array_sum($labIds[55]['totalPrice']) - array_sum($labIds[55]['paidTotal']), 2, ',', ' ');
		$data['partiallyPaidTotal'] =  count($partiallyPaidTotal);
		$data['partiallyPaidTotalLFMI'] =  count($labIds[56]['partiallyPaidTotal']);
		$data['partiallyPaidTotalLFHI'] =  count($labIds[54]['partiallyPaidTotal']);
		$data['partiallyPaidTotalLSM'] =  count($labIds[57]['partiallyPaidTotal']);
		$data['partiallyPaidTotalDSL'] =  count($labIds[55]['partiallyPaidTotal']);
		$data['partiallyPaidTotalSum'] =  number_format(array_sum($partiallyPaidTotal), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFMI'] =  number_format(array_sum($labIds[55]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFHI'] =  number_format(array_sum($labIds[54]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLSM'] =  number_format(array_sum($labIds[57]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumDSL'] =  number_format(array_sum($labIds[55]['partiallyPaidTotalSum']), 2, ',', ' ');


		$totalPriceByYear = 0;
		$setOplataByYear = 0;
		$partiallyPaidTotalByYear = [];

		$requestArrYear = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID, b.PRICE  FROM `ba_tz` b 
									WHERE year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.STAGE_ID NOT IN ('LOSE','5','6','7','8','9','10','11','12')
									AND b.TYPE_ID = 'SALE'");
		$requestListYear = [];
		while ($requestByYear = $requestArrYear->Fetch()) {
			$requestListYear[] = $requestByYear['ID_Z'];
		}
		$requestStrByYear = implode(',', $requestListYear);

		$assToGostYear = $this->DB->Query("SELECT gtp.price, bg.LFHI, bg.LFMI, bg.LSM, bg.DSL, b.PRICE, b.OPLATA, b.ID
									FROM `ba_tz` b
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									INNER JOIN `ba_gost` bg ON gtp.gost_method = bg.ID
									WHERE b.ID_Z IN ({$requestStrByYear})");

		while ($reqByLabByYear = $assToGostYear->Fetch()) {

			$data['labs'][] = $reqByLabByYear;
			if ($reqByLabByYear['LFHI'] == 1) {
				$labIdsByYear[54]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[54]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[54]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[54]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[54]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[54]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			} elseif ($reqByLabByYear['LFMI'] == 1) {
				$labIdsByYear[56]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[56]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[56]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[56]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[56]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[56]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			} elseif ($reqByLabByYear['LSM'] == 1) {
				$labIdsByYear[57]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[57]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[57]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[57]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] > 0 && $reqByLabByYear['OPLATA'] < $reqByLabByYear['PRICE']) {
					$labIdsByYear[57]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[57]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			}elseif ($reqByLabByYear['DSL'] == 1) {
				$labIdsByYear[55]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[55]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[55]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[55]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[55]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[55]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			}
		}

		foreach ($oplataByYear as $k => $val) {
			$setOplataByYear += $val['oplata'];
			if ($val['oplata'] == 0) {
				$unOplataByYear[] = $val['price'] - $val['oplata'];
				$unOplataID[] = [
					'id' =>$k,
					'price' =>$val['price']
				];
			} elseif ($val['oplata'] >= $val['price']) {
				$paidTotalByYear[] = $val['price'];
				$paidTotalID[] = [
					'id' =>$k
				];
			} else {
//				$partiallyPaidTotalByYear[$k] = $val['oplata'];
			}
		}
		$data['partiallyPaidTotalByYear111'] =$partiallyPaidTotalByYear;
		$data['totalByYear'] =  number_format($totalPriceByYear, 2, ',', ' ');
		$data['totalLFMIByYear'] =  number_format(array_sum($labIdsByYear[56]['totalPriceByYear']), 2, ',', ' ');
		$data['totalLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['totalPriceByYear']), 2, ',', ' ');
		$data['totalLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['totalPriceByYear']), 2, ',', ' ');
		$data['totalDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['totalPriceByYear']), 2, ',', ' ');
		$data['notPaidTotalByYear'] =  count($unOplataByYear);
		$data['notPaidTotalLFMIByYear'] =  count($labIdsByYear[56]['notPaidTotalByYear']);
		$data['notPaidTotalLFHIByYear'] =  count($labIdsByYear[54]['notPaidTotalByYear']);
		$data['notPaidTotalLSMByYear'] =  count($labIdsByYear[57]['notPaidTotalByYear']);
		$data['notPaidTotalDSLByYear'] =  count($labIdsByYear[55]['notPaidTotalByYear']);
		$data['notPaidSumTotalByYear'] =  number_format($totalPriceByYear - array_sum($paidTotalByYear), 2, ',', ' ');
		$data['notPaidSumTotalLFMIByYear'] =  number_format(array_sum($labIdsByYear[56]['totalPriceByYear']) - array_sum($labIdsByYear[56]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['totalPriceByYear']) - array_sum($labIdsByYear[54]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['totalPriceByYear']) - array_sum($labIdsByYear[57]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['totalPriceByYear']) - array_sum($labIdsByYear[55]['paidTotalByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalByYear'] =  count($partiallyPaidTotalByYear);
		$data['partiallyPaidTotalLFMIByYear'] =  count($labIdsByYear[56]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalLFHIByYear'] =  count($labIdsByYear[54]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalLSMByYear'] =  count($labIdsByYear[57]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalDSLByYear'] =  count($labIdsByYear[55]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalSumByYear'] =  number_format(array_sum($partiallyPaidTotalByYear), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFMIByYear'] =  number_format(array_sum($labIdsByYear[55]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['partiallyPaidTotalSumByYear']), 2, ',', ' ');

		return $data;

	}
	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticFinanceByMonthNew($monthReport)
	{
		$userModel = new User();
		$requestModel = new Request();
		$companyModel = new Company();

		$month = date('m', strtotime($monthReport));
		$year = 2023;//date('Y', strtotime($monthReport));

		$data = [];
		$requestList = [];
		$totalPrice = 0;
		$oplata = [];
		$setOplata = 0;
		$unOplata = [];
		$paid = [];
		$paidTotal = [];
		$paidTotalByYear = [];
		$labIds[54] = [];
		$labIds[55] = [];
		$labIds[56] = [];
		$labIds[57] = [];
		$loseRequest = [
			'LOSE',
			'5',
			'6',
			'7',
			'8',
			'9',
			'10',
			'11',
			'12',
			'EXECUTING'
		];

		$labIdsByYear[54] = [];
		$labIdsByYear[55] = [];
		$labIdsByYear[56] = [];
		$labIdsByYear[57] = [];

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID, b.PRICE  FROM `ba_tz` b 
									WHERE month(b.DATE_CREATE_TIMESTAMP) = {$month} 
									AND year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.STAGE_ID NOT IN ('LOSE','5','6','7','8','9','10','11','12')
									AND b.TYPE_ID = 'SALE'");

		while ($request = $requestArr->Fetch()) {
				$requestList[] = $request['ID_Z'];
		}
		$requestStr = implode(',', $requestList);

		$assToGost = $this->DB->Query("SELECT gtp.price, bg.LFHI, bg.LFMI, bg.LSM, bg.DSL, b.PRICE, b.OPLATA, b.ID
									FROM `ba_tz` b
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									INNER JOIN `ba_gost` bg ON gtp.gost_method = bg.ID
									WHERE b.ID_Z IN ({$requestStr})");

		while ($reqByLab = $assToGost->Fetch()) {

			$data['labs'][] = $reqByLab;
			if ($reqByLab['LFHI'] == 1) {
				$labIds[54]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[54]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[54]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[54]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[54]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[54]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			} elseif ($reqByLab['LFMI'] == 1) {
				$labIds[56]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[56]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[56]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[56]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[56]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[56]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			} elseif ($reqByLab['LSM'] == 1) {
				$labIds[57]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[57]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[57]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[57]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[57]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[57]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			}elseif ($reqByLab['DSL'] == 1) {
				$labIds[55]['totalPrice'][] = $reqByLab['price'];
				$totalPrice += $reqByLab['price'];
				if ($reqByLab['OPLATA'] >= $reqByLab['PRICE']) {
					$labIds[55]['paidTotal'][] = $reqByLab['price'];
				} elseif ($reqByLab['OPLATA'] == 0) {
					$labIds[55]['notPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[55]['notPaidTotalSum'][] = $reqByLab['price'];
				} else {
					$labIds[55]['partiallyPaidTotal'][$reqByLab['ID']] = $reqByLab['price'];
					$labIds[55]['partiallyPaidTotalSum'][] = $reqByLab['price'];
				}
				$oplata[$reqByLab['ID']] = [
					'price' => $reqByLab['PRICE'],
					'oplata' => $reqByLab['OPLATA']
				];
				continue;
			}
		}

		foreach ($oplata as $k => $val) {
			$setOplata += $val['oplata'];
			if ($val['oplata'] == 0) {
				$unOplata[] = $val['price'] - $val['oplata'];
				$unOplataID[] = [
					'id' =>$k,
					'price' =>$val['price']
					];
			} elseif ($val['oplata'] >= $val['price']) {
				$paidTotal[] = $val['price'];
				$paidTotalID[] = [
					'id' =>$k
				];
			} else {
				$partiallyPaidTotal[] = $val['oplata'];
			}
		}

		$data['test'] = $requestList;
		$data['total'] =  number_format($totalPrice, 2, ',', ' ');
		$data['totalLFMI'] =  number_format(array_sum($labIds[56]['totalPrice']), 2, ',', ' ');
		$data['totalLFHI'] =  number_format(array_sum($labIds[54]['totalPrice']), 2, ',', ' ');
		$data['totalLSM'] =  number_format(array_sum($labIds[57]['totalPrice']), 2, ',', ' ');
		$data['totalDSL'] =  number_format(array_sum($labIds[55]['totalPrice']), 2, ',', ' ');
		$data['notPaidTotal'] =  count($unOplata);
		$data['notPaidTotalLFMI'] =  count($labIds[56]['notPaidTotal']);
		$data['notPaidTotalLFHI'] =  count($labIds[54]['notPaidTotal']);
		$data['notPaidTotalLSM'] =  count($labIds[57]['notPaidTotal']);
		$data['notPaidTotalDSL'] =  count($labIds[55]['notPaidTotal']);
		$data['paidTotal'] =  number_format(array_sum($paidTotal), 2, ',', ' ');
		$data['paidTotalLFMI'] =  number_format(array_sum($labIds[56]['paidTotal']), 2, ',', ' ');
		$data['paidTotalLFHI'] =  number_format(array_sum($labIds[54]['paidTotal']), 2, ',', ' ');
		$data['paidTotalDSL'] =  number_format(array_sum($labIds[55]['paidTotal']), 2, ',', ' ');
		$data['paidTotalLSM'] =  number_format(array_sum($labIds[57]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotal'] =  number_format($totalPrice - array_sum($paidTotal), 2, ',', ' ');
		$data['notPaidSumTotalLFMI'] =  number_format(array_sum($labIds[56]['totalPrice']) - array_sum($labIds[56]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalLFHI'] =  number_format(array_sum($labIds[54]['totalPrice']) - array_sum($labIds[54]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalLSM'] =  number_format(array_sum($labIds[57]['totalPrice']) - array_sum($labIds[57]['paidTotal']), 2, ',', ' ');
		$data['notPaidSumTotalDSL'] =  number_format(array_sum($labIds[55]['totalPrice']) - array_sum($labIds[55]['paidTotal']), 2, ',', ' ');
		$data['partiallyPaidTotal'] =  count($partiallyPaidTotal);
		$data['partiallyPaidTotalLFMI'] =  count($labIds[56]['partiallyPaidTotal']);
		$data['partiallyPaidTotalLFHI'] =  count($labIds[54]['partiallyPaidTotal']);
		$data['partiallyPaidTotalLSM'] =  count($labIds[57]['partiallyPaidTotal']);
		$data['partiallyPaidTotalDSL'] =  count($labIds[55]['partiallyPaidTotal']);
		$data['partiallyPaidTotalSum'] =  number_format(array_sum($partiallyPaidTotal), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFMI'] =  number_format(array_sum($labIds[55]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFHI'] =  number_format(array_sum($labIds[54]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLSM'] =  number_format(array_sum($labIds[57]['partiallyPaidTotalSum']), 2, ',', ' ');
		$data['partiallyPaidTotalSumDSL'] =  number_format(array_sum($labIds[55]['partiallyPaidTotalSum']), 2, ',', ' ');


		$totalPriceByYear = 0;
		$setOplataByYear = 0;
		$partiallyPaidTotalByYear = [];

		$requestArrYear = $this->DB->Query("SELECT b.ID_Z, b.LABA_ID, b.COMPANY_ID, b.PRICE  FROM `ba_tz` b 
									WHERE year(b.DATE_CREATE_TIMESTAMP) = {$year}
									AND b.STAGE_ID NOT IN ('LOSE','5','6','7','8','9','10','11','12')
									AND b.TYPE_ID = 'SALE'");
		$requestListYear = [];
		while ($requestByYear = $requestArrYear->Fetch()) {
			$requestListYear[] = $requestByYear['ID_Z'];
		}
		$requestStrByYear = implode(',', $requestListYear);

		$assToGostYear = $this->DB->Query("SELECT gtp.price, bg.LFHI, bg.LFMI, bg.LSM, bg.DSL, b.PRICE, b.OPLATA, b.ID
									FROM `ba_tz` b
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									INNER JOIN `ba_gost` bg ON gtp.gost_method = bg.ID
									WHERE b.ID_Z IN ({$requestStrByYear})");

		while ($reqByLabByYear = $assToGostYear->Fetch()) {

			$data['labs'][] = $reqByLabByYear;
			if ($reqByLabByYear['LFHI'] == 1) {
				$labIdsByYear[54]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[54]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[54]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[54]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[54]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[54]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			} elseif ($reqByLabByYear['LFMI'] == 1) {
				$labIdsByYear[56]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[56]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[56]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[56]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[56]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[56]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			} elseif ($reqByLabByYear['LSM'] == 1) {
				$labIdsByYear[57]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[57]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[57]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[57]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] > 0 && $reqByLabByYear['OPLATA'] < $reqByLabByYear['PRICE']) {
					$labIdsByYear[57]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[57]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			}elseif ($reqByLabByYear['DSL'] == 1) {
				$labIdsByYear[55]['totalPriceByYear'][] = $reqByLabByYear['price'];
				$totalPriceByYear += $reqByLabByYear['price'];
				if ($reqByLabByYear['OPLATA'] >= $reqByLabByYear['PRICE']) {
					$labIdsByYear[55]['paidTotalByYear'][] = $reqByLabByYear['price'];
				} elseif ($reqByLabByYear['OPLATA'] == 0) {
					$labIdsByYear[55]['notPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['price'];
					$labIdsByYear[55]['notPaidTotalSumByYear'][] = $reqByLabByYear['price'];
				} else {
					$labIdsByYear[55]['partiallyPaidTotalByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
					$labIdsByYear[55]['partiallyPaidTotalSumByYear'][$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
                    $partiallyPaidTotalByYear[$reqByLabByYear['ID']] = $reqByLabByYear['OPLATA'];
				}
				$oplataByYear[$reqByLabByYear['ID']] = [
					'price' => $reqByLabByYear['PRICE'],
					'oplata' => $reqByLabByYear['OPLATA']
				];
				continue;
			}
		}

		foreach ($oplataByYear as $k => $val) {
			$setOplataByYear += $val['oplata'];
			if ($val['oplata'] == 0) {
				$unOplataByYear[] = $val['price'] - $val['oplata'];
				$unOplataID[] = [
					'id' =>$k,
					'price' =>$val['price']
				];
			} elseif ($val['oplata'] >= $val['price']) {
				$paidTotalByYear[] = $val['price'];
				$paidTotalID[] = [
					'id' =>$k
				];
			} else {
//				$partiallyPaidTotalByYear[$k] = $val['oplata'];
			}
		}
		$data['partiallyPaidTotalByYear111'] =$partiallyPaidTotalByYear;
		$data['totalByYear'] =  number_format($totalPriceByYear, 2, ',', ' ');
		$data['totalLFMIByYear'] =  number_format(array_sum($labIdsByYear[56]['totalPriceByYear']), 2, ',', ' ');
		$data['totalLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['totalPriceByYear']), 2, ',', ' ');
		$data['totalLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['totalPriceByYear']), 2, ',', ' ');
		$data['totalDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['totalPriceByYear']), 2, ',', ' ');
		$data['notPaidTotalByYear'] =  count($unOplataByYear);
		$data['notPaidTotalLFMIByYear'] =  count($labIdsByYear[56]['notPaidTotalByYear']);
		$data['notPaidTotalLFHIByYear'] =  count($labIdsByYear[54]['notPaidTotalByYear']);
		$data['notPaidTotalLSMByYear'] =  count($labIdsByYear[57]['notPaidTotalByYear']);
		$data['notPaidTotalDSLByYear'] =  count($labIdsByYear[55]['notPaidTotalByYear']);
		$data['notPaidSumTotalByYear'] =  number_format($totalPriceByYear - array_sum($paidTotalByYear), 2, ',', ' ');
		$data['notPaidSumTotalLFMIByYear'] =  number_format(array_sum($labIdsByYear[56]['totalPriceByYear']) - array_sum($labIdsByYear[56]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['totalPriceByYear']) - array_sum($labIdsByYear[54]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['totalPriceByYear']) - array_sum($labIdsByYear[57]['paidTotalByYear']), 2, ',', ' ');
		$data['notPaidSumTotalDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['totalPriceByYear']) - array_sum($labIdsByYear[55]['paidTotalByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalByYear'] =  count($partiallyPaidTotalByYear);
		$data['partiallyPaidTotalLFMIByYear'] =  count($labIdsByYear[56]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalLFHIByYear'] =  count($labIdsByYear[54]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalLSMByYear'] =  count($labIdsByYear[57]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalDSLByYear'] =  count($labIdsByYear[55]['partiallyPaidTotalByYear']);
		$data['partiallyPaidTotalSumByYear'] =  number_format(array_sum($partiallyPaidTotalByYear), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFMIByYear'] =  number_format(array_sum($labIdsByYear[55]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLFHIByYear'] =  number_format(array_sum($labIdsByYear[54]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumLSMByYear'] =  number_format(array_sum($labIdsByYear[57]['partiallyPaidTotalSumByYear']), 2, ',', ' ');
		$data['partiallyPaidTotalSumDSLByYear'] =  number_format(array_sum($labIdsByYear[55]['partiallyPaidTotalSumByYear']), 2, ',', ' ');

		return $data;

	}

	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticByYear($monthReport)
	{
		$data = [];
		$year = 2023;//date('Y', strtotime($monthReport));

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.REQUEST_TITLE FROM `ba_tz` b 
									WHERE year(b.DATE_CREATE_TIMESTAMP) = {$year} order by b.ID_Z desc")->Fetch();

		$fullYearRequest1 = explode('№', $requestArr['REQUEST_TITLE'])[1];

		$data['fullYearRequest'] = explode('/', $fullYearRequest1)[0];

		$contractArr = $this->DB->Query("SELECT * FROM DOGOVOR WHERE year(DATE) = {$year}");

		$contract = [];
		$contractLongterm = [];

		while ($contractAll = $contractArr->Fetch()) {
			$contract[] = $contractAll;

			if ($contractAll['LONGTERM'] == 1) {
				$contractLongterm[] = $contractAll;
			}
		}

		$data['fullYearContract'] = count($contract);
		$data['fullYearContractLongterm'] = count($contractLongterm);

		$assToGost = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, gtp.`price`, p.NUMBER 
									FROM `PROTOCOLS` p
									INNER JOIN `ba_tz` b ON p.`ID_TZ` = b.`ID`
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									WHERE year(p.DATE) = {$year} AND p.NUMBER is not NULL group by gtp.id");

		$tests = [];
		$prots = [];
		$probe = [];

		while ($gost = $assToGost->Fetch()) {
			$tests[] = $gost;
		}

		$data['fullYearTests'] = count($tests);

		$protocolArr = $this->DB->Query("SELECT * FROM PROTOCOLS WHERE year(DATE) = {$year} AND `NUMBER` is not null");

		while ($protocols = $protocolArr->Fetch()) {
			$prots[] = $protocols;
		}

		$data['fullYearProtocols'] = count($prots) -1;

		$probeArr = $this->DB->Query("SELECT * FROM ACT_BASE ab, MATERIALS_TO_REQUESTS mtr, probe_to_materials ptm  WHERE ab.ID_Z = mtr.ID_DEAL AND mtr.ID = ptm.material_request_id AND year(ab.ACT_DATE) = {$year} AND ab.`ACT_NUM` is not null");

		while ($probes = $probeArr->Fetch()) {
			$probe[] = $probes;
		}

		$data['fullYearProbe'] = count($probe);

		return $data;

	}
	/**
	 * @param $monthReport
	 * @return array
	 */
	public function getStatisticByYearNew($monthReport)
	{
		$data = [];
		$year = 2023;//date('Y', strtotime($monthReport));

		$requestArr = $this->DB->Query("SELECT b.ID_Z, b.REQUEST_TITLE FROM `ba_tz` b 
									WHERE year(b.DATE_CREATE_TIMESTAMP) = {$year} order by b.ID_Z desc")->Fetch();

		$fullYearRequest1 = explode('№', $requestArr['REQUEST_TITLE'])[1];

		$data['fullYearRequest'] = explode('/', $fullYearRequest1)[0];

		$contractArr = $this->DB->Query("SELECT * FROM DOGOVOR WHERE year(DATE) = {$year}");

		$contract = [];
		$contractLongterm = [];

		while ($contractAll = $contractArr->Fetch()) {
			$contract[] = $contractAll;

			if ($contractAll['LONGTERM'] == 1) {
				$contractLongterm[] = $contractAll;
			}
		}

		$data['fullYearContract'] = count($contract);
		$data['fullYearContractLongterm'] = count($contractLongterm);

		$assToGost = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, gtp.`price`, p.NUMBER 
									FROM `PROTOCOLS` p
									INNER JOIN `ba_tz` b ON p.`ID_TZ` = b.`ID`
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON b.`ID_Z` = mtr.`ID_DEAL`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									WHERE year(p.DATE) = {$year} AND p.NUMBER is not NULL group by gtp.id");

		$tests = [];
		$prots = [];
		$probe = [];

		while ($gost = $assToGost->Fetch()) {
			$tests[] = $gost;
		}

		$data['fullYearTests'] = count($tests);

		$protocolArr = $this->DB->Query("SELECT * FROM PROTOCOLS WHERE year(DATE) = {$year} AND `NUMBER` is not null");

		while ($protocols = $protocolArr->Fetch()) {
			$prots[] = $protocols;
		}

		$data['fullYearProtocols'] = count($prots) -1;

		$probeArr = $this->DB->Query("SELECT * FROM ACT_BASE ab, MATERIALS_TO_REQUESTS mtr, probe_to_materials ptm  WHERE ab.ID_Z = mtr.ID_DEAL AND mtr.ID = ptm.material_request_id AND year(ab.ACT_DATE) = {$year} AND ab.`ACT_NUM` is not null");

		while ($probes = $probeArr->Fetch()) {
			$probe[] = $probes;
		}

		$data['fullYearProbe'] = count($probe);

		return $data;

	}


    /**
     * @param $arr
     * @return array
     */
    protected function protDop($arr)
	{
        $labDop[57] = 0;
        $labDop[54] = 0;
        $labDop[56] = 0;
        $labDop[55] = 0;
        $labDop[58] = 0;

        foreach ($arr as $lab) {
            foreach ($lab as $item) {
                $labAll[] = $item;
                if (count($lab) > 1) {
                    $labDop[$item]++;
                }
            }
        }

        return $labDop;
    }

    protected function countUniq($arr)
	{
		foreach ($arr as $key => $item) {
			if ($item >= 20) {
				$massive[$key] = $item;
			}
		}

		return count($massive);
	}

	public function getStatisticStaffByMonthForChart($monthReport)
    {
        $user = new User();

        $month = date('m', strtotime($monthReport));
        $year = 2023;//date('Y',  strtotime($monthReport));

		$userArr = $user->getUserByDepartment();

		$userByLab = [];
        $assToGost = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`
									FROM `PROTOCOLS` p
									INNER JOIN `ulab_material_to_request` umtr ON p.ID = umtr.protocol_id
									INNER JOIN `ulab_gost_to_probe` ugtp ON umtr.id = ugtp.`material_to_request_id`
									INNER JOIN `MATERIALS_TO_REQUESTS` mtr ON umtr.`mtr_id` = mtr.`ID`
									INNER JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									INNER JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									WHERE month(p.DATE) = {$month} AND year(p.DATE) = {$year} AND p.NUMBER is not NULL
									group by gtp.id");

        while ($idAssToGost = $assToGost->Fetch()) {
			$gostList[$idAssToGost['assigned']][] = $idAssToGost['gost_method'];
        }

        $assToGostInWork = $this->DB->Query("SELECT gtp.`gost_method`, gtp.`assigned`, b.`ID_Z`,b.ID, b.`REQUEST_TITLE`, ugtp.id 
                                    FROM `ba_tz` b 
									LEFT JOIN `ulab_material_to_request` umtp ON umtp.`deal_id` = b.`ID_Z`
									LEFT JOIN `ulab_gost_to_probe` ugtp ON umtp.`id` = ugtp.`material_to_request_id` 
									LEFT JOIN `MATERIALS_TO_REQUESTS` mtr ON umtp.`mtr_id` = mtr.`ID`
									LEFT JOIN `probe_to_materials` ptm ON mtr.`ID` = ptm.`material_request_id`
									LEFT JOIN `gost_to_probe` gtp ON ptm.`id` = gtp.`probe_id`
									LEFT JOIN PROTOCOLS p ON umtp.`protocol_id` = p.`ID`
									LEFT JOIN `b_uts_user` u2d ON gtp.`assigned` = u2d.`VALUE_ID`
									WHERE year(b.DATE_CREATE_TIMESTAMP) <= {$year} AND month(b.DATE_CREATE_TIMESTAMP) <= {$month}
									and gtp.`assigned` != 0 and gtp.`assigned` is not null and umtp.`protocol_id` = ''
									and ACT_NUM is not null and b.STAGE_ID != 'LOSE' and b.STAGE_ID < 5 and (b.PRICE - b.OPLATA = 0) group by gtp.id 
									order by u2d.UF_DEPARTMENT, gtp.`assigned`");


        while ($row = $assToGostInWork->Fetch()) {
			$testInProgress[$row['assigned']][] = $idAssToGost['gost_method'];
        }

		foreach ($userArr as $key => $item) {
			if ($key == 58) {
				continue;
			}

			foreach ($item as $k => $val) {
				$tbu = count($gostList[$k]);
				$tip = count($testInProgress[$k]);
				$userByLab[$k] = [
					'won' => $tbu,
					'progress' => $tip,
					'id' => $val['user_id'],
					'short_name' => $val['short_name'],
				];
			}
		}


		$userByLab = array_values($userByLab);
        return $userByLab;
    }


    /**
     * журнал отчет лаба метод кол-во стоимость. завершенных работ
     * @param array $filter
     * @return array
     */
    public function getJournalReportMethodList($filter = [])
    {
        $labModel = new Lab();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "p.DATE between '{$filter['search']['dateStart']}' AND '{$filter['search']['dateEnd']}' AND ";
                }
                // Определяемая характеристика / показатель
                if ( isset($filter['search']['name']) ) {
                    $where .= "m.name LIKE '%{$filter['search']['name']}%' AND ";
                }
                if ( isset($filter['search']['gost']) ) {
                    $where .= "g.reg_doc LIKE '%{$filter['search']['gost']}%' AND ";
                }
                // Контрагент
                if ( isset($filter['search']['TITLE']) ) {
                    $where .= "com.TITLE LIKE '%{$filter['search']['TITLE']}%' AND ";
                }
                if ( isset($filter['search']['REQUEST_TITLE']) ) {
                    $where .= "tz.REQUEST_TITLE LIKE '%{$filter['search']['REQUEST_TITLE']}%' AND ";
                }
                if ( isset($filter['search']['NUMBER_AND_YEAR']) ) {
                    $where .= "p.NUMBER_AND_YEAR LIKE '%{$filter['search']['NUMBER_AND_YEAR']}%' AND ";
                }
                // Лаба
                if ( isset($filter['search']['lab']) ) {
                    $where .= "l.`lab_id` = {$filter['search']['lab']} AND ";
                }

                // Статус
//                if ( isset($filter['search']['stage']) ) {
//                    if ( $filter['search']['stage'] == 1 ) { // Актуальные
//                        $where .= "m.is_actual = 1 AND ";
//                    }
//                    if ( $filter['search']['stage'] == 2 ) { // В ОА
//                        $where .= "m.in_field = 1 AND m.is_extended_field = 0 AND m.is_actual = 1 AND ";
//                    }
//                    if ( $filter['search']['stage'] == 3 ) { // РОА
//                        $where .= "m.is_extended_field = 1 AND m.is_actual = 1 AND ";
//                    }
//                    if ( $filter['search']['stage'] == 5 ) { // Вне ОА
//                        $where .= "m.in_field = 0 AND m.is_actual = 1 AND ";
//                    }
//                    if ( $filter['search']['stage'] == 7 ) { // Не актуальные
//                        $where .= "m.is_actual = 0 AND ";
//                    }
//                } else {
//                    $where .= "m.is_actual = 1 AND ";
//                }

            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'name':
                    $order['by'] = 'm.name';
                    break;
                case 'REQUEST_TITLE':
                    $order['by'] = 'tz.REQUEST_TITLE';
                    break;
                case 'NUMBER_AND_YEAR':
                    $order['by'] = 'p.NUMBER_AND_YEAR';
                    break;
                case 'DATE_END':
                    $order['by'] = 'p.DATE_END';
                    break;
                case 'DATE_BEGIN':
                    $order['by'] = 'p.DATE_BEGIN';
                    break;
                case 'short_name':
                    $order['by'] = 'l.ID';
                    break;
                case 'count_method':
                    $order['by'] = 'count(m.id)';
                    break;
                default:
                    $order['by'] = 'm.num_oa';
            }
        }

        // работа с пагинацией
        if (isset($filter['paginate'])) {
            $offset = 0;
            // количество строк на страницу
            if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                $length = $filter['paginate']['length'];

                if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                    $offset = $filter['paginate']['start'];
                }
                $limit = "LIMIT {$offset}, {$length}";
            }
        }

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT     tz.ID_Z, tz.REQUEST_TITLE, 
                        g.id, g.reg_doc, m.name, m.id as method_id, m.clause, m.duration_work,  
                        mp.fsa_id mp_fsa_id, mp.name as mp_name,
                        com.TITLE, p.DATE_BEGIN, p.DATE_END, p.NUMBER_AND_YEAR, 
                        count(m.id) as count_method, gtp.price, lab.short_name
                    FROM ba_tz as tz
                    inner join ulab_material_to_request as mater on tz.ID_Z = mater.deal_id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mater.id
                    inner JOIN ulab_methods as m ON m.id = gtp.new_method_id 
                    inner JOIN ulab_gost as g ON g.id = m.gost_id 
                    left JOIN ulab_measured_properties as mp ON mp.id = m.measured_properties_id 
                    left JOIN ulab_methods_lab as l ON l.method_id = m.id
                    left JOIN ba_laba as lab ON l.lab_id = lab.ID
                    inner join PROTOCOLS as p on mater.protocol_id = p.ID
                    inner JOIN b_crm_company com ON com.ID = tz.COMPANY_ID
                    WHERE p.NUMBER is not null and {$where}
                    group by p.ID, m.id
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );


        $dataTotal = $this->DB->Query(
            "SELECT 
                        tz.ID
                    FROM ba_tz as tz
                    inner join ulab_material_to_request as mater on tz.ID_Z = mater.deal_id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mater.id
                    inner JOIN ulab_methods as m ON m.id = gtp.new_method_id 
                    inner JOIN ulab_gost as g ON g.id = m.gost_id   
                    inner JOIN ulab_measured_properties as mp ON mp.id = m.measured_properties_id 
                    inner JOIN ulab_methods_lab as l ON l.method_id = m.id
                    inner JOIN ba_laba as lab ON l.lab_id = lab.ID
                    inner join PROTOCOLS as p on mater.protocol_id = p.ID
                    inner JOIN b_crm_company com ON com.ID = tz.COMPANY_ID
                    inner JOIN ACT_BASE act ON act.ID_TZ = tz.ID                   
                    WHERE p.NUMBER is not null 
                    group by p.ID, m.id"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT 
                        tz.ID
                    FROM ba_tz as tz
                    inner join ulab_material_to_request as mater on tz.ID_Z = mater.deal_id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mater.id
                    inner JOIN ulab_methods as m ON m.id = gtp.new_method_id
                    inner JOIN ulab_gost as g ON g.id = m.gost_id
                    inner JOIN ulab_measured_properties as mp ON mp.id = m.measured_properties_id 
                    inner JOIN ulab_methods_lab as l ON l.method_id = m.id
                    inner JOIN ba_laba as lab ON l.lab_id = lab.ID
                    inner join PROTOCOLS as p on mater.protocol_id = p.ID
                    inner JOIN b_crm_company com ON com.ID = tz.COMPANY_ID
                    inner JOIN ACT_BASE act ON act.ID_TZ = tz.ID     
                    WHERE p.NUMBER is not null and {$where}
                    group by p.ID, m.id"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $row['total'] = $row['price'] * $row['count_method'];

            $row['DATE_END'] = StringHelper::dateRu($row['DATE_END']);
            $row['DATE_BEGIN'] = StringHelper::dateRu($row['DATE_BEGIN']);

//            $labInfo = $labModel->get($row['lab_id']);
//            $row['lab_short'] = $labInfo['short_name']?? '';


            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * журнал отчет оборудование
     * @param array $filter
     * @return array
     */
    public function getJournalReportOborudList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        $identFilter = [
            'SI' => 'СИ',
            'IO' => 'ИО',
            'VO' => 'ВО',
            'TS' => 'ТС',
            'SO' => 'CO',
            'REACT' => 'Реактивы',
            'OOPP' => 'Оборудование для отбора/подготовки проб',
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "p.DATE between '{$filter['search']['dateStart']}' AND '{$filter['search']['dateEnd']}' AND ";
                }
                if ( isset($filter['search']['OBJECT']) ) {
                    $where .= "bo.OBJECT LIKE '%{$filter['search']['OBJECT']}%' AND ";
                }
                if ( isset($filter['search']['REG_NUM']) ) {
                    $where .= "bo.REG_NUM LIKE '%{$filter['search']['REG_NUM']}%' AND ";
                }
                if ( isset($filter['search']['IDENT']) ) {
                    $where .= "bo.IDENT LIKE '{$filter['search']['IDENT']}' AND ";
                }
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'OBJECT':
                    $order['by'] = 'bo.OBJECT';
                    break;
                case 'REG_NUM':
                    $order['by'] = 'bo.REG_NUM';
                    break;
                case 'count_oborud':
                    $order['by'] = 'count(bo.ID)';
                    break;
                default:
                    $order['by'] = 'bo.ID';
            }
        }

        // работа с пагинацией
        if (isset($filter['paginate'])) {
            $offset = 0;
            // количество строк на страницу
            if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                $length = $filter['paginate']['length'];

                if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                    $offset = $filter['paginate']['start'];
                }
                $limit = "LIMIT {$offset}, {$length}";
            }
        }

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT bo.ID, bo.OBJECT, bo.REG_NUM, bo.IDENT, count(bo.ID) as count_oborud
                    FROM TZ_OB_CONNECT as toc
                    left join PROTOCOLS p ON p.ID = toc.PROTOCOL_ID 
                    inner join ba_oborud bo on toc.ID_OB = bo.ID
                    WHERE p.NUMBER is not NULL and {$where}
                    group by bo.ID
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );


        $dataTotal = $this->DB->Query(
            "SELECT *
                    FROM TZ_OB_CONNECT as toc
                    left join PROTOCOLS p ON p.ID = toc.PROTOCOL_ID 
                    inner join ba_oborud bo on toc.ID_OB = bo.ID
                    WHERE p.NUMBER is not NULL and 1
                    group by bo.ID"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT *
                    FROM TZ_OB_CONNECT as toc
                    left join PROTOCOLS p ON p.ID = toc.PROTOCOL_ID 
                    inner join ba_oborud bo on toc.ID_OB = bo.ID
                    WHERE p.NUMBER is not NULL and {$where}
                    group by bo.ID"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $row['IDENT'] = $identFilter[$row['IDENT']];

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

	public function newFuncTest()
	{
		$res = $this->DB->Query("select umtr.deal_id, umtr.id, umtr.quarry_id, m.ID, m.NAME, c.TITLE, group_concat(ugtp.method_id) as meth 
from ulab_material_to_request as umtr
inner join b_crm_deal as d on d.ID = umtr.deal_id
inner join b_crm_company as c on c.ID = d.COMPANY_ID
left join ulab_gost_to_probe as ugtp on umtr.id = ugtp.material_to_request_id
inner join MATERIALS as m on m.ID = umtr.material_id
where m.NAME like '%песок%' or m.NAME like '%щебень%'
group by umtr.id");
	}


    /**
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
	public function reportFromLims($dateStart, $dateEnd)
    {
        // поступило
        $sql = $this->DB->Query($this->getSqlReportFromLims(
            "tz.DATE_CREATE_TIMESTAMP >= '{$dateStart}' and ml.lab_id <> 6 and st.state is null and tz.ACT_NUM is not null and tz.TYPE_ID <> 7"
        ));;

        // в работе
        $sql2 = $this->DB->Query($this->getSqlReportFromLims(
            "ml.lab_id <> 6 and st.state <> 'complete' and st.state is not null and tz.ACT_NUM is not null and tz.TYPE_ID <> 7 and ugtp.id not in (select ugtp_id from ulab_start_trials where state = 'complete')"
        ));

        // завершено
        $sql3 = $this->DB->Query($this->getSqlReportFromLims(
            "tz.DATE_CREATE_TIMESTAMP >= '{$dateStart}' and ml.lab_id <> 6 and st.state = 'complete' and tz.TYPE_ID <> 7 and st.date <= '{$dateEnd}'"
        ));

        // опытный завод поступило
        $sql4 = $this->DB->Query($this->getSqlReportFromLims(
            "tz.DATE_CREATE_TIMESTAMP >= '{$dateStart}' and ml.lab_id <> 6 and st.state is null and tz.TYPE_ID = 7"
        ));

        // опытный завод завершено
        $sql5 = $this->DB->Query($this->getSqlReportFromLims(
            "tz.DATE_CREATE_TIMESTAMP >= '{$dateStart}' and ml.lab_id <> 6 and st.state = 'complete' and tz.TYPE_ID = 7 and st.date <= '{$dateEnd}'"
        ));

        $result = [];

        $result[1]['title'] = "Количество испытаний по единичным заявкам <b>ПОСТУПИЛО</b>";
        $result[4]['title'] = "Количество испытаний по комплексным заявкам <b>ПОСТУПИЛО</b>";
        $result[2]['title'] = "Количество испытаний по единичным заявкам <b>В РАБОТЕ</b>";
        $result[5]['title'] = "Количество испытаний по комплексным заявкам <b>В РАБОТЕ</b>";
        $result[3]['title'] = "Количество испытаний по единичным заявкам <b>ЗАВЕРШЕНО</b>";
        $result[6]['title'] = "Количество испытаний по комплексным заявкам <b>ЗАВЕРШЕНО</b>";
        $result[7]['title'] = "Количество испытаний для Опытного завода <b>ПОСТУПИЛО</b>";
        $result[8]['title'] = "Количество испытаний для Опытного завода <b>ЗАВЕРШЕНО</b>";

        for ($i = 1; $i <= 8; $i++) {
            $result[$i]['lfhi'] = 0;
            $result[$i]['lsm'] = 0;
            $result[$i]['lfmi'] = 0;
            $result[$i]['dsl'] = 0;
            $result[$i]['osk'] = 0;
            $result[$i]['smk'] = 0;
            $result[$i]['total'] = 0;
        }

        while ($row = $sql->Fetch()) {
            if ( $row['unic'] == 1 ) {
                $result[1]['lfhi'] += $row['lfhi'];
                $result[1]['lsm'] += $row['lsm'];
                $result[1]['lfmi'] += $row['lfmi'];
                $result[1]['dsl'] += $row['dsl'];
                $result[1]['osk'] += $row['osk'];
                $result[1]['smk'] += $row['smk'];
                $result[1]['total'] += $row['total'];
            } else {
                $result[4]['lfhi'] += $row['lfhi'];
                $result[4]['lsm'] += $row['lsm'];
                $result[4]['lfmi'] += $row['lfmi'];
                $result[4]['dsl'] += $row['dsl'];
                $result[4]['osk'] += $row['osk'];
                $result[4]['smk'] += $row['smk'];
                $result[4]['total'] += $row['total'];
            }
        }

        while ($row = $sql2->Fetch()) {
            if ( $row['unic'] == 1 ) {
                $result[2]['lfhi'] += $row['lfhi'];
                $result[2]['lsm'] += $row['lsm'];
                $result[2]['lfmi'] += $row['lfmi'];
                $result[2]['dsl'] += $row['dsl'];
                $result[2]['osk'] += $row['osk'];
                $result[2]['smk'] += $row['smk'];
                $result[2]['total'] += $row['total'];
            } else {
                $result[5]['lfhi'] += $row['lfhi'];
                $result[5]['lsm'] += $row['lsm'];
                $result[5]['lfmi'] += $row['lfmi'];
                $result[5]['dsl'] += $row['dsl'];
                $result[5]['osk'] += $row['osk'];
                $result[5]['smk'] += $row['smk'];
                $result[5]['total'] += $row['total'];
            }

            $maxDate = date('Y-m-d', max([strtotime($row['DATE_OPLATA']), strtotime($row['DATE_ACT'])]));

            $result[2]['tz'][] =
                [
                    'id_tz' => $row['ID_Z'],
                    'type_of_day' => $row['type_of_day'],
                    'date_max' => $maxDate,
                    'day_to_test' => $row['DAY_TO_TEST'],
                    'end_date' => date('Y-m-d', strtotime("{$maxDate} + {$row['DAY_TO_TEST']} days")),
                    'title' => $row['REQUEST_TITLE']
                ];
        }

        while ($row = $sql3->Fetch()) {
            if ( $row['unic'] == 1 ) {
                $result[3]['lfhi'] += $row['lfhi'];
                $result[3]['lsm'] += $row['lsm'];
                $result[3]['lfmi'] += $row['lfmi'];
                $result[3]['dsl'] += $row['dsl'];
                $result[3]['osk'] += $row['osk'];
                $result[3]['smk'] += $row['smk'];
                $result[3]['total'] += $row['total'];
            } else {
                $result[6]['lfhi'] += $row['lfhi'];
                $result[6]['lsm'] += $row['lsm'];
                $result[6]['lfmi'] += $row['lfmi'];
                $result[6]['dsl'] += $row['dsl'];
                $result[6]['osk'] += $row['osk'];
                $result[6]['smk'] += $row['smk'];
                $result[6]['total'] += $row['total'];
            }
        }

        while ($row = $sql4->Fetch()) {
            $result[7]['lfhi'] += $row['lfhi'];
            $result[7]['lsm'] += $row['lsm'];
            $result[7]['lfmi'] += $row['lfmi'];
            $result[7]['dsl'] += $row['dsl'];
            $result[7]['osk'] += $row['osk'];
            $result[7]['smk'] += $row['smk'];
            $result[7]['total'] += $row['total'];
        }

        while ($row = $sql5->Fetch()) {
            $result[8]['lfhi'] += $row['lfhi'];
            $result[8]['lsm'] += $row['lsm'];
            $result[8]['lfmi'] += $row['lfmi'];
            $result[8]['dsl'] += $row['dsl'];
            $result[8]['osk'] += $row['osk'];
            $result[8]['smk'] += $row['smk'];
            $result[8]['total'] += $row['total'];
        }

        ksort($result);

        return $result;
    }

	/**
	 * @param $dateStart
	 * @param $dateEnd
	 * @return array
	 */
	public function reportOZ($dateStart, $dateEnd)
	{

		$dateStart = "'{$dateStart} 00:00:00'";
		$dateEnd = "'{$dateEnd} 23:59:59'";

		$result = [];

		$res = $this->DB->Query("SELECT DISTINCT b.ID b_id, max(sh.status_id) as mStat, b.TZ, b.STAGE_ID, b.ID_Z, b.ACT_NUM, b.REQUEST_TITLE, b.TAKEN_SERT_ISP, b.RESULTS, b.TAKEN_ID_DEAL, b.TYPE_ID,
                        CONVERT(substring_index(substring_index(b.REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) request,
                        b.DATE_CREATE_TIMESTAMP, b.COMPANY_TITLE, b.DEADLINE,  b.DEADLINE_TABLE, b.ACCOUNT, b.MATERIAL,
                        b.ASSIGNED, b.NUM_ACT_TABLE, b.PRICE, b.OPLATA, b.DATE_OPLATA, b.PDF,
                        b.discount_type, b.DISCOUNT,
                        b.MANUFACTURER_TITLE, b.USER_HISTORY, b.LABA_ID, b.ACTUAL_VER b_actual_ver, c.leader, c.confirm,
                        count(c.id) c_count, count(c.date_return) с_date_return, k.ID k_id , d.IS_ACTION,  CONCAT(d.CONTRACT_TYPE, ' ', d.NUMBER, ' от ', DATE_FORMAT(d.DATE, '%d.%m.%Y')) as DOGOVOR_TABLE
                    FROM ba_tz b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID
                    LEFT JOIN CHECK_TZ c ON b.ID=c.tz_id
                    LEFT JOIN KP k ON b.ID=k.TZ_ID
                    LEFT JOIN PROTOCOLS p ON p.ID_TZ=b.ID
                    LEFT JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL=b.ID_Z
                    LEFT JOIN DOGOVOR d ON d.ID=dtc.ID_CONTRACT
                    LEFT JOIN AKT_VR act ON act.TZ_ID=b.ID
                    LEFT JOIN assigned_to_request ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN b_user usr ON ass.user_id = usr.ID
					left join tz_status_history sh ON b.ID_Z = sh.deal_id
                    WHERE b.TYPE_ID = '7' AND (b.DATE_CREATE_TIMESTAMP >= {$dateStart} AND b.DATE_CREATE_TIMESTAMP <= {$dateEnd}) AND 1
                    GROUP BY b.ID ORDER BY b.DATE_CREATE_TIMESTAMP DESC");

//		$res = $this->DB->Query("SELECT DISTINCT b.ID b_id, b.TZ, b.STAGE_ID, b.ID_Z, b.ACT_NUM, b.REQUEST_TITLE, b.TAKEN_SERT_ISP, b.RESULTS, b.TAKEN_ID_DEAL, b.TYPE_ID,
//                        CONVERT(substring_index(substring_index(b.REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) request,
//                        b.DATE_CREATE_TIMESTAMP, b.COMPANY_TITLE, b.DEADLINE,  b.DEADLINE_TABLE, b.ACCOUNT, b.MATERIAL,
//                        b.ASSIGNED, b.NUM_ACT_TABLE, b.PRICE, b.OPLATA, b.DATE_OPLATA, b.PDF,
//                        b.discount_type, b.DISCOUNT,
//                        b.MANUFACTURER_TITLE, b.USER_HISTORY, b.LABA_ID, b.ACTUAL_VER b_actual_ver, c.leader, c.confirm,
//                        count(c.id) c_count, count(c.date_return) с_date_return, k.ID k_id , d.IS_ACTION,  CONCAT(d.CONTRACT_TYPE, ' ', d.NUMBER, ' от ', DATE_FORMAT(d.DATE, '%d.%m.%Y')) as DOGOVOR_TABLE
//                    FROM ba_tz b
//                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID
//                    LEFT JOIN CHECK_TZ c ON b.ID=c.tz_id
//                    LEFT JOIN KP k ON b.ID=k.TZ_ID
//                    LEFT JOIN PROTOCOLS p ON p.ID_TZ=b.ID
//                    LEFT JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL=b.ID_Z
//                    LEFT JOIN DOGOVOR d ON d.ID=dtc.ID_CONTRACT
//                    LEFT JOIN AKT_VR act ON act.TZ_ID=b.ID
//                    LEFT JOIN assigned_to_request ass ON ass.deal_id = b.ID_Z
//                    LEFT JOIN b_user usr ON ass.user_id = usr.ID
//					left join tz_status_history sh ON b.ID_Z = sh.deal_id
//                    WHERE b.TYPE_ID != '3' AND (b.STAGE_ID not in ('LOSE',5,6,7,8,9,10,11,12) OR sh.status_id != 23)AND b.REQUEST_TITLE <> '' AND b.REQUEST_TITLE LIKE '%ПР%' AND (b.DATE_CREATE_TIMESTAMP >= {$dateStart} AND b.DATE_CREATE_TIMESTAMP <= {$dateEnd}) AND 1
//                    GROUP BY b.ID ORDER BY b.DATE_CREATE_TIMESTAMP DESC");

		$allSum = 0;
		$wonSum = 0;
		$aaa = [];
		while ($row = $res->Fetch()) {
			if ($row['mStat'] != 23 ) {
				$aaa[] = [$row['REQUEST_TITLE'], $row['PRICE']];
//				$aaa[] = $row['PRICE'];
				$allSum += $row['PRICE'];
			}
			if ($row['mStat'] == 22 ) {
				$wonSum += $row['PRICE'];
			}
//			if ($row['STAGE_ID'] == 2 || $row['STAGE_ID'] == 'WON') {
//				$wonSum += $row['PRICE'];
//			}
		}
		$result['allSUM'] = $allSum;
		$result['wonSum'] = $wonSum;

		return $result;
	}


    /**
     * @param $where
     * @return string
     */
    private function getSqlReportFromLims($where)
    {
        return "select 
                tz.ID_Z, 
                tz.DAY_TO_TEST,
                tz.type_of_day,
                tz.DATE_OPLATA,
                tz.DATE_ACT,
                tz.REQUEST_TITLE,
                sum(case when ml.lab_id = 1 then 1 else 0 end) as `lfhi`,
                sum(case when ml.lab_id = 2 then 1 else 0 end) as `lsm`,
                sum(case when ml.lab_id = 3 then 1 else 0 end) as `lfmi`,
                sum(case when ml.lab_id = 4 then 1 else 0 end) as `dsl`,
                sum(case when ml.lab_id = 5 then 1 else 0 end) as `osk`,
                sum(case when ml.lab_id = 7 then 1 else 0 end) as `smk`,
                count(ml.lab_id) as `total`,
                count(DISTINCT ml.lab_id) as `unic`
            from ulab_material_to_request as umtr 
            inner join ba_tz as tz on tz.ID_Z = umtr.deal_id
            inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            inner join ulab_methods as method on method.id = ugtp.new_method_id
            inner join ulab_methods_lab as ml on ml.method_id = method.id
            left join ulab_start_trials as st on st.ugtp_id = ugtp.id
            where /*(tz.OPLATA > 0 or tz.price_discount is null) and*/ umtr.deal_id not in (select TAKEN_ID_DEAL from ba_tz where TAKEN_ID_DEAL is not null) and {$where}
            group by tz.ID_Z";
    }


    /**
     * @param $dateStart
     * @param $dateEnd
     * @return bool|int|string
     */
    public function getCountTzNew($dateStart, $dateEnd)
    {
        return $this->DB->Query("select * from TZ_DOC where `DATE` between '{$dateStart} 00:00:00' and '{$dateEnd} 23:59:59'")->SelectedRowsCount();
    }


    /**
     * @param $dateStart
     * @param $dateEnd
     * @return bool|int|string
     */
    public function getCountTzInWork($dateStart, $dateEnd)
    {
        return $this->DB->Query(
            "select distinct umtr.deal_id from ulab_start_trials as st 
                    inner join ulab_gost_to_probe as ugtp on st.ugtp_id = ugtp.id
                    inner join ulab_material_to_request as umtr on ugtp.material_to_request_id = umtr.id
                    where st.`created_at` between '{$dateStart} 00:00:00' and '{$dateEnd} 23:59:59'
                    and ugtp.id not in (select `ugtp_id`
                    from `ulab_start_trials`                      
					where `state` = 'complete')"
        )->SelectedRowsCount();
    }


    /**
     * @param $dateStart
     * @param $dateEnd
     * @return bool|int|string
     */
    public function getCountTzComplete($dateStart, $dateEnd)
    {
        return $this->DB->Query("select * from AKT_VR where `DATE` between '{$dateStart} 00:00:00' and '{$dateEnd} 23:59:59'")->SelectedRowsCount();
    }


    /**
     * @param $dates
     * @return array
     */
    public function getDatesForStatistic($dates)
    {
        $datesArr = [];
        $currentYear = date('Y');

        foreach($dates as $date) {
            $datesArr[] = $date['end_date'];
        }

        return [
                'max_date' => date('Y-m-d', strtotime(max( $datesArr))),
                'first_date_of_year' => date('Y-m-d', mktime(0, 0, 0, 1, 1, intval($currentYear)))
            ];
    }

    /**
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getTzInWork($dateStart, $dateEnd, $lab_id)
    {
        $result = [];

        $holidaysArray = array(
            '2024.01.01', '2024.01.02', '2024.01.03', '2024.01.04', '2024.01.05', '2024.01.06', '2024.01.07', '2024.01.08',
            '2024.01.13', '2024.01.14', '2024.01.20', '2024.01.21', '2024.01.27', '2024.01.28', '2024.02.03', '2024.02.04',
            '2024.02.10', '2024.02.11', '2024.02.17', '2024.02.18', '2024.02.23', '2024.02.24', '2024.02.25', '2024.03.02',
            '2024.03.03', '2024.03.08', '2024.03.09', '2024.03.10', '2024.03.16', '2024.03.17', '2024.03.23', '2024.03.24',
            '2024.03.30', '2024.03.31', '2024.04.06', '2024.04.07', '2024.04.13', '2024.04.14', '2024.04.20', '2024.04.21',
            '2024.04.28', '2024.04.29', '2024.04.30', '2024.05.01', '2024.05.04', '2024.05.05', '2024.05.09', '2024.05.10',
            '2024.05.11', '2024.05.12', '2024.05.18', '2024.05.19', '2024.05.25', '2024.05.26', '2024.06.01', '2024.06.02',
            '2024.06.08', '2024.06.09', '2024.06.12', '2024.06.15', '2024.06.16', '2024.06.22', '2024.06.23', '2024.06.29',
            '2024.06.30', '2024.07.06', '2024.07.07', '2024.07.13', '2024.07.14', '2024.07.20', '2024.07.21', '2024.07.27',
            '2024.07.28', '2024.08.03', '2024.08.04', '2024.08.10', '2024.08.11', '2024.08.17', '2024.08.18', '2024.08.24',
            '2024.08.25', '2024.08.31', '2024.09.01', '2024.09.07', '2024.09.08', '2024.09.14', '2024.09.15', '2024.09.21',
            '2024.09.22', '2024.09.28', '2024.09.29', '2024.10.05', '2024.10.06', '2024.10.12', '2024.10.13', '2024.10.19',
            '2024.10.20', '2024.10.26', '2024.10.27', '2024.11.03', '2024.11.04', '2024.11.09', '2024.11.10', '2024.11.16',
            '2024.11.17', '2024.11.23', '2024.11.24', '2024.11.30', '2024.12.01', '2024.12.07', '2024.12.08', '2024.12.14',
            '2024.12.15', '2024.12.21', '2024.12.22', '2024.12.29', '2024.12.30', '2024.12.31'
        );

        $labWhere = '';

        if($lab_id == 0) {
            $labWhere .= '1';
        }else {
            $labWhere .= "uml.lab_id = {$lab_id}";
        }

		$response = $this->DB->Query(
//            "select distinct
//                    bt.ID_Z,
//                    bt.DAY_TO_TEST,
//                    bt.type_of_day,
//                    bt.DATE_OPLATA,
//                    bt.DATE_ACT,
//                    bt.REQUEST_TITLE,
//                    bt.DATE_SOZD,
//                    bcd.TYPE_ID,
//                    bcc.TITLE
//                    from ulab_start_trials as st
//                    inner join ulab_gost_to_probe as ugtp on st.ugtp_id = ugtp.id
//                    inner join ulab_methods_lab as uml on uml.method_id = ugtp.method_id
//                    inner join ulab_material_to_request as umtr on ugtp.material_to_request_id = umtr.id
//                    inner join ba_tz as bt on bt.ID_Z = umtr.deal_id
//                    inner join b_crm_deal as bcd ON bcd.ID = bt.ID_Z
//                    inner join b_crm_company as bcc ON bcc.ID = bcd.COMPANY_ID
//					left join AKT_VR as av on bt.ID = av.TZ_ID
//                    where st.`created_at` between '{$dateStart} 00:00:00' and '{$dateEnd} 23:59:59'
//                    and ugtp.id not in (select `ugtp_id`
//                    from `ulab_start_trials`
//					where `state` = 'complete') or st.ugtp_id is null
//                    and av.ID is null  and bt.ACT_NUM is not null and bt.OPLATA is not null
//					and {$labWhere}
//					order by bt.ID_Z
			"select distinct
                    bt.ID_Z, 
                    bt.DAY_TO_TEST,
                    bt.type_of_day,
                    bt.DATE_OPLATA,
                    bt.DATE_ACT,
                    bt.REQUEST_TITLE,
                    bcc.TITLE,
                    uml.*
                    from ulab_material_to_request as umtr
                    inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    inner join ulab_methods_lab as uml on uml.method_id = ugtp.method_id
                    left join ulab_start_trials as st on st.ugtp_id = ugtp.id
                    inner join ba_tz as bt on bt.ID_Z = umtr.deal_id 
                                        and bt.STAGE_ID not in ('WON', 2, 4, 'LOSE', 5, 6, 7, 8, 9, 10, 11, 12)
                        				and bt.OPLATA is not null and bt.OPLATA not in (0, '')  and bt.ACT_NUM is not null 
                    left join AKT_VR as av on bt.ID = av.TZ_ID and av.ID is null 
                    inner join b_crm_deal as bcd ON bcd.ID = bt.ID_Z
                    inner join b_crm_company as bcc ON bcc.ID = bcd.COMPANY_ID
                    /*where st.created_at between '{$dateStart} 00:00:00' and '{$dateEnd} 23:59:59'
                    and (ugtp.id not in (select ugtp_id
                    from ulab_start_trials                      
				    where state = 'complete') or st.ugtp_id is null)*/
				    and bt.DATE_CREATE_TIMESTAMP >= '2023-01-01 00:01'
                    and   bt.TYPE_ID = 'SALE'                                     
                    and {$labWhere}
				  order by bt.ID_Z"
		);



        while($row = $response->Fetch()) {
            $maxDate = date('Y-m-d', max([strtotime($row['DATE_OPLATA']), strtotime($row['DATE_ACT'])]));

            $endDate = '';

            if($row['TYPE_ID'] != 7) {
                if ($row['type_of_day'] == 'day') {
                    $endDate = date('Y-m-d', strtotime("{$maxDate} + {$row['DAY_TO_TEST']} days"));
                } else if ($row['type_of_day'] == 'work_day') {
                    $endDate = date('Y-m-d', strtotime("{$maxDate} + {$row['DAY_TO_TEST']} days"));
                    $endMonthDate = date('Y-m-d', strtotime("{$endDate} + 5 months"));

                    $period = new DatePeriod(
                        new DateTime($maxDate),

                        new DateInterval('P1D'),

                        new DateTime("{$endMonthDate} 23:59")
                    );

                    $daysCount = $row['DAY_TO_TEST'];

                    foreach ($period as $value) {
                        $checkDate = $value->format('Y-m-d');

                        if ($daysCount >= 0) {
                            if (!in_array($checkDate, HOLIDAYS)) {
                                $endDate = $checkDate;

                                $daysCount--;
                            }
                        }
                    }
                } else {
                    $endDate = date('Y-m-d', strtotime("{$maxDate} + {$row['DAY_TO_TEST']} months"));
                }
            }else {
                $maxDate = date('Y-m-d', strtotime($row['DATE_SOZD']));

                $endDate = date('Y-m-d', strtotime("{$maxDate} + {$row['DAY_TO_TEST']} days"));
                $endMonthDate = date('Y-m-d', strtotime("{$endDate} + 2 months"));

                $period = new DatePeriod(
                    new DateTime($maxDate),

                    new DateInterval('P1D'),

                    new DateTime("{$endMonthDate} 23:59")
                );

                $daysCount = 2;

                foreach ($period as $value) {
                    $checkDate = $value->format('Y-m-d');

                    if ($daysCount >= 0) {
                        if (!in_array($checkDate, HOLIDAYS)) {
                            $endDate = $checkDate;

                            $daysCount--;
                        }
                    }
                }
            }

            $result[$row['ID_Z']] =
                [
                    'id_tz' => $row['ID_Z'],
                    'type_of_day' => $row['type_of_day'],
                    'date_max' => $maxDate,
                    'day_to_test' => $row['DAY_TO_TEST'],
                    'end_date' => $endDate,
                    'title' => $row['REQUEST_TITLE'],
                    'company_title' => $row['TITLE']
                ];
        }

        $reversedResult = array_reverse($result);

        return $reversedResult;
    }

    public function getStatisticEntityForMonth($id, $month, $entityKey)
    {
        $year = date('Y');

        $months =
            [
                'Январь',
                'Февраль',
                'Март',
                'Апрель',
                'Май',
                'Июнь',
                'Июль',
                'Август',
                'Сентябрь',
                'Октябрь',
                'Ноябрь',
                'Декабрь',
            ];

        $result = [];

        if ( !array_key_exists($entityKey, $this->entities) || empty($id) ) {
            return $result;
        }

        $dataEntity = $this->entities[$entityKey];
        $sql = $dataEntity['chart']['bar']['days_sql'] ?? '';

        if ( empty($sql) ) {
            return $result;
        }

        $sql = str_replace('{id}', $id, $sql);
        $sql = str_replace('{month}', $month, $sql);
        $sql = str_replace('{year}', $year, $sql);
        $data = $this->DB->Query($sql);

        $result['label'] = '';
        $result['formatted'][0] = $dataEntity['chart']['bar']['formatted'] ?? 'Данные';

        $lastDayInMonth = date('t', strtotime("{$year}-{$month}-01 01:01:01"));

        for($i = 0; $i < $lastDayInMonth; $i++) {
            $result['value'][0][$i] = 0;
        }

        $result['date_start'] = date('Y-m-d', strtotime("{$year}-{$month}-1"));
        $result['date_end'] = date('Y-m-d', strtotime("{$year}-{$month}-{$lastDayInMonth}"));
        $result['month'] = $months[$month - 1];

        while ($row = $data->Fetch()) {
            $result['label'] = $row['label'];
            $result['value'][0][$row['day'] - 1] = $row['value'];

            if (isset($row['value_2'])) {
                if (empty($result['value'][1])) {
                    for($i = 0; $i < $lastDayInMonth; $i++) {
                        $result['value'][1][$i] = 0;
                    }
                }

                $result['value'][1][$row['day'] - 1] = $row['value_2'];
                $result['formatted'][1] = $dataEntity['chart']['bar']['formatted_2'] ?? 'Данные 2';

                $result['double'] = true;
            }else {
                $result['double'] = false;
            }
        }

        return $result;
    }

    /**
     * @param int $userId
     * @param array $filter = [ 'search' => [], 'order' => [], 'paginate' => [] ]
     * @return array
     */
    public function getTzStat(array $filter = []): array
    {
        $statusModel = new Status();
        $requestModel = new Request();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        $query = "";
        $dataFiltered = "";

        if ( !empty($filter) ) {
            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'requestTitle':
                        $order['by'] = 'bt.REQUEST_TITLE';
                        break;
                    case 'DATE_CREATE_TIMESTAMP':
                        $order['by'] = 'bt.DATE_CREATE_TIMESTAMP';
                        break;
                }
            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }

            // работа с фильтрами
            if ( !empty($filter['search']) ) {
                // Заявка
                if ( isset($filter['search']['requestTitle']) ) {
                    $where .= "bt.REQUEST_TITLE LIKE '%{$filter['search']['requestTitle']}%' AND ";
                }
                // ID заявки
                if ( isset($filter['search']['deal_id']) ) {
                    $where .= "bt.ID_Z = {$filter['search']['deal_id']} AND ";
                }
                // Дата
                if ( isset($filter['search']['DATE_CREATE_TIMESTAMP']) ) {
                    $where .= "LOCATE('{$filter['search']['DATE_CREATE_TIMESTAMP']}', DATE_FORMAT(bt.DATE_CREATE_TIMESTAMP, '%d.%m.%Y')) > 0 AND ";
                }

                $where .= " 1 ";

                // Статус заявки
                if ( isset($filter['search']['stageGroup']) ) {
                    switch ($filter['search']['stageGroup']) {
                        case 0:
                            $query =
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date, bt.STAGE_ID, bt.REQUEST_TITLE, bt.ID_Z
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND {$where}
                                ORDER BY {$order['by']} {$order['dir']} {$limit}
                                ";
                            $dataFiltered = $this->DB->Query(
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND {$where}
                                 ")->SelectedRowsCount();
                            break;
                        case 1:
                            $query =
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date, bt.STAGE_ID, bt.REQUEST_TITLE, bt.ID_Z
                                FROM ba_tz bt
                                INNER JOIN AKT_VR av ON av.TZ_ID = bt.ID
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND {$where}
                                ORDER BY {$order['by']} {$order['dir']} {$limit}
                                ";
                            $dataFiltered = $this->DB->Query(
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date
                                FROM ba_tz bt
                                INNER JOIN AKT_VR av ON av.TZ_ID = bt.ID
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND {$where}
                                 ")->SelectedRowsCount();
                            break;
                        case 2:
                            $query =
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date, bt.STAGE_ID, bt.REQUEST_TITLE, bt.ID_Z
                                FROM ba_tz bt
                                INNER JOIN AKT_VR av ON av.TZ_ID = bt.ID
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND av.SEND_DATE IS NOT NULL AND {$where}
                                ORDER BY {$order['by']} {$order['dir']} {$limit}
                                ";
                            $dataFiltered = $this->DB->Query(
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date
                                FROM ba_tz bt
                                INNER JOIN AKT_VR av ON av.TZ_ID = bt.ID
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND av.SEND_DATE IS NOT NULL AND {$where}
                                 ")->SelectedRowsCount();
                            break;
                        case 3:
                            $query =
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date, bt.STAGE_ID, bt.REQUEST_TITLE, bt.ID_Z
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND (bt.STAGE_ID = '4' OR bt.STAGE_ID = 'WON' OR bt.STAGE_ID = 'WON-NO-ACT') AND {$where}
                                ORDER BY {$order['by']} {$order['dir']} {$limit}
                                ";
                            $dataFiltered = $this->DB->Query(
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND (bt.STAGE_ID = '4' OR bt.STAGE_ID = 'WON' OR bt.STAGE_ID = 'WON-NO-ACT') AND {$where}
                                 ")->SelectedRowsCount();
                            break;
                        case 4:
                            $query =
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date, bt.STAGE_ID, bt.REQUEST_TITLE, bt.ID_Z
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND (bt.STAGE_ID = 'LOSE' OR bt.STAGE_ID = '5' OR bt.STAGE_ID = '6' OR bt.STAGE_ID = '7' OR bt.STAGE_ID = '8' OR bt.STAGE_ID = '9' OR bt.STAGE_ID = '10' OR bt.STAGE_ID = '11') AND {$where}
                                ORDER BY {$order['by']} {$order['dir']} {$limit}
                                ";
                            $dataFiltered = $this->DB->Query(
                                "
                                SELECT bt.ID_Z as id, bt.DATE_CREATE_TIMESTAMP as date
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_CREATE_TIMESTAMP) = 2024 AND (bt.STAGE_ID = 'LOSE' OR bt.STAGE_ID = '5' OR bt.STAGE_ID = '6' OR bt.STAGE_ID = '7' OR bt.STAGE_ID = '8' OR bt.STAGE_ID = '9' OR bt.STAGE_ID = '10' OR bt.STAGE_ID = '11') AND {$where}
                                 ")->SelectedRowsCount();
                            break;
                    }
                }
            }
        }
        $result = [];

        $data = $this->DB->Query($query);

        $dataTotal = $this->DB->Query(
            "SELECT bt.ID_Z as id
                                FROM ba_tz bt
                                WHERE YEAR(bt.DATE_SOZD) = 2024"
        )->SelectedRowsCount();



        while ($row = $data->Fetch()) {
            $statusInfo = $statusModel->getStatusesForDeal($row['id']);
            $stage = $requestModel->getStage($row);

            if($row['id'] < DEAL_NEW_STATUS) {
                $row['titleStage'] = $stage['title'];
                $row['bgStage'] = $stage['color'];
            }else {
                $row['titleStage'] = $statusInfo['title'];
                $row['bgStage'] = $statusInfo['color'];
            }

            $crmDeal = $requestModel->getDealById($row['id']);

            $row['REQUEST_TITLE'] = $crmDeal['TITLE'];

            $row['DATE_CREATE_TIMESTAMP'] = !empty($row['date']) && $row['date'] != "0000-00-00 00:00:00"
                ? date('Y-m-d',  strtotime($row['date']))
                : '';

            $row['dateCreateRu'] = !empty($row['date']) && $row['date'] != "0000-00-00 00:00:00"
                ? date('d.m.Y',  strtotime($row['date']))
                : '';

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * @param array $filter
     * @return array
     * @throws Exception
     */
    public function getDiffDayProtocolJournal($filter = [])
    {
        $requestModel = new Request();
        $statusModel = new Status();
        $methodModel = new Methods();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'tz.ID',
            'dir' => 'DESC'
        ];

        if ( !empty($filter) ) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if ( !empty($filter['search']) ) {
                // Заявка
                if ( isset($filter['search']['title']) ) {
                    $where .= "tz.REQUEST_TITLE LIKE '%{$filter['search']['title']}%' AND ";
                }
                // Номер протокола
                if ( isset($filter['search']['protocol_number']) ) {
                    $where .= "p.NUMBER LIKE '%{$filter['search']['protocol_number']}%' AND ";
                }
                if ( isset($filter['search']['in_lims']) ) {
                    if ( $filter['search']['in_lims'] == 'y' ) {
                        $where .= "p.PROTOCOL_OUTSIDE_LIS <> 1 AND ";
                    } else if ( $filter['search']['in_lims'] == 'n' ) {
                        $where .= "p.PROTOCOL_OUTSIDE_LIS = 1 AND ";
                    }

                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'title':
                        $order['by'] = 'tz.REQUEST_TITLE';
                        break;
                    case 'protocol_date':
                        $order['by'] = 'p.DATE';
                        break;
                    case 'test_date':
                        $order['by'] = 'p.DATE_END';
                        break;
                    case 'diff_day':
                        $order['by'] = 'DATEDIFF(p.DATE, p.DATE_END)';
                        break;
                    case 'diff_day_send':
                        $order['by'] = 'DATEDIFF(p.DATE, p.PROTOCOL_SEND_DATETIME)';
                        break;
                    case 'protocol_number':
                        $order['by'] = 'p.DATE desc, p.NUMBER';
                        break;
                }
            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "select 
                tz.*, tz.ID_Z as deal_id,
                tz.REQUEST_TITLE as title, 
                case when p.PROTOCOL_OUTSIDE_LIS = 1 then 'нет' else 'да' end as in_lims,
                p.NUMBER as protocol_number, p.DATE as p_date, p.DATE_END as p_date_end, 
                DATE_FORMAT(p.DATE, '%d.%m.%Y') as protocol_date,
                DATE_FORMAT(p.DATE_END, '%d.%m.%Y') as test_date,
                DATEDIFF(p.DATE, p.DATE_END) as diff_day,
                DATEDIFF(p.DATE, p.PROTOCOL_SEND_DATETIME) as diff_day_send,
                DATEDIFF(now(), p.DATE) as diff_day_no_send,
                u.LAST_NAME as `user`
            from ba_tz as tz
            inner join PROTOCOLS as p on p.DEAL_ID = tz.ID_Z
            inner join b_user as u on SUBSTRING_INDEX(SUBSTRING(p.VERIFY, 15, 3), '\"', 1) = u.ID
            where 
                p.NUMBER is not null and 
                p.INVALID <> 1 and
                tz.STAGE_ID not in ('LOSE','5','6','7','8','9','10','11','12') and 
                {$where}
            order by {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "select 
                tz.ID_Z as deal_id
            from ba_tz as tz
            inner join PROTOCOLS as p on p.DEAL_ID = tz.ID_Z
            inner join b_user as u on SUBSTRING_INDEX(SUBSTRING(p.VERIFY, 15, 3), '\"', 1) = u.ID
            where 
                p.NUMBER is not null and 
                p.INVALID <> 1 and
                tz.STAGE_ID not in ('LOSE','5','6','7','8','9','10','11','12')"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "select 
                tz.ID_Z as deal_id
            from ba_tz as tz
            inner join PROTOCOLS as p on p.DEAL_ID = tz.ID_Z
            inner join b_user as u on SUBSTRING_INDEX(SUBSTRING(p.VERIFY, 15, 3), '\"', 1) = u.ID
            where 
                p.NUMBER is not null and 
                p.INVALID <> 1 and
                tz.STAGE_ID not in ('LOSE','5','6','7','8','9','10','11','12') and 
                {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            if ($row['ID_Z'] < DEAL_NEW_STATUS) {
                $stage = $requestModel->getStage($row);
                $row['titleStage'] = $stage['title'];
                $row['bgStage'] = $stage['color'];
            } else {
                $statusInfo = $statusModel->getStatusesForDeal($row['ID_Z']);
                $row['titleStage'] = $statusInfo['title'];
                $row['bgStage'] = $statusInfo['color'];
            }

            if ( $row['diff_day_send'] == '' ) {
                $row['diff_day_send'] = "Не отправлено ({$row['diff_day_no_send']} дней)";
            }

            $days = $methodModel->getDatesFromRange($row['p_date_end'], $row['p_date']);

            $countWorkDay = count($days);

            if ( $countWorkDay > 0 ) {
                $countWorkDay--;
            }

            $row['diff_day'] = "{$countWorkDay}/{$row['diff_day']}";

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


}
