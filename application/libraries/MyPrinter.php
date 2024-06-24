<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MyPrinter extends Escpos\Printer
{

    public function setText($text, $mode = Escpos\Printer::MODE_FONT_A, $feed = 1)
    {
        $this->selectPrintMode($mode);
        // $printer->setUnderline(Escpos\Printer::UNDERLINE_DOUBLE);
        $this->text($text);
        $this->feed($feed);
    }

    // Print image inline. If it is a multi-line image, then only the first line is printed!
    public function inlineImage(Escpos\EscposImage $img, $size = Escpos\Printer::IMG_DEFAULT)
    {
        $highDensityVertical = !(($size & self::IMG_DOUBLE_HEIGHT) == Escpos\Printer::IMG_DOUBLE_HEIGHT);
        $highDensityHorizontal = !(($size & self::IMG_DOUBLE_WIDTH) == Escpos\Printer::IMG_DOUBLE_WIDTH);
        // Header and density code (0, 1, 32, 33) re-used for every line
        $densityCode = ($highDensityHorizontal ? 1 : 0) + ($highDensityVertical ? 32 : 0);
        $colFormatData = $img->toColumnFormat($highDensityVertical);
        $header = Escpos\Printer::dataHeader(array($img->getWidth()), true);
        foreach ($colFormatData as $line) {
            // Print each line, double density etc for printing are set here also
            $this->connector->write(self::ESC . "*" . chr($densityCode) . $header . $line);
            break;
        }
    }

    // public function setUserDefinedCharacter(Escpos\EscposImage $img, $char)
    // {
    //     $verticalBytes = 3;
    //     $colFormatData = $img->toColumnFormat(true);
    //     foreach ($colFormatData as $line) {
    //         // Print each line, double density etc for printing are set here also
    //         $this->connector->write(self::ESC . "&" . chr($verticalBytes) . $char . $char . chr($img->getWidth()) . $line);
    //         break;
    //     }
    // }

    // public function selectUserDefinedCharacterSet($on = true)
    // {
    //     self::validateBoolean($on, __FUNCTION__);
    //     $this->connector->write(self::ESC . "%" . ($on ? chr(1) : chr(0)));
    // }
}
