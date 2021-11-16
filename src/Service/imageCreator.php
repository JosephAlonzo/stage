<?php 
namespace App\Service;
use GDText\Box;
use GDText\Color;

class imageCreator
{
    private $fontFamily;
    private $fontSize;

    public function __construct( $fontFamily, $fontSize = null)
    {
        $this->fontFamily = $fontFamily;
        $this->fontSize = !$fontSize ? 35 : $fontSize;
       
    }

    public function createImageTable($table){
        
        $bodyImage   = $this->columns( $table['columns'], $table['options']['background']['columns'], $table['options']['images'] );
        $headerImage = $this->header(  $table['header'],  $table['options']['background']['header'] );
        $footerImage = $this->footer(  $table['footer'],  $table['options']['background']['footer'] );
        $arrowImage  = imagecreatefromjpeg( $table['options']['images']['arrow']);
        $widthArrow  = imagesx($arrowImage);

        $widthImage  = max( $bodyImage['width'] , $headerImage['width'] , $footerImage['width'] ) + $widthArrow;
        $heightImage = $bodyImage['height'] + $headerImage['height'] + $footerImage['height'];

        //I need to subtract 120 because the header has blank spaces after the title
        $headerImage['height'] -= 50;
        $planning = [ $headerImage,$bodyImage,$footerImage ];

        $outputImage = \imagecreatetruecolor( $widthImage , $heightImage) or die("Cannot Initialize new GD image stream");
        \imagefill($outputImage, 0, 0, \imagecolorallocate($outputImage, 255, 255, 255));

        $accumulatedHeight = 0;
        foreach ($planning as $i => $part) {
            $x = $part['type'] == 'body' ? (($widthImage - $part['width']) / 2) - 320 : 0;
            
            imagecopymerge($outputImage, $part['img'], $x, $accumulatedHeight, 0, 0, $part['width'], $part['height'], 100);
            $accumulatedHeight += $part['height'];
            if($i == 1){
                $accumulatedHeight += 50;
            }
        }

        imagejpeg($outputImage, "uploads/planning/planning.jpg");
        
    }

    public function header($header, $options){
        $imgHeader = isset( $options[0]  ) ? imagecreatefromjpeg( $options[0] ) : null;
        // Sizes of images columns
        $widthHeader =  $imgHeader ? imagesx($imgHeader): 0;
        $heightHeader = $imgHeader ? imagesy($imgHeader): 0;
        $headerY = $imgHeader ? ceil(($heightHeader ) / 2): 0;
        $headerX = $imgHeader ? ceil($widthHeader / 2): 0;

        if($imgHeader){
            $textbox = new Box($imgHeader);
            $textbox->setFontSize( $this->fontSize + 30 );
            $textbox->setFontFace( $this->fontFamily );
            $textbox->setFontColor( new Color(0, 0, 0) );
            $textbox->setBox(
                -320,  // distance from left edge
                $headerY,  // distance from top edge
                $widthHeader, // textbox width
                $heightHeader - 10  // textbox height
            );
            $textbox->setTextAlign('center', 'top');
            $textbox->draw( $header[0] );
        }

        return [ "img" => $imgHeader, "width" => $widthHeader, 'height' => $heightHeader, 'type' => 'header' ];
    }

