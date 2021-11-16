<?php
namespace App\Utils;

class DocumentTCPDF extends \TCPDF {

    private $_options;

	function __construct($options)
	{
		parent::__construct();
		$this->_options = $options;
	}

    //Page header
    public function Header() {
    }

    // Page footer
    public function Footer() {

        $this->SetY(-25);

        $y = $this->getY();

        // Image example with resizing
        // Image( filename, left, top, width, height, type, link, align, resize, dpi, align, ismask, imgmask, border, fitbox, hidden, fitonpage)
        $this->Image($this->_options['imageFooter'], 20, $y-20, 24, 0, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);

        $this->SetFont('helvetica', 'B', 9);
        $this->MultiCell(0, 4, 'C.D.O.S. ' . $this->_options['companyApp']->getTenant()->getCdosName()  , 0, 'C', 0, 1, '', $y-10, true);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(0, 4, $this->_options['companyApp']->getTenant()->getAddress() . ' — '. $this->_options['companyApp']->getTenant()->getCity()->getPostalCode() . " " . strtoupper($this->_options['companyApp']->getTenant()->getCity()->getName()) ." Tél.: " . $this->_options['companyApp']->getTenant()->getPhoneNumber(), 0, 'C', 0, 1, '', '', true);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(0, 4, 'Courriel: '. $this->_options['companyApp']->getTenant()->getEmail() .' + Site Internet: ' . $this->_options['companyApp']->getTenant()->getSiteInternet(), 0, 'C', 0, 1, '', '', true);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell(0, 4, 'Numéro SIRET: ' . $this->_options['companyApp']->getTenant()->getSiret() . ' + Code APE: ' . $this->_options['companyApp']->getTenant()->getCodeApe() , 0, 'C', 0, 1, '', '', true);
    }
}

?>