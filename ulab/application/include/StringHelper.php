<?php

class StringHelper {

    /**
     * @param string $text
     * @return string
     */
    public static function encode(string $text): string
    {
        return htmlspecialchars(trim($text), ENT_QUOTES);
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $textEnd
     * @param bool $encode
     * @return string
     */
    public static function cropString(string $string, int $length = 20, string $textEnd = '...', bool $encode = false): string
    {
        $string = $encode ? self::encode($string) : $string;
        if (mb_strlen($string, 'UTF-8') > $length) {
            $string = mb_substr($string, 0, $length, 'UTF-8') . $textEnd;
        }
        return $string;
    }

    /**
     * @param $name
     * @return false|string
     */
    public static function shortName($name)
    {
        return mb_substr($name, 0, 1, 'UTF-8');
    }

    /**
     * @param $price
     * @return string
     */
    public static function priceFormatRus($price)
    {
        return number_format($price, 2, ',', '') . " руб.";
    }

    /**
     * @param $datetime
     * @return false|string
     */
    public static function dateRu($datetime)
    {
        return date("d.m.Y", strtotime($datetime));
    }

	/**
	 * @param $str
	 * @return string|string[]|null
	 */
	public static function removeSpace($str)
	{
		return preg_replace("/\s+/u", " ", trim($str));
	}

    public static function numberToRussian ($sourceNumber){
        //Целое значение $sourceNumber вывести прописью по-русски
        //Максимальное значение для аругмента-числа PHP_INT_MAX
        //Максимальное значение для аругмента-строки минус/плюс 999999999999999999999999999999999999
        $smallNumbers=array( //Числа 0..999
            array('ноль'),
            array('','один','два','три','четыре','пять','шесть','семь','восемь','девять'),
            array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать',
                'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'),
            array('','','двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто'),
            array('','сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот'),
            array('','одна','две')
        );
        $degrees=array(
            array('дофигальон','','а','ов'), //обозначение для степеней больше, чем в списке
            array('тысяч','а','и',''), //10^3
            array('миллион','','а','ов'), //10^6
            array('миллиард','','а','ов'), //10^9
            array('триллион','','а','ов'), //10^12
            array('квадриллион','','а','ов'), //10^15
            array('квинтиллион','','а','ов'), //10^18
            array('секстиллион','','а','ов'), //10^21
            array('септиллион','','а','ов'), //10^24
            array('октиллион','','а','ов'), //10^27
            array('нониллион','','а','ов'), //10^30
            array('дециллион','','а','ов') //10^33
            //досюда написано в Вики по нашей короткой шкале: https://ru.wikipedia.org/wiki/Именные_названия_степеней_тысячи
        );

        if ($sourceNumber==0) return $smallNumbers[0][0]; //Вернуть ноль
        $sign = '';
        if ($sourceNumber<0) {
            $sign = 'минус '; //Запомнить знак, если минус
            $sourceNumber = substr ($sourceNumber,1);
        }
        $result=array(); //Массив с результатом

        //Разложение строки на тройки цифр
        $digitGroups = array_reverse(str_split(str_pad($sourceNumber,ceil(strlen($sourceNumber)/3)*3,'0',STR_PAD_LEFT),3));
        foreach($digitGroups as $key=>$value){
            $result[$key]=array();
            //Преобразование трёхзначного числа прописью по-русски
            foreach ($digit=str_split($value) as $key3=>$value3) {
                if (!$value3) continue;
                else {
                    switch ($key3) {
                        case 0:
                            $result[$key][] = $smallNumbers[4][$value3];
                            break;
                        case 1:
                            if ($value3==1) {
                                $result[$key][]=$smallNumbers[2][$digit[2]];
                                break 2;
                            }
                            else $result[$key][]=$smallNumbers[3][$value3];
                            break;
                        case 2:
                            if (($key==1)&&($value3<=2)) $result[$key][]=$smallNumbers[5][$value3];
                            else $result[$key][]=$smallNumbers[1][$value3];
                            break;
                    }
                }
            }
            $value*=1;
            if (!$degrees[$key]) $degrees[$key]=reset($degrees);

            //Учесть окончание слов для русского языка
            if ($value && $key) {
                $index = 3;
                if (preg_match("/^[1]$|^\\d*[0,2-9][1]$/",$value)) $index = 1; //*1, но не *11
                else if (preg_match("/^[2-4]$|\\d*[0,2-9][2-4]$/",$value)) $index = 2; //*2-*4, но не *12-*14
                $result[$key][]=$degrees[$key][0].$degrees[$key][$index];
            }
            $result[$key]=implode(' ',$result[$key]);
        }

        return $sign.implode(' ',array_reverse($result));
    }

