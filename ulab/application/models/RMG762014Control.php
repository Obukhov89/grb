<?php


class RMG762014Control extends Model
{
    // коэффициенты
    const a = [2 => 1.128, 3 => 1.693, 4 => 2.059, 5 => 2.326];
    const A1 = [2 => 2.834, 3 => 3.469, 4 => 3.819, 5 => 4.054];
    const A2 = [2 => 3.686, 3 => 4.358, 4 => 4.698, 5 => 4.918];

    /**
     * Результаты контрольных определений (Результаты измерений)
     * @param $data
     * @return array
     */
    public function getControlData($data)
    {
        $vlkModel = new Vlk;

        $umcId = (int)$data['umc_id'];
        $dateStart = $data['date_start'] ?? '';
        $dateEnd = $data['date_end'] ?? '';

        $measuring = $vlkModel->getVlkMeasuringByDate($umcId, $dateStart, $dateEnd);

        return array_column($measuring, 'result');
    }

    /**
     * Дополнительные данные
     * @param $data
     * @return array
     */
    public function getExtraData($data)
    {
        $vlkModel = new Vlk;
        $oborudModel = new Oborud;
        $methodsModel = new Methods;

        $umc = $vlkModel->getMethodComponent((int)$data['umc_id']);
        $component = $oborudModel->getComponent((int)$umc['component_id']);
        $uncertainty = $methodsModel->getUncertainty((int)$umc['method_id']);

        $certifiedValue = $component['certified_value'] ?? null;
        $measuringCount = $umc['measuring_count'] ?? null;

        $accuracyControl = $methodsModel->findUncertaintyData($uncertainty, $certifiedValue);

        return [
            'certified_value' => $certifiedValue,
            'measuring_count' => $measuringCount,
            'Rl' => $accuracyControl['Rl'] ?? null,
            'r' => $accuracyControl['r'] ?? null,
            'Kt' => $accuracyControl['Kt'] ?? null,
        ];
    }

    public function validateData($control, $controlData, $extraData) {
        if (empty($controlData)) {
            return [
                'success' => false,
                'errors' => "Внимание! Отсутствуют результаты контрольных определений"
            ];
        }

        if (in_array($control, ['repetition', 'precision']) && !isset($extraData['r'])) {
            return [
                'success' => false,
                'errors' => "Внимание! Отсутствует показатель контроля точности 'r'"
            ];
        }

        if (in_array($control, ['repetition', 'precision']) &&
            (!isset($extraData['measuring_count']) || $extraData['measuring_count'] < 2 || $extraData['measuring_count'] > 5)) {
            return [
                'success' => false,
                'errors' => "Внимание! Кол-во контрольных определений менее 2 или более 5"
            ];
        }

        if (in_array($control, ['deviation']) && !isset($extraData['Kt'])) {
            return [
                'success' => false,
                'errors' => "Внимание! Отсутствует показатель контроля точности 'Кт'"
            ];
        }

        if (in_array($control, ['deviation']) && !isset($extraData['certified_value'])) {
            return [
                'success' => false,
                'errors' => "Внимание! Не указано аттестованное значение, заполните данные."
            ];
        }

        return ['success' => true];
    }

    /**
     * Получает наименование диаграммы контрольных карт
     * @param $control
     * @return string
     */
    public function getChartLabel($control)
    {
        switch ($control) {
            case 'repetition':
                $label = 'Контрольная карта Шухарта для контроля повторяемости';
                break;
            case 'precision':
                $label = 'Контрольная карта Шухарта для контроля внутрилабораторной прецизионности';
                break;
            case 'deviation':
                $label = 'Контрольная карта Шухарта для контроля погрешности с применением ОК';
                break;
            default:
                $label = '';
        }

        return $label;
    }

    /**
     * Получает метки оси X
     * @param $control
     * @param $points
     * @return array
     */
    public function getXAxisLabel($control, $points)
    {
        $response = [];

        $control = ucfirst($control);
        $method = "getXAxisLabelFor{$control}";

        if (method_exists($this, $method)) {
            $response = $this->$method($points);
        }

        return $response;
    }

    public function getXAxisLabelForRepetition($points)
    {
        return $this->generateSequentialLabels($points);
    }

    public function getXAxisLabelForPrecision($points)
    {
        return $this->generateSequentialLabels($points);
    }

    public function getXAxisLabelForDeviation($points)
    {
        return $this->generateSequentialLabels($points);
    }

    public function generateSequentialLabels($data)
    {
        $count = count($data);
        $labels = [];
        for ($i = 0; $i < $count; ++$i) {
            $labels[] = $i + 1;
        }

        return $labels;
    }

