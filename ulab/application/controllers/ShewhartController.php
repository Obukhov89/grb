<?php

/**
 * @desc КК Шухарта
 * Class ShewhartController
 */
class ShewhartController extends Controller
{
    /**
     * @desc Получить данные для построения КК Шухарта согласно РМГ 76-2014
     * @param $data
     * @return array
     */
    public function processControlRMG762014($data)
    {
        /** @var  RMG762014Control $RMG762014Model */
        $RMG762014Model = $this->model('RMG762014Control');

        $control = $data['control'] ?? '';

        $controlData = $RMG762014Model->getControlData($data);
        $extraData = $RMG762014Model->getExtraData($data);

        $valid = $RMG762014Model->validateData($control, $controlData, $extraData);
        if (!$valid['success']) {
            return $valid;
        }

        $averages = $RMG762014Model->calcRowAvg($controlData);
        $points = $RMG762014Model->calculateControlData($control, $controlData, $extraData);
        $averageLine = $RMG762014Model->calculateAverageLine($control, $extraData);
        $warningLimit = $RMG762014Model->calculateWarningLimit($control, $extraData);
        $actionLimit = $RMG762014Model->calculateActionLimit($control, $extraData);

        $predictability = $RMG762014Model->checkPredictability($control, $points, $averageLine, $warningLimit, $actionLimit);

        $chartLabel = $RMG762014Model->getChartLabel($control);
        $xAxisLabel = $RMG762014Model->getXAxisLabel($control, $points);

        return $response = [
            'success' => true,
            'control' => $control,
            'measuringCount' => (int)$extraData['measuring_count'],
            'predictability' => $predictability,
            'controlData' => $controlData,
            'averages' => $averages,
            'chartLabel' => $chartLabel,
            'xAxisLabel' => $xAxisLabel,
            'points' => $points,
            'averageLine' => [
                'data' => $averageLine,
                'color' => 'green',
            ],
            'warningLimit' => [
                'top' => $warningLimit['top'],
                'bottom' => $warningLimit['bottom'],
                'color' => 'yellow',
            ],
            'actionLimit' => [
                'top' => $actionLimit['top'],
                'bottom' => $actionLimit['bottom'],
                'color' => 'red',
            ],
        ];
    }

    /**
     * @desc Получить данные для построения КК Шухарта согласно РМГ 76-2014 Ajax запросом
     */
    public function processControlRMG762014Ajax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $formData = [];
        if (!empty($_POST['form'])) {
            parse_str($_POST['form'], $formData);
        }

        if (!empty($formData)) {
            $response = $this->processControlRMG762014($formData);
        } else {
            $response = [
                'success' => false,
                'errors' => "Ошибка, не указаны или указаны неверно данные"
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}