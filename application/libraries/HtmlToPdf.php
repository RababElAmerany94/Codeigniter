<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HtmlToPdf
{
    var $globalOptions = array(
            'ignoreWarnings' => true,
            'no-outline',         // Make Chrome not complain
            'page-size' => 'A4',
            'commandOptions' => array(
                'useExec' => true,
                'procEnv' => array('LANG' => 'fr_FR.utf-8'),
            ),
            'disable-smart-shrinking',
    );
    var $html;
    var $css;
    var $path;
    var $filename;
    var $paper_size;
    var $orientation;

    /**
     * Constructor
     *
     * @access    public
     * @param    array    initialization parameters
     */
    function __construct($params = array())
    {
        $this->CI =& get_instance();

        if (count($params) > 0) {
            $this->initialize($params);
        }

        log_message('debug', 'PDF Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Initialize Preferences
     *
     * @access    public
     * @param    array    initialization parameters
     * @return    void
     */
    function initialize($params)
    {
        $this->clear();
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (isset($this->$key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set html
     *
     * @access    public
     * @param null $html
     * @return    void
     */
    function html($html = NULL)
    {
        $this->html = $html;
    }

    // --------------------------------------------------------------------

    /**
     * Set path
     *
     * @access    public
     * @param $path
     * @return    void
     */
    function folder($path)
    {
        $this->path = $path;
    }

    /**
     * Set path
     *
     * @access    public
     * @param $filename
     * @return    void
     */
    function filename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Set path
     *
     * @access    public
     * @param $filename
     * @return    void
     */
    function AddCssFile($filename)
    {
        $this->css[] = $filename;
    }

    /**
     * Create PDF
     *
     * @access          public
     * @param array     $params
     * @return          void
     */
    public function setOptions($params = array())
    {
        $this->globalOptions = array_merge($this->globalOptions, $params);
    }

    /**
     * Create PDF
     *
     * @access          public
     * @param string $mode
     * @return          null|string|boolean
     */
    public function create($mode = 'display')
    {
        if (is_null($this->html)) {
            show_error("HTML is not set");
        }

        if ($mode == "save" && is_null($this->path)) {
            show_error("Path is not set");
        }

        // Load the wkhtmltopdf libary
        require_once("phpwkhtmltopdf/Pdf.php");

        // Load the wkhtmltopdf library
        $pdf = new Pdf();
        $pdf->setOptions($this->globalOptions);
        $pdf->addPage($this->html);

        switch ($mode) {
            case 'save':
                if (!$pdf->saveAs($this->path . $this->filename)) {
                    $error = $pdf->getError();
                    show_error("PDF could not be written to the path: {$error}");
                }
                break;
            case 'display':
                if (!$pdf->send()) {
                    $error = $pdf->getError();
                    show_error("PDF could not be show up inline: {$error}");
                }
                break;
            case 'download':
                if (!$pdf->send($this->filename)) {
                    $error = $pdf->getError();
                    show_error("PDF could not be download: {$error}");
                }
                break;
            default:
                if (!$pdf->send()) {
                    $error = $pdf->getError();
                    show_error("PDF could not be show up inline: {$error}");
                }
        }

        log_message('debug', 'PDF Class Completed');
    }
}

/* End of file Html2pdf.php */