    /**
     * Получает значение средней линии
     * @param $control
     * @param $extraData
     * @return |null
     */
    public function calculateAverageLine($control, $extraData)
    {
        return $this->calculateLineValue($control, $extraData, 'average');
    }

    /**
     * Получает значение предела предупреждения
     * @param $control
     * @param $extraData
     * @return array
     */
    public function calculateWarningLimit($control, $extraData)
    {
        $topLine = $this->calculateLineValue($control, $extraData, 'warningTop');

        $bottomLine = null;
        if ($this->shouldCalculateBottom($control)) {
            $bottomLine = $this->calculateLineValue($control, $extraData, 'warningBottom');
        }

        return [
            'top' => $topLine,
            'bottom' => $bottomLine
        ];
    }

    /**
     * Получает значение предела действия
     * @param $control
     * @param $extraData
     * @return array
     */
    public function calculateActionLimit($control, $extraData)
    {
        $topLine = $this->calculateLineValue($control, $extraData, 'actionTop');

        $bottomLine = null;
        if ($this->shouldCalculateBottom($control)) {
            $bottomLine = $this->calculateLineValue($control, $extraData, 'actionBottom');
        }

        return [
            'top' => $topLine,
            'bottom' => $bottomLine
        ];
    }

    public function calculateLineValue($control, $extraData, $type)
    {
        $response = null;

        $type = ucfirst($type);
        $method = "{$control}{$type}Line";

        if (method_exists($this, $method)) {
            $response = $this->$method($extraData);
        }

        return $response;
    }

    /**
     * Проверяет нужно ли рассчитать нижний предел
     * @param $control
     * @return bool
     */
    private function shouldCalculateBottom($control)
    {
        return ($control !== 'repetition' && $control !== 'precision');
    }

    /**
     * Расчёт средней линии повторяемости rср = an * σr
     * an = [2 => 1.128, 3 => 1.693, 4 => 2.059, 5 => 2.326]
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function repetitionAverageLine($extraData)
    {
        $aSelected = self::a[$extraData['measuring_count']] ?? null;

        if (!isset($aSelected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;

        return $aSelected * $sigmaR;
    }

    /**
     * Расчёт средней линии прецизионности Rср = a2 * σRЛ
     * an = [2 => 1.128, 3 => 1.693, 4 => 2.059, 5 => 2.326]
     * σRл = 0.84 * σR
     * σR = σr / 0.7
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function precisionAverageLine($extraData)
    {
        $aSelected = self::a[$extraData['measuring_count']] ?? null;

        if (!isset($aSelected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;
        $sigmaRL = 0.84 * ($sigmaR / 0.7);

        return $aSelected * $sigmaRL;
    }

    public function deviationAverageLine($extraData)
    {
        return 0;
    }

    /**
     * Расчёт предела предупреждения повторяемости rпр = A1,n * σr
     * A1 = [2 => 2.834, 3 => 3.469, 4 => 3.819, 5 => 4.054]
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function repetitionWarningTopLine($extraData)
    {
        $A1Selected = self::A1[$extraData['measuring_count']] ?? null;

        if (!isset($A1Selected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;

        return $A1Selected * $sigmaR;
    }

    /**
     * Расчёт верхнего предела предупреждения прецизионности Rпр = A1,2 * σRЛ
     * A1 = [2 => 2.834, 3 => 3.469, 4 => 3.819, 5 => 4.054]
     * σRл = 0.84 * σR
     * σR = σr / 0.7
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function precisionWarningTopLine($extraData)
    {
        $A1Selected = self::A1[$extraData['measuring_count']] ?? null;

        if (!isset($A1Selected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;
        $sigmaRL = 0.84 * ($sigmaR / 0.7);

        return $A1Selected * $sigmaRL;
    }

    /**
     * Расчёт верхнего предела предупреждения погрешности Kпр,в = Kпр = Δл
     * Кт - из методики в зависимости от аттестационного значения
     * Δл = 0.84 * Кт
     * @param $extraData
     * @return float|null
     */
    public function deviationWarningTopLine($extraData) {
        if (!isset($extraData['Kt']) || !is_numeric($extraData['Kt'])) {
            return null;
        }

        return 0.84 * $extraData['Kt'];
    }

    /**
     * Расчёт нижнего предела предупреждения погрешности Kпр,н = − Kпр
     * @param $extraData
     * @return float|null
     */
    public function deviationWarningBottomLine($extraData) {
        if (!isset($extraData['Kt']) || !is_numeric($extraData['Kt'])) {
            return null;
        }

        return -(0.84 * $extraData['Kt']);
    }

