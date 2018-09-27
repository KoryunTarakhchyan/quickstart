<?php

class Atlas_Model_PDFMapper
{
	protected $_pdf_handler;
	
	public function __construct( $mode_1, $mode_2, $extra )
	{
        require_once("C:\\xampp\\htdocs\\quickstart\\library\\MPDF57\\mpdf.php");

        if( is_array($mode_2) ) {
            $this->_pdf_handler = new mPDF(
                $mode_1,
                $mode_2, // SIZE
                0,       // default font size
                '',      // default font family
                0,       // margin_left
                0,       // margin right
                0,       // margin top
                0,       // margin bottom
                0,       // margin header
                0,       // margin footer
                $extra   // orientation
            );
        } else {
            $this->_pdf_handler = new mPDF(
                $mode_1,
                $mode_2, // UNITS
                0,    // default font size
                '',   // default font family
                0,    // margin_left
                0,    // margin right
                0,    // margin top
                0,    // margin bottom
                0,    // margin header
                0     // margin footer
            );
            $this->_pdf_handler->SetDisplayMode($extra);
        }
	}


    public function setDir( $dir )
    {
        $this->_dir = $dir;
    }


    public function addContent( $data )
    {
        $this->_pdf_handler->WriteHTML($data);
    }


    public function addCSSFile( $file_name )
    {
        $stylesheet = file_get_contents($file_name);
        $this->_pdf_handler->WriteHTML($stylesheet, 1);
    }
	
	
	public function setChinese()
	{
            $this->_pdf_handler->useAdobeCJK = true;
            $this->_pdf_handler->SetAutoFont(AUTOFONT_ALL);
    
        }
    
	public function outputPDF()
	{
        $this->_pdf_handler->Output();
	}

	public function outputPDFChinese()
	{
            $this->_pdf_handler->Output();
	}

	public function writeBarcode($text, $x , $y , $size , $hieght , $code)
	{
//        $this->_pdf_handler->writeBarcode('0010013547',0,0,50,1.5);
        $this->_pdf_handler->WriteBarcode2($text, $x, $y, $size, $hieght, false, false, $code);
	}
        
    public function outputPDFtoFile( $file_name )
    {
        $this->_pdf_handler->Output($file_name, 'F');
    }


    public function addImage( $path, $margin_x, $margin_y, $width, $height, $ext, $paint, $constrain )
    {
        $this->_pdf_handler->Image($path, $margin_x, $margin_y, $width, $height, $ext, $paint, $constrain);
    }
	
}

?>