    public function columns($columns, $options, $aditionalsOptions){
        // Specify font path   
        $rows = count( $columns );
        $countColumns = count( $columns[0] ) - 1;
        $imgColumn = isset( $options[0] ) ? imagecreatefromjpeg($options[0]) : null;
        $arrow = imagecreatefromjpeg($aditionalsOptions['arrow']);
        $newImage = imagecreatefrompng($aditionalsOptions['new']);
        $divisionImage = imagecreatefrompng($aditionalsOptions['division']);
        $test = [];
        //Size of arrow image
        $widthNew     = imagesx($newImage);
        $heightNew    = imagesy($newImage);
        $widthDivision = imagesx($divisionImage);
        $heightDivision = imagesy($divisionImage);
        $widthArrow   = imagesx($arrow);
        $heightArrow  = imagesy($arrow);
        // Sizes of images columns
        $widthColumn = imagesx($imgColumn);
        $heightColumn = imagesy($imgColumn);
        $columnY = ceil( $heightColumn / 2) - 50; 
        //divide in columns like bootstrap
        $col = ceil( $widthColumn / 12 );

        $widthImage  = $widthColumn;
        $heightImage = $heightColumn * $rows;
        //add minumun heightImage
        if( $rows < 6 ){
            $heightImage = $heightColumn * 6;
        }

        $outputImage = \imagecreatetruecolor( ($widthImage + $widthArrow), $heightImage) or die("Cannot Initialize new GD image stream");
        $white = \imagecolorallocate($outputImage, 255, 255, 255);
        \imagefill($outputImage, 0, 0, $white);

        $imagesColumns = [];
        foreach ($columns as $k => $row) {
            //Compare today to creation date to dacide if add new label
            $today = new \DateTime("now");
            $interval =  $today->diff($columns[$k][4])->d;
            $new = $interval < 30? true : false;
            //Select image background of row table
            $index = $k%2 == 0 ? 0: 1;
            $origin = imagecreatefromjpeg($options[$index]);
            if($new){
                imagecopy($origin, $newImage, ($col * 9.3), ($heightColumn/2)-50, 0, 0, $widthNew, $heightNew);
            }
            foreach ($row as $i => $column) {
                if( $i < 3 ){
                    $textbox = new Box($origin);
                    $textbox->setFontSize( $this->fontSize );
                    $textbox->setFontFace( $this->fontFamily );
                    $textbox->setFontColor( new Color(0, 0, 0) );
                    $textbox->setBox(
                        $i == 2 ? $col * 10 : ($col * 2) * $i, // distance from left edge
                        $columnY,           // distance from top edge
                        $i == 1 ? $col * 8 : $col * 2,           // textbox width
                        $heightColumn - 20  // textbox height
                    );
                    $textbox->setTextAlign('center', 'top');
                    $textbox->draw( $column );
                }
            }
            // Add new label
            if($columns[$k][3]){   
                $columnImage = \imagecreatetruecolor( ($widthImage + $widthArrow), $heightImage) or die("Cannot Initialize new GD image stream");
                $white = \imagecolorallocate($columnImage, 255, 255, 255);
                \imagefill($columnImage, 0, 0, $white);
                imagecopymerge($columnImage, $origin, 0, 0, 0, 0, $widthColumn, $heightColumn, 100);
                imagecopymerge($columnImage, $arrow, $widthColumn, ($heightColumn/2)-50, 0, 0, $widthArrow, $heightArrow, 100);
        
                $origin = $columnImage;
            }
            // Add day division 
            if($columns[$k][5]){
                $tmpWidth = $columns[$k][3] ? $widthImage + $widthArrow : $widthImage;
                $columnImage = \imagecreatetruecolor( $tmpWidth, ($heightImage + $heightDivision) ) or die("Cannot Initialize new GD image stream");
                $white = \imagecolorallocate($columnImage, 255, 255, 255);
                \imagefill($columnImage, 0, 0, $white);

                imagecopymerge($columnImage, $origin, 0,  $heightDivision-20, 0, 0, ($widthColumn+ $widthArrow), $heightColumn, 100);
                imagecopy($columnImage, $divisionImage, 0, 0, 0, 0, $widthDivision, $heightDivision);
                
                $origin = $columnImage;
            }

           
            array_push($imagesColumns, $origin);
        }
        
        
        foreach ($imagesColumns as $i => $image)
        { 
            imagecopymerge($outputImage, $image, 0, ($heightColumn * $i), 0, 0, imagesx($image), $heightColumn, 100);
        }

        return [ "img" => $outputImage, "width" => ($widthImage + $widthArrow), 'height' => $heightImage, 'type' => 'body' ];
    }

    public function footer($footer, $options){ 
        $imgFooter = isset( $options[0]  ) ? imagecreatefromjpeg( $options[0] ) : null;

        // Sizes of images columns
        $widthFooter =  $imgFooter ? imagesx($imgFooter): 0;
        $heightFooter = $imgFooter ? imagesy($imgFooter): 0;
        $footerY = $imgFooter ? ceil(($heightFooter ) / 2): 0;
        $footerX = $imgFooter ? ceil($widthFooter / 2): 0;
        
        if($imgFooter){
            $textbox = new Box($imgFooter);
            $textbox->setFontSize( $this->fontSize );
            $textbox->setFontFace( $this->fontFamily );
            $textbox->setFontColor( new Color(93, 94, 96) );
            $textbox->setBox(
                0,  // distance from left edge
                -35,  // distance from top edge
                $widthFooter, // textbox width
                $heightFooter // textbox height
            );
            $textbox->setTextAlign('center', 'bottom');
            if( isset($footer[0]) ){
                $textbox->draw( $footer[0] );
            }
        }

        return [ "img" => $imgFooter, "width" => $widthFooter, 'height' => $heightFooter, 'type' => 'footer' ];

    }




}
