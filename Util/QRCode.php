<?php

namespace SAPF\Util;

class QRCode
{

    public static function getQRCode($data, $size = 200)
    {
        $urlencoded = urlencode($data);
        return 'https://chart.googleapis.com/chart?chs=' . $size . 'x' . $size . '&chld=M|0&cht=qr&chl=' . $urlencoded . '';
    }

}