    /**
     * Расчёт верхнего предела действия повторяемости rд = A2,n * σr
     * A2 = [2 => 3.686, 3 => 4.358, 4 => 4.698, 5 => 4.918]
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function repetitionActionTopLine($extraData)
    {
        $A2Selected = self::A2[$extraData['measuring_count']] ?? null;

        if (!isset($A2Selected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;

        return $A2Selected * $sigmaR;
    }

    /**
     * Расчёт верхнего предела действия прецизионности Rд = A2,2 * σRЛ
     * A2 = [2 => 3.686, 3 => 4.358, 4 => 4.698, 5 => 4.918]
     * σRл = 0.84 * σR
     * σR = σr / 0.7
     * σr = r / 2.77 (r - берём из методики в зависимости от аттестационного значения)
     * @param $extraData
     * @return float|int|null
     */
    public function precisionActionTopLine($extraData)
    {
        $A2Selected = self::A2[$extraData['measuring_count']] ?? null;

        if (!isset($A2Selected) || !isset($extraData['r']) || !is_numeric($extraData['r'])) {
            return null;
        }

        $sigmaR = $extraData['r'] / 2.77;
        $sigmaRL = 0.84 * ($sigmaR / 0.7);

        return $A2Selected * $sigmaRL;
    }

    /**
     * Расчёт верхнего предела действия погрешности Kд,в = Kд = 1,5 * Δл = 1,5 * Kпр
     * Kпр = Δл
     * Δл = 0.84 * Кт
     * @param $extraData
     * @return float|null
     */
    public function deviationActionTopLine($extraData)
    {
        if (!isset($extraData['Kt']) || !is_numeric($extraData['Kt'])) {
            return null;
        }

        return 1.5 * (0.84 * $extraData['Kt']);
    }

    /**
     * Расчёт нижнего предела действия погрешности Kд,н = − Kд
     * @param $extraData
     * @return float|null
     */
    public function deviationActionBottomLine($extraData)
    {
        if (!isset($extraData['Kt']) || !is_numeric($extraData['Kt'])) {
            return null;
        }

        return -(1.5 * (0.84 * $extraData['Kt']));
    }

    /**
     * Рассчитывает результат контрольной процедуры
     * @param $control
     * @param $controlData
     * @param $extraData
     * @return array
     */
    public function calculateControlData($control, $controlData, $extraData)
    {
        $response = [];

        $method = "{$control}ControlData";

        if (method_exists($this, $method)) {
            $response = $this->$method($controlData, $extraData);
        }

        return $response;
    }

    /**
     * Результат контрольной процедуры для внутрилабораторной прецизионности
     * @param $controlData
     * @param $extraData
     * @return array
     */
    public function repetitionControlData($controlData, $extraData)
    {
        $response = [];
        foreach ($controlData as $values) {

            $max = max($values);
            $min = min($values);

            $response[] = $max - $min;
        }

        return $response;
    }

    public function precisionControlData($controlData, $extraData)
    {
        $averages = $this->calcRowAvg($controlData);

        return $this->subtractPrevVal($averages);
    }

    public function deviationControlData($controlData, $extraData)
    {
        $response= [];

        $certifiedValue = $extraData['certified_value'] ?? null;
        if (!isset($certifiedValue)) {
            return null;
        }

        $averages = $this->calcRowAvg($controlData);

        foreach ($averages as $value) {
            $result = $value - $certifiedValue;
            $response[] = $result;
        }

        return $response;
    }

    /**
     * Находит среднее значение каждой строки многомерного массива и возвращает массив средних значений
     * @param $arr
     * @return array
     */
    public function calcRowAvg($arr)
    {
        $avg = [];

        foreach ($arr as $key => $row) {
            $sum = array_sum($row);
            $count = count($row);

            if ($count > 0) {
                $avg[] = $sum / $count;
            } else {
                $avg[] = 0;
            }
        }

        return $avg;
    }

    /**
     * Вычисляет разности между текущим и следующим значениями в массиве средних абсолютных значений.
     * @param $averages
     * @return array
     */
    public function subtractPrevVal($averages)
    {
        $response= [];
        $count = count($averages);

        for ($i = 0; $i < $count - 1; $i++) {
            // Вычисляем разницу между текущим и следующим значениями и добавляем ее в массив
            $diff = abs($averages[$i] - $averages[$i + 1]);
            $response[] = $diff;
        }

        return $response;
    }

    /**
     * Проверка предсказуемости
     * @param $control
     * @param $points
     * @param $averageLine
     * @param $warningLimit
     * @param $actionLimit
     * @return array
     */
    public function checkPredictability($control, $points, $averageLine, $warningLimit, $actionLimit) {
        $response = [];

        $control = ucfirst($control);
        $method = "check{$control}";

        if (method_exists($this, $method)) {
            $response = $this->$method($points, $averageLine, $warningLimit, $actionLimit);
        }

        return $response;
    }

    public function checkRepetition($points, $averageLine, $warningLimit, $actionLimit) {
        return $this->checkRepetitionPrecision($points, $averageLine, $warningLimit, $actionLimit);
    }

    public function checkPrecision($points, $averageLine, $warningLimit, $actionLimit) {
        return $this->checkRepetitionPrecision($points, $averageLine, $warningLimit, $actionLimit);
    }

    /**
     * РМГ 76-2014 6.3.4 Анализ данных для контроля повторяемости или внутрилабораторной прецизионности
     * @param $points
     * @param $averageLine
     * @param $warningLimit
     * @param $actionLimit
     * @return array
     */
    public function checkRepetitionPrecision($points, $averageLine, $warningLimit, $actionLimit) {
        $actionTop = $actionLimit['top'] ?? null;
        $actionBottom = $actionLimit['bottom'] ?? null;
        $warningTop = $warningLimit['top'] ?? null;

        $aboveAverage = 0;
        $incSeqPoints = 0;
        $aboveWarning = 0;
        $aboveHalfWarning = 0;

        if (empty($points)) {
            return [
                'isPredictable' => false,
                'message' => "Невозможно определить предсказуемость, отсутствуют данные"
            ];
        }

        foreach ($points as $i => $point) {
            // одна точка вышла за предел действия
            if (($actionTop !== null && $point > $actionTop) || ($actionBottom !== null && $point < $actionBottom)) {
                return [
                    'isPredictable' => false,
                    'message' => "Непредсказуемый - 'одна точка вышла за предел действия', примите меры"
                ];
            }

            // девять точек подряд находятся выше средней линии
            if ($averageLine !== null && $point > $averageLine) {
                $aboveAverage++;
                if ($aboveAverage >= 9) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'девять точек подряд находятся выше средней линии', примите меры"
                    ];
                }
            } else {
                $aboveAverage = 0;
            }