    public static function numDeclension ($number, $titles)
    {
        $abs = abs($number);
        $cases = array (2, 0, 1, 1, 1, 2);
        return $titles[ ($abs%100 > 4 && $abs %100 < 20) ? 2 : $cases[min($abs%10, 5)] ];
    }

    public static function getMonthTitle($monthNumber)
    {
        $monthArr = [
            "январь", "февраль", "март", "апрель", "май", "июнь",
            "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"
        ];

        return $monthArr[$monthNumber - 1];
    }

    public static function setTextMoneyFormat($sum)
    {
        $sum = number_format($sum, 2, '.', '');

        $fullSumArr = explode(".", $sum);
        $fullSumRub = $fullSumArr[0];
        $fullSumCop = $fullSumArr[1];

        return $fullSumRub . " " . StringHelper::numDeclension($fullSumRub, ["рубль", "рубля", "рублей"]) . " " . $fullSumCop . " коп.";
    }

    public static function floatValue($val)
    {
        $val = str_replace(",",".",$val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        
        return floatval($val);
    }

    public function getInitials($str)
    {
        return mb_substr($str, 0, 1, 'UTF-8') . '.';
    }

    public function num_word($n, $form1, $form2, $form3)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) {
            return $form3;
        }

        if ($n1 > 1 && $n1 < 5) {
            return $form2;
        }

        if ($n1 == 1) {
            return $form1;
        }

        return $form3;
    }

	public static function num2str($num) {
		$nul='ноль';
		$ten=array(
			array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
			array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
		);
		$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
		$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
		$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
		$unit=array( // Units
			array('копейка' ,'копейки' ,'копеек',	 1),
			array('рубль'   ,'рубля'   ,'рублей'    ,0),
			array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
			array('миллион' ,'миллиона','миллионов' ,0),
			array('миллиард','милиарда','миллиардов',0),
		);
		//
		list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
		$out = array();
		if (intval($rub)>0) {
			foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
				if (!intval($v)) continue;
				$uk = sizeof($unit)-$uk-1; // unit key
				$gender = $unit[$uk][3];
				list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
				// mega-logic
				$out[] = $hundred[$i1]; # 1xx-9xx
				if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
				else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
				// units without rub & kop
				if ($uk>1) $out[]= self::num_word($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
			} //foreach
		}
		else $out[] = $nul;
		$out[] = self::num_word(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
		$out[] = $kop.' '. self::num_word($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
		return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	}

	public static function mb_strtoupper_first($str, $encoding = 'UTF8')
	{
		return
			mb_strtoupper(mb_substr($str, 0, 1)) .
			mb_strtolower(mb_substr($str, 1, mb_strlen($str)));
	}

	public static function getMonthGen($date)
    {
        list($year, $month, $day) = explode('-', $date);

// Массив месяцев в родительном падеже
        $months = [
            1 => 'января', 2 => 'февраля', 3 => 'марта',
            4 => 'апреля', 5 => 'мая', 6 => 'июня',
            7 => 'июля', 8 => 'августа', 9 => 'сентября',
            10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        ];

        return (int)$day . ' ' . $months[(int)$month] . ' ' . $year;
    }
}
