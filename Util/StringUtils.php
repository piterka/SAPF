<?php

namespace SAPF\Util;

class StringUtils
{

    public static function getDateStringRelative($timestamp, $fullFormat = 'd.m.Y H:i', $timeFormat = 'H:i', \SAPF\Translate\TranslateInterface $lang = null)
    {
        if (date('d.m.Y') === date('d.m.Y', $timestamp)) {
            if ($lang) {
                return $lang->t("Dziś o {TIME}", array('{TIME}' => date($timeFormat, $timestamp)));
            }
            return "Dziś o " . date($timeFormat, $timestamp);
        }

        if (date('d.m.Y') === date('d.m.Y', $timestamp + 86400)) {
            if ($lang) {
                return $lang->t("Wczoraj o {TIME}", array('{TIME}' => date($timeFormat, $timestamp)));
            }
            return "Wczoraj o " . date($timeFormat, $timestamp);
        }

        if (date('d.m.Y') === date('d.m.Y', $timestamp + 2 * 86400)) {
            if ($lang) {
                return $lang->t("Przedwczoraj o {TIME}", array('{TIME}' => date($timeFormat, $timestamp)));
            }
            return "Przedwczoraj o " . date($timeFormat, $timestamp);
        }

        if (date('d.m.Y') === date('d.m.Y', $timestamp - 86400)) {
            if ($lang) {
                return $lang->t("Jutro o {TIME}", array('{TIME}' => date($timeFormat, $timestamp)));
            }
            return "Jutro o " . date($timeFormat, $timestamp);
        }

        if (date('d.m.Y') === date('d.m.Y', $timestamp - 2 * 86400)) {
            if ($lang) {
                return $lang->t("Pojutrze o {TIME}", array('{TIME}' => date($timeFormat, $timestamp)));
            }
            return "Pojutrze o " . date($timeFormat, $timestamp);
        }

        return date($fullFormat, $timestamp);
    }

    public static function getTimeStringFromMinutes($minCnt)
    {
        if ($minCnt <= 0) {
            return htmlspecialchars("< 1min");
        }

        $dni_r    = ($minCnt) / (60 * 24);
        $dni_c    = floor($dni_r);
        $godzin_r = ($dni_r - $dni_c) * 24;
        $godzin_c = floor($godzin_r);
        $minut_r  = ($godzin_r - $godzin_c) * 60;
        $minut_c  = floor($minut_r);

        $ret = "";

        if ($dni_c > 0) {
            $ret .= $dni_c . "d";
        }
        if ($godzin_c > 0) {
            $ret .= " " . $godzin_c . "h";
        }
        if ($minut_c > 0) {
            $ret .= " " . $minut_c . "min";
        }

        return $ret;
    }

    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    public static function equalIgnoreCase($a, $b)
    {
        return strtolower($a) == strtolower($b);
    }

}