            // шесть возрастающих точек подряд *(30) [при построении контрольной карты с использованием одного и того же ОК (пробы)]
            if ($i > 0 && $point > $points[$i - 1]) {
                $incSeqPoints++;
                if ($incSeqPoints >= 6) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'шесть возрастающих точек подряд', примите меры"
                    ];
                }
            } else {
                $incSeqPoints = 0;
            }

            // две из трех последовательных точек находятся выше предела предупреждения
            if ($warningTop !== null && $point > $warningTop) {
                $aboveWarning++;
                if ($aboveWarning >= 2) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'две из трех последовательных точек находятся выше предела предупреждения', примите меры"
                    ];
                }
            } else {
                $aboveWarning = 0;
            }

            // четыре из пяти последовательных точек находятся выше половинной границы зоны предупреждения
            // (т. е. четыре из пяти последовательных результатов контрольных процедур превышают значение rср + (rпр − rср) / 2
            // при контроле повторяемости, значение Rср + (Rпр − Rср) / 2 — при контроле внутрилабораторной прецизионности)
            if ($averageLine !== null && $warningTop !== null &&
                $point > ($averageLine + ($warningTop - $averageLine) / 2)) {
                $aboveHalfWarning++;
                if ($aboveHalfWarning >= 4) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'четыре из пяти последовательных точек находятся выше половинной границы зоны предупреждения', примите меры"
                    ];
                }
            } else {
                $aboveHalfWarning = 0;
            }
        }

        return ['isPredictable' => true, 'message' => "Предсказуемый"];
    }

    /**
     * РМГ 76-2014 6.3.4 Анализ данных для контроля точности
     * @param $points
     * @param $averageLine
     * @param $warningLimit
     * @param $actionLimit
     * @return array
     */
    public function checkDeviation($points, $averageLine, $warningLimit, $actionLimit) {
        $actionTop = $actionLimit['top'] ?? null;
        $actionBottom = $actionLimit['bottom'] ?? null;
        $warningTop = $warningLimit['top'] ?? null;
        $warningBottom = $warningLimit['bottom'] ?? null;

        $consecutivePoints = 0;
        $previousDirection = null;
        $incSeqPoints = 0;
        $decSeqPoints = 0;
        $aboveWarning = 0;
        $belowWarning = 0;
        $aboveHalfWarning = 0;
        $belowHalfWarning = 0;
        $consecBothHalfWarns = 0;
        $aboveAverage = false;
        $belowAverage = false;

        if (empty($points)) {
            return [
                'isPredictable' => false,
                'message' => "Невозможно определить предсказуемость, отсутствуют данные"
            ];
        }

        foreach ($points as $i => $point) {
            // одна точка вышла за пределы действия
            if (($actionTop !== null && $point > $actionTop) || ($actionBottom !== null && $point < $actionBottom)) {
                return [
                    'isPredictable' => false,
                    'message' => "Непредсказуемый - 'одна точка вышла за предел действия', примите меры"
                ];
            }

            if ($averageLine !== null) {
                // Определяем направление отклонения от средней линии
                $direction = ($point > $averageLine) ? 'above' : 'below';

                // Проверяем, совпадает ли направление с предыдущим
                if ($direction !== $previousDirection) {
                    $consecutivePoints = 1; // Начинаем новую серию
                    $previousDirection = $direction;
                } else {
                    $consecutivePoints++;
                }

                // девять точек подряд находятся по одну сторону от средней линии
                if ($consecutivePoints >= 9) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'девять точек подряд находятся по одну сторону от средней линии', примите меры"
                    ];
                }
            }

            // шесть возрастающих или убывающих точек подряд
            if ($i > 0 && $point > $points[$i - 1]) {
                $incSeqPoints++;
                if ($incSeqPoints >= 6) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'шесть возрастающих точек подряд', примите меры"
                    ];
                }
            } else {
                $incSeqPoints = 0;
            }

            if ($i > 0 && $point < $points[$i - 1]) {
                $decSeqPoints++;
                if ($decSeqPoints >= 6) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'шесть убывающих точек подряд', примите меры"
                    ];
                }
            } else {
                $decSeqPoints = 0;
            }

            // две из трех последовательных точек вышли за пределы предупреждения
            if ($warningTop !== null && $point > $warningTop) {
                $aboveWarning++;
                if ($aboveWarning >= 2) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'две из трех последовательных точек находятся выше предела предупреждения', примите меры"
                    ];
                }
            } else {
                $aboveWarning = 0;
            }

            if ($warningBottom !== null && $point < $warningBottom) {
                $belowWarning++;
                if ($belowWarning >= 2) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'две из трех последовательных точек находятся ниже предела предупреждения', примите меры"
                    ];
                }
            } else {
                $belowWarning = 0;
            }


            // четыре из пяти последовательных точек вышли за половинные границы верхней или нижней зоны предупреждения
            // (т. е. значения четырех из пяти последовательных результатов контрольных процедур больше Кпр / 2 или меньше −Кпр / 2)
            if ($warningTop !== null && $point > ($warningTop / 2)) {
                $aboveHalfWarning++;
                if ($aboveHalfWarning >= 4) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'четыре из пяти последовательных точек вышли за половинные границы верхней зоны предупреждения', примите меры"
                    ];
                }
            } else {
                $aboveHalfWarning = 0;
            }

            if ($warningTop !== null && $point < ($warningBottom / 2)) {
                $belowHalfWarning++;
                if ($belowHalfWarning >= 4) {
                    return [
                        'isPredictable' => false,
                        'message' => "Непредсказуемый - 'четыре из пяти последовательных точек вышли за половинные границы нижней зоны предупреждения', примите меры"
                    ];
                }
            } else {
                $belowHalfWarning = 0;
            }

            // восемь последовательных точек находятся по обеим сторонам средней линии,
            // и все эти точки вышли за половинные границы зоны предупреждения
            // (т. е. модуль значений восьми последовательных результатов контрольных процедур превышает значение Кпр / 2)
            if ($warningTop !== null && $point > ($warningTop / 2)) {
                $aboveAverage = true;
                $consecBothHalfWarns++;
            } elseif ($warningBottom !== null && $point < ($warningBottom / 2)) {
                $belowAverage = true;
                $consecBothHalfWarns++;
            } else {
                $aboveAverage = false;
                $belowAverage = false;
                $consecBothHalfWarns = 0;
            }

            if ($consecBothHalfWarns >= 8 && $aboveAverage && $belowAverage) {
                return [
                    'isPredictable' => false,
                    'message' => "Непредсказуемый - 'восемь последовательных точек находятся по обеим сторонам средней линии и все эти точки вышли за половинные границы зоны предупреждения', примите меры"
                ];
            }
        }

        return ['isPredictable' => true, 'message' => "Предсказуемый"];
    }

}