<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "info@clearfeather.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "daf984" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'C60A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WEMYQximMLQii4m0srYyhDJMdUASC2gUaWR0dAgIQBZrEGlgbQh0EEFyX9SqaWFLV0VmTUNyX0CDaCuSOpjeRteGwNAQNDscHR1R1EHcwogiBnEzqthAhR8VIRb3AQCi58umc0m2IQAAAABJRU5ErkJggg==',
			'CF2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WENEQx1CGUNDkMREWkUaGB0dHZDVBTSKNLA2BKKKNYgASbgY2ElRq6aGrVqZGZqF5D6wulZGTL1TGDHsYAhAFQO7xQFVjDUE6JZQVLcMVPhREWJxHwAPdMl3WUZoYQAAAABJRU5ErkJggg==',
			'4021' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEMIQytKKIhTCGMDo6TEUWYwxhbWVtCAhFFmOdItLo0BAA0wt20rRp01Zmrcxaiuy+AJC6VlQ7QkOBYlPQ7J3C2gp0DZoY0C0O6GIMAayhAaEBgyH8qAexuA8Aj0fK/PT1N8UAAAAASUVORK5CYII=',
			'FD3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGaYGIIkFNIi0sjY6BIigijU6NAQ6sKCLNTo6ILsvNGrayqypK7OQ3YemDsU8bGJodmBxC6abByr8qAixuA8AzkvOVWuKY9gAAAAASUVORK5CYII=',
			'A389' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGaY6IImxBoi0Mjo6BAQgiYlMYWh0bQh0EEESC2hlAKpzhImBnRS1dFXYqtBVUWFI7oOoc5iKrDc0FGReQAOaeSAxNDsw3RLQiunmgQo/KkIs7gMAwgDMOzBjhRQAAAAASUVORK5CYII=',
			'4BD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGsIYyhDogi4WItLI2OjoEIIkxhog0ujYENIggibFOAaoDigUguW/atKlhS1dFLc1Ccl8AqjowDA3FNI9hClYxDLdgdfNAhR/1IBb3AQBDNM4AZcNOWAAAAABJRU5ErkJggg==',
			'047F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWllDA0NDkMRYAximMjQEOiCrE5nCEIouFtDK6MrQ6AgTAzspaunSpauWrgzNQnJfQKtIK8MURjS9oqEOAYzodrQyOqCKAd3SytqAKgZ2M5rYQIUfFSEW9wEAPr7IuTV5sGoAAAAASUVORK5CYII=',
			'B5B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGaY6IIkFTBFpYG10CAhAFmsFijUEOoigqgthbXSEiYGdFBo1denS0FVRYUjuC5jC0Oja6DAVRW8rUKwhoAFVTAQkhmYHayu6W0IDGEPQ3TxQ4UdFiMV9APSszoHsCfPZAAAAAElFTkSuQmCC',
			'7E0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMIaGIIu2ijQwhDI6MKCJMTo6oopNEWlgbQiEiUHcFDU1bOmqyNAsJPcxOqCoA0PWBkwxkQZMOwIaMN0S0AB2M6pbBij8qAixuA8A9c/IylgP2dEAAAAASUVORK5CYII=',
			'0A38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGaY6IImxBjCGsDY6BAQgiYlMYW1laAh0EEESC2gVaXRAqAM7KWrptJVZU1dNzUJyH5o6qJhoqAOaeSJTgOrQxFgDRBpd0fQyOog0OqK5eaDCj4oQi/sAcfrNgWVQGP0AAAAASUVORK5CYII=',
			'05D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGaY6IImxBog0sDY6BAQgiYlMAYo1BDqIIIkFtIqEsDYEwNSBnRS1dOrSpauipmYhuS+glaHRFaEOSQzVPKAdGGKsAayt6G5hdGAMQXfzQIUfFSEW9wEABAfM6e2wfpQAAAAASUVORK5CYII=',
			'1212' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjdWBtZQhhCAhAEhN1EGl0DGF0EEHRy9DoMIWhQQTJfSuzVi1dNW3Vqigk9wHVTQHCRgdUvQFAsVY0t0BUooixNgBFApDFRENEQx2BMGQQhB8VIRb3AQD4+sjWwl+QVwAAAABJRU5ErkJggg==',
			'A97C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwEbQlvkIHMBo9EGkZgiqTwBjBCCpiSlA5QgsDfXfF/Mu2XS/SnvOLHyqNErHBMIEYJCI6FJWRNg3aOwSrLvXq/qZQyl232fjAedGH1uzFSVrQM1tU2Pm2ISaLGpfaNlTXOX/3vwdz4HfqgzAp0hhPsAAAAAElFTkSuQmCC',
			'AB8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaYGIImxBoi0Mjo6BIggiYlMEWl0bQh0YEESC2gFqXN0QHZf1NKpYatCV2Yhuw9NHRiGhkLMY0A1D4cdqG4JaMV080CFHxUhFvcBAEJty8eU/7bNAAAAAElFTkSuQmCC',
			'F6E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMDkMQCGlhbWRsYHRhQxEQasYg1AMVcHZDcFxo1LWxp6MqoKCT3BTSIAs1jAKpGNc8VqxijA6oYyC0MAajuA7nZYarDIAg/KkIs7gMAp9nMCX56iDYAAAAASUVORK5CYII=',
			'C165' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUMDkMREWhkDGB0dHZDVBTSyBrA2oIk1MADFGF0dkNwXBURLp66MikJyH1ido0ODCIbeAFSxRpBYoIMIilsYgG5xCEB2H2sIayhDKMNUh0EQflSEWNwHAJtLyYiomEhzAAAAAElFTkSuQmCC',
			'7AE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHUIdkEVbGUNYGxgdAlDEWFtZgbQIstgUkUZXIB2A7L6oaStTQ1ctzUJyH6MDijowZG0QDXVFM0+kAaIOWSwALIbqFrAYupsHKPyoCLG4DwBplszE4n/I+QAAAABJRU5ErkJggg==',
			'BD30' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGVqRxQKmiLSyNjpMdUAWaxVpdGgICAhAVdfo0OjoIILkvtCoaSuzpq7MmobkPjR1SOYFYhHDsAPDLdjcPFDhR0WIxX0AUwfPhFwj0jwAAAAASUVORK5CYII=',
			'0E25' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUMDkMRYA0QaGB0dHZDViUwRaWBtCEQRC2gVAZKBrg5I7otaOjVs1crMqCgk94HVtQLNQNc7BVUMZAdDAKMDshjYLQ4MAcjuA7mZNTRgqsMgCD8qQizuAwA7hsnwVo+ZWAAAAABJRU5ErkJggg==',
			'31F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA1qRxQKmMAawNjBMRVHZygoSC0URm8IAEoPpBTtpZdSqqKWhq5aiuA9VHdQ84sQCsOgVBboY5JaAQRB+VIRY3AcAGSPI7fm349oAAAAASUVORK5CYII=',
			'B974' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDAxoCkMQCprC2AslGFLFWkUYHIImqDijW6DAlAMl9oVFLl2YtXRUVheS+gCmMgQ5TGB1QzWNodAhgDA1BEWNpdHRgwHALawOqGNjNaGIDFX5UhFjcBwD9aM/J+0YXkwAAAABJRU5ErkJggg==',
			'7E0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEwNQBZtFWlgCGUIEEETY3R0dGBBFpsi0sDaEOiA4r6oqWFLV0VmIbuP0QFFHRiyNmCKiTRg2hHQgOmWgAYsbh6g8KMixOI+ALGPykx2sQkxAAAAAElFTkSuQmCC',
			'0752' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxBjA0ujYwBAQgiYlMAYkxOoggiQW0MrSyTgXKIbkvaumqaUszs1ZFIbkPqC4ASDY6oOhldADLoNjB2sDaEDCFAcUtIg2Mjg4BqG4G2hjKGBoyCMKPihCL+wDE18usXdB01QAAAABJRU5ErkJggg==',
			'487C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37pjCGsIYGTA1AFgthbWVoCAgQQRJjDBFpdGgIdGBBEmOdAlTX6OiA7L5p01aGrVq6MgvZfQEgdVMYHZDtDQ0FmheAKsYwRQRoGiOKHQxAvawNDChuAbu5gQHVzQMVftSDWNwHAOeYywwoFkgsAAAAAElFTkSuQmCC',
			'2F83' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUIdkMREpog0MDo6OgQgiQW0ijSwNgQ0iCDrbgWpc2gIQHbftKlhq0JXLc1Cdl8AijowZHTANI+1AVNMpAHTLaGhQBVobh6o8KMixOI+AOwwy/xPu/SlAAAAAElFTkSuQmCC',
			'7D98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIu2irQyOjoEBKCKNbo2BDqIIItNAYkFwNRB3BQ1bWVmZtTULCT3MTqINDqEBKCYx9oAFEMzTwQo5ogmFtCA6ZaABixuHqDwoyLE4j4A+tTM7nFqm50AAAAASUVORK5CYII=',
			'0241' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mIouJTBFpdJjqEIosFtAK1BkI1wt2UtTSVUtXZmYtRXYfUN0UVjQ7gGIBrKEBrah2MDpgcUsDuhijg2ioQ6NDaMAgCD8qQizuAwB7/Mx07hH3FQAAAABJRU5ErkJggg==',
			'B835' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUMDkMQCprC2sjY6OiCrC2gVaXRoCEQVA6pjaHR0dUByX2jUyrBVU1dGRSG5D6LOoUEEw7wALGKBDiIYbnEIQHYfxM0MUx0GQfhREWJxHwA/Ac4JRLbt6AAAAABJRU5ErkJggg==',
			'5F8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMDkMQCGkQaGB0dHRjQxFgbAlHEAgNQ1IGdFDZtatiq0JWhWcjua8U0DySGbl4AFjGRKZh6WYH2MqC5eaDCj4oQi/sAc+DJ3SdXNXoAAAAASUVORK5CYII=',
			'64C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHUMDkMREpjBMZXQIdEBWF9DCEMraIIgq1sDoygrEDkjui4xaunTpqpVRUUjuC5ki0soKMhdZb6toqCuGGEMryA5kMaBbWhkdAgKQ3Qdxs8NUh0EQflSEWNwHANFEy1Dx2tpfAAAAAElFTkSuQmCC',
			'BD39' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGaY6IIkFTBFpZW10CAhAFmsVaXRoCHQQQVXX6NDoCBMDOyk0atrKrKmrosKQ3AdR5zBVBMO8gAYsYuh2YLgFm5sHKvyoCLG4DwAhqM9n9mJmRgAAAABJRU5ErkJggg==',
			'72CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMDkEVbWVsZHQIdUFS2ijS6Ngiiik1hAIoxwsQgbopatXTpqpWhWUjuA6qYwopQB4asDQwB6GIiQD4rmh0BDSBVgWhioqEO6G4eoPCjIsTiPgC/2sl46QsiWwAAAABJRU5ErkJggg==',
			'8EAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIaGIImJTBFpYAhldEBWF9Aq0sDo6IgiBlLH2hAIEwM7aWnU1LClqyJDs5Dch6YObh5rKBYxNHXY9ILcjC42UOFHRYjFfQCQZMo1nBcv5QAAAABJRU5ErkJggg==',
			'A207' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ2AMAwEP0U2CPs4Bb2LuGEEpggFG3iFFGRKQkUsKEHgl1ycbOtk1Etl/Cmv+DlyCeokdcyzXyHIoWNBwxIjGcYrljFzy+k3lVpKnba582tz6o/e7YqAG1OYe80mElvmM8SRZYOQWvbV/x7Mjd8OCWrMEeZHJA0AAAAASUVORK5CYII=',
			'9691' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMRRYLaBVpZG0ICEUTawCKwfSCnTRt6rSwlZlRS5Hdx+oq2soQEoBiBwPQPIcGVDEBoJgjmhjULShiUDeHBgyC8KMixOI+AGUPy5rXjGMxAAAAAElFTkSuQmCC',
			'872E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMDkMREpjA0Ojo6OiCrC2hlaHRtCEQRA6prZUCIgZ20NGrVtFUrM0OzkNwHVBfA0MqIZh6QPwVdjLWBIYARzQ6RBkYHVDHWAJEG1tBAFDcPVPhREWJxHwA8xcmgGZUfqQAAAABJRU5ErkJggg==',
			'47D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37poiGuoYytKKIhTA0ujY6THVAEmMEiTUEBAQgibFOYWhlbQh0EEFy37Rpq6YtXRWZNQ3JfQFTGAKQ1IFhaCijA7oYwxTWBlY0OximiDSworkFLIbu5oEKP+pBLO4DAPOezMouzzU2AAAAAElFTkSuQmCC',
			'7D9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUNDkEVbRVoZHR0dGFDFGl0bAlHFpqCIQdwUNW1lZmZkaBaS+xgdRBodQlD1sjYAxdDMEwGKOaKJBTRguiWgAexmVLcMUPhREWJxHwC7yMo2ZGcO7wAAAABJRU5ErkJggg==',
			'2AD6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaY6IImJTGEMYW10CAhAEgtoZW1lbQh0EEDW3SrS6AoUQ3HftGkrU1dFpmYhuy8ArA7FPEYH0VCQXhFktzRAzEMWEwGJobklNBQohubmgQo/KkIs7gMAn+PM3p5g908AAAAASUVORK5CYII=',
			'E3BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUMdkMQCGkRaWRsdHQJQxBgaXRsCHURQxcDqRJDcFxq1Kmxp6MqsaUjuQ1OHzzwsYphuwebmgQo/KkIs7gMAPYDNEvgW51AAAAAASUVORK5CYII=',
			'79C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHaY6IIu2srYyOgQEBKCIiTS6Ngg6iCCLTQGJMcLEIG6KWro0FUiGIbmP0YEx0LWBYSqyXtYGBqBehgZkMZEGFqCYAIodAQ2YbglowOLmAQo/KkIs7gMAtkLL7tyYHlMAAAAASUVORK5CYII=',
			'4887' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjCGMIQyhoYgi4WwtjI6OjSIIIkxhog0ujYEoIixToGoC0By37RpK8NWha5amYXkvgCIulZke0NDweZNQXULWCwAVQyk19EBi5tRxQYq/KgHsbgPAEYqy2G7pWI2AAAAAElFTkSuQmCC',
			'4EB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37poiGsoYyTHVAFgsRaWBtdAgIQBJjBIk1BDqIIImxTgGpc4SJgZ00bdrUsKWhq6LCkNwXAFbnMBVZb2goyLyABhEUt4DFHDDE0NyC1c0DFX7Ug1jcBwDrC8wmKARZHgAAAABJRU5ErkJggg==',
			'C4DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYWllDGUMDkMREWhmmsjY6OiCrC2hkCGVtCEQVa2B0RRIDOylq1dKlS1dFhmYhuS8AaCKmXtFQV3SxRgYMdUC3tKK7BZubByr8qAixuA8AmtTK+bvohIEAAAAASUVORK5CYII=',
			'3D7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RANEQ1hDA0MdkMQCpoi0MjQEOgQgq2wVaXQAiokgi00BijU6wtSBnbQyatrKrKUrQ7OQ3QdSN4UR07wARlTzgGKODqhiILewNqDqBbu5gRHFzQMVflSEWNwHAG2CzCPNFNqtAAAAAElFTkSuQmCC',
			'832C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaYGIImJTBFpZXR0CBBBEgtoZWh0bQh0YEFRx9DKABRDdt/SqFVhq1ZmZiG7D6yuldGBAc08hylYxAIY0ewQAelEcQvIzayhAShuHqjwoyLE4j4AdKzKw/Xft0YAAAAASUVORK5CYII=',
			'3AC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHVqRxQKmMIYwOgRMRVHZytrK2iAQiiI2RaTRFSiD7L6VUdNWpq5atRTFfajqoOaJhmKKgdQJoLlFpNHRIQBFTDRApNEh1CE0YBCEHxUhFvcBAEAkzJLrMBpDAAAAAElFTkSuQmCC',
			'D457' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYWllDHUNDkMQCpjBMZQXSIshirQyhmGKMrqxTgTSS+6KWAkFm1sosJPcFtIq0gk1A0Ssa6gCyCdWOVtaGgAAGVLe0Mjo6OqC7mSGUEUVsoMKPihCL+wBsIsz42sHRTwAAAABJRU5ErkJggg==',
			'161B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMIY6IImxOrC2MoQwOgQgiYk6iDQyAsVEUPQCeVPg6sBOWpk1LWzVtJWhWUjuY3QQbUVSB9Pb6DAFwzwsYqwYekVDgC4JdURx80CFHxUhFvcBAHUTx8w8yyK0AAAAAElFTkSuQmCC',
			'609B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0sLayNgQ6iCCLNYg0ugLFApDcFxk1bWVmZmRoFpL7QqaINDqEBKKa1woUQzevlbWVEU0Mm1uwuXmgwo+KEIv7APoHy0AT3bOhAAAAAElFTkSuQmCC',
			'BE13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIQ6IIkFTBFpYAhhdAhAFmsVaWAMYWgQQVc3BUgjuS80amrYqmmrlmYhuQ9NHdw8kJgIITGwXlS3gNzMGOqA4uaBCj8qQizuAwCq9c127KWr0AAAAABJRU5ErkJggg==',
			'D862' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtcHQQQRFjbWUF0iJI7otaujJs6VQgjeQ+sDpHh0YHDPMCWhkwxaYwYHELppsZQ0MGQfhREWJxHwCVGs4XIydDowAAAABJRU5ErkJggg==',
			'082D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbSytjIgxMBOilq6MmzVysysaUjuA6trZUTTK9LoMAVVDGSHQwCqGNgtDowobgG5mTU0EMXNAxV+VIRY3AcAm47KGLtbdAUAAAAASUVORK5CYII=',
			'B7C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHVqRxQKmMDQ6OgRMdUAWa2VodG0QCAhAVdfK2sDoIILkvtCoVdOWrlqZNQ3JfUB1AUjqoOYxOmCKsTawYtghAlSF6pbQAKAuNDcPVPhREWJxHwDxPc1yZBDWyAAAAABJRU5ErkJggg==',
			'B708' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMEx1QBILmMLQ6BDKEBCALNbK0Ojo6OgggqqulbUhAKYO7KTQqFXTlq6KmpqF5D6gugAkdVDzGB1YGwJRzQOaxohhB5CH5pbQAKAYmpsHKvyoCLG4DwAzac2oSAvhjQAAAABJRU5ErkJggg==',
			'4862' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGMIQyTHVAFgthbWV0dAgIQBJjDBFpdG1wdBBBEmOdwtrKCqRFkNw3bdrKsKVTV62KQnJfAEido0Mjsh2hoSDzAlpR3QIWm4IqBnELppsZQ0MGQ/hRD2JxHwAFH8wp+qtz9gAAAABJRU5ErkJggg==',
			'73B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDGVpRRFtFWlkbHaaiijE0ujYEhKKITWEAqYPphbgpalXY0tBVS5Hdx+iAog4MWRvA5qGIiWARC2gQwdAb0AB2c2jAIAg/KkIs7gMAjl7MrdR20s0AAAAASUVORK5CYII=',
			'A84F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUNDkMRYA1hbGVodHZDViUwRaXSYiioW0ApUFwgXAzspaunKsJWZmaFZSO4DqWNtRNUbGirS6BoaiGYe0I5GLHZgiIHdjCI2UOFHRYjFfQC478s2zghJXQAAAABJRU5ErkJggg==',
			'912F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUNDkMREpjAGMDo6OiCrC2hlDWBtCEQTA+pFiIGdNG3qqqhVKzNDs5Dcx+oKVNfKiKKXAaR3CqqYAEgsAFVMZApIBFUM6JJQ1lBUtwxU+FERYnEfAEHixl86o6pWAAAAAElFTkSuQmCC',
			'D228' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtCHQQQRFjaHRoCICpAzspaumqpatWZk3NQnIfUN0UhlYGNPMYAhimMKKZx+jAEIAmNoW1ASSKrDc0QDTUNTQAxc0DFX5UhFjcBwDHic1SfnnEqAAAAABJRU5ErkJggg==',
			'AD45' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUMDkMRYA0RaGVodHZDViUwRaXSYiioW0AoUC3R0dUByX9TSaSszMzOjopDcB1Ln2ujQIIKkNzQUKAa0VQTdvEZHBzSxVoZGh4AAFDGQmx2mOgyC8KMixOI+AM9ezfUDeaWvAAAAAElFTkSuQmCC',
			'417F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjAEsIYGhoYgi4UwBjA0BDogq2MMYcUQYwXqZWh0hImBnTRt2qqoVUtXhmYhuS8ApG4KI4re0FCgWACqGMgtjA6YYqwN6GKsoRhiAxV+1INY3AcAK8THHk5BsK4AAAAASUVORK5CYII=',
			'E3C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNYQxhCHUMDkMQCGkRaGR0CHRhQxBgaXRsE0cVaWRsYXR2Q3BcatSps6aqVUVFI7oOoY2gQwTAPm5igA6oYyC0BAcjug7jZYarDIAg/KkIs7gMAV23MfnHP7o0AAAAASUVORK5CYII=',
			'B395' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMDkMQCpoi0Mjo6OiCrC2hlaHRtCEQVm8LQytoQ6OqA5L7QqFVhKzMjo6KQ3AdSxxAS0CCCZp5DA6aYI9AOEQy3OAQguw/iZoapDoMg/KgIsbgPAM39zODguaJ2AAAAAElFTkSuQmCC',
			'C48B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYWhlCGUMdkMREWhmmMjo6OgQgiQU0MoSyNgQ6iCCLNTC6IqkDOylq1dKlq0JXhmYhuS8AaCKGeQ2ioa7o5jUytKLbAXQLhl5sbh6o8KMixOI+AMDDyx3iF5+dAAAAAElFTkSuQmCC',
			'09CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHUMdkMRYA1hbGR0CHQKQxESmiDS6Ngg6iCCJBbSCxBhhYmAnRS1dujR11cqsaUjuC2hlDERSBxVjaEQXE5nCgmEHNrdgc/NAhR8VIRb3AQBsVcrDlVhVygAAAABJRU5ErkJggg==',
			'9AC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHaY6IImJTGEMYXQICAhAEgtoZW1lbRB0EEERE2l0bWCAqQM7adrUaStTV62amoXkPlZXFHUQ2Coa6trAiGKeANg8VDtEpog0OqK5hTVApNEBzc0DFX5UhFjcBwCDJMylOeENxQAAAABJRU5ErkJggg==',
			'CEC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQxlCHaY6IImJtIo0MDoEBAQgiQU0ijSwNgg6iCCLNYDEGGDqwE6KWjU1bOmqVVOzkNyHpg5JjBHVPCx2YHMLNjcPVPhREWJxHwA8Fsw2bQQMJQAAAABJRU5ErkJggg==',
			'A4EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDHUMDkMRYAximsoJkkMREpjCEoosFtDK6IomBnRS1FAhCV4ZmIbkvoFWkFV1vaKhoqCuGeQwY6nCKobl5oMKPihCL+wBkjsl4hAKtCAAAAABJRU5ErkJggg==',
			'C1A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhimMEx1QBITaWUMYAhlCAhAEgtoZA1gdHR0EEEWa2AIYG0IhImBnRQFREtXRUWFIbkPoi5gKobe0IAGFLFGsDoUO0RawWIobmENYQ0FmYfs5oEKPypCLO4DADb4yuVMfJ4TAAAAAElFTkSuQmCC',
			'998B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iKCJOSLUgZ00berSpVmhK0OzkNzH6soY6IhmHkMrA4Z5Aq0sGGLY3ILNzQMVflSEWNwHANzvyw/1Ek7RAAAAAElFTkSuQmCC',
			'7FB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DGaY6IIu2ijSwNjoEBKCLNQQ6CCCLTQGpc3RAcV/U1LCloStTs5Dcx+gAVodiHmsDxDwRJDERLGIBDZhuAYuhu3mAwo+KEIv7ACgLzErcJvUJAAAAAElFTkSuQmCC',
			'3227' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUNDkMQCprC2Mjo6NIggq2wVaXRtCEAVm8LQ6AAUC0By38qoVUtXrcxamYXsvilA2AqEKOYxBIDFUcQYHYCiAQyobmlgdACKo7hZNNQ1NBBFbKDCj4oQi/sA/IXK3MNoIL4AAAAASUVORK5CYII=',
			'E556' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHaY6IIkFNIg0sDYwBARgiDE6CKCKhbBOZXRAdl9o1NSlSzMzU7OQ3Ac0p9GhIRDNPLCYgwiqeY2uGGKsrYyODih6Q0MYQxhCGVDcPFDhR0WIxX0AGBTM/Z6MNOMAAAAASUVORK5CYII=',
			'E091' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mooqxtrI2BISiiok0ujYEwPSCnRQaNW1lZmbUUmT3gdQ5hAS0out1aEAXY21lxBADuwVFDOrm0IBBEH5UhFjcBwDxQMzdjepkygAAAABJRU5ErkJggg==',
			'2299' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEHW3cqALAZx07RVS1dmRkWFIbsvgGEKQ0jAVGS9jA5A0YaABmQxVqAoY0MAih0iIFE0t4SGioY6oLl5oMKPihCL+wDrDcssV0sMsAAAAABJRU5ErkJggg==',
			'DB89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtCHQQQRUDqnOEiYGdFLV0atiq0FVRYUjug6hzmCqCYV5AAxYxVDuwuAWbmwcq/KgIsbgPAB/SzdQhr6WTAAAAAElFTkSuQmCC',
			'A18E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMDkMRYAxgDGB0dHZDViUxhDWBtCEQRC2hlQFYHdlLU0lVRq0JXhmYhuQ9NHRiGhjJgNY+AHVAx1lB0Nw9U+FERYnEfAHRtx+pa53IsAAAAAElFTkSuQmCC',
			'34AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYWhmmMIaGIIkFTGGYyhDK6ICishUo4uiIKjaF0ZW1IRAmBnbSyqilS5euigzNQnbfFJFWJHVQ80RDXUPRxRgw1AHdgiEGcjOGeQMUflSEWNwHAPTzyb+6xeQIAAAAAElFTkSuQmCC',
			'2778' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6Y6IImJTGFodGgICAhAEgtoBYkFOogg625lAInC1EHcNA0Il66amoXsvgAgnMKAYh6jA6MDQwAjinmsQAgSRxYTAULWBlS9oaFgMRQ3D1T4URFicR8AIj7LwIi4BsIAAAAASUVORK5CYII=',
			'AA9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGEMYHR2mOiCJiUxhbWVtCAgIQBILaBVpdG0IdBBBcl/U0mkrMzMjs6YhuQ+kziEErg4MQ0NFQx0aAkND0MxzbEBVBxZzdMQQcwhlRBEbqPCjIsTiPgBNrMyxeSMUwwAAAABJRU5ErkJggg==',
			'78CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMDkEVbWVsZHQIdUFS2ijS6Ngiiik1hbWVtYISJQdwUtTJs6aqVoVlI7mN0QFEHhqwNIPNQxUQaMO0IaMB0S0ADFjcPUPhREWJxHwDtBsmwjmMGmgAAAABJRU5ErkJggg==',
			'163F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGUNDkMRYHVhbWRsdHZDViTqINDI0BDqg6hVpYECoAztpZda0sFVTV4ZmIbmP0UG0lQHNPKDeRgdM87CIYXFLCNjNKGIDFX5UhFjcBwBXEMeB5nUxkQAAAABJRU5ErkJggg==',
			'7FD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DGUIdkEVbRRpYGx0dAtDFGgIaRJDFpkDEApDdFzU1bOmqqKVZSO5jdEBRB4asDZjmiWARA/HQ3QIWQ3fzAIUfFSEW9wEAxuXNjy8WlsUAAAAASUVORK5CYII=',
			'5613' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMIQ6IIkFNLC2MoQwOgSgiIk0AlU2iCCJBQYAeVNAcgj3hU2bFrZq2qqlWcjuaxVtRVIHFRNpdJiCal4AFjGRKUC3TEF1C2sAYwhjqAOKmwcq/KgIsbgPAHOOzGy9d/SzAAAAAElFTkSuQmCC',
			'4F07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGOkxhDA1BFgsRaWAIZWgQQRJjBIoxOjqgiLFOEWlgbQgAQoT7pk2bGrZ0VdTKLCT3BUDUtSLbGxoKFpuC6hawHQHoYgyhjA4YYlPQxAYq/KgHsbgPAGiZy2psBnanAAAAAElFTkSuQmCC',
			'38B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUMDkMQCprC2sjY6OqCobBVpdG0IRBWDqHN1QHLfyqiVYUtDV0ZFIbsPrM6hQQTDvAAsYoEOIhhucQhAdh/EzQxTHQZB+FERYnEfAOOozCmJGciGAAAAAElFTkSuQmCC',
			'F0BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUMdkMQCGhhDWBsdHQJQxFhbWRsCHURQxEQaXYHqRJDcFxo1bWVq6MqsaUjuQ1OHEMMwD5sd2NyC6eaBCj8qQizuAwD8E8zl+lb34QAAAABJRU5ErkJggg==',
			'69A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQximMLQii4lMYW1lCGWYiiwW0CLS6OjoEIoi1iDS6NoQANMLdlJk1NKlqauiliK7L2QKYyCSOojeVoZG11B0MZZGdHUgt7CiiYHcDBQLDRgE4UdFiMV9AJurzZtVK7FgAAAAAElFTkSuQmCC',
			'3197' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsQ2AMAwE7SIbwD5kgy+SJhvAFG6yQZZIpsSisoESBP7u9NKfTONyQn/KK34zCJQ5J8PQGBwXmWyzBgSBZ40OBuPXyyh9LX2zftqjhOqWqzJdOjMWgJyLshgX7xyyOjv21f8ezI3fDqtpySRigQSXAAAAAElFTkSuQmCC',
			'8F85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMDkMREpog0MDo6OiCrC2gVaWBtCEQRg6pzdUBy39KoqWGrQldGRSG5D6LOoUEEw7wALGKBDiIYdjgEILuPNQCoIpRhqsMgCD8qQizuAwAIkMt1ZD9tGwAAAABJRU5ErkJggg==',
			'0EFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0NDkMRYA0QaWIEyyOpEpmCKBbSiiIGdFLV0atjS0JWhWUjuQ1OHUwybHdjcAnYzmthAhR8VIRb3AQAaJMf2DUGIyQAAAABJRU5ErkJggg==',
			'3834' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhDGRoCkMQCprC2sjY6NCKLMbSKNDo0BLSiiAHVMTQ6TAlAct/KqJVhq6auiopCdh9YnaMDpnmBoSGYdmBzC4oYNjcPVPhREWJxHwBCnM6zPvJg9wAAAABJRU5ErkJggg==',
			'4772' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37poiGuoYGTHVAFgthaHRoCAgIQBJjBIsFOoggibFOYWgFiYoguW/atFXTVi1dtSoKyX0BUxgCGKaAVCL0hoYyOgBFW1HdwtoAFJ2CKibSwNoAVIkhxhgaMhjCj3oQi/sAmXvMLzJYR+IAAAAASUVORK5CYII=',
			'C554' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQ1lDHRoCkMREWkUaWBsYGpHFAhrBYq0oYg0iIaxTGaYEILkvatXUpUszs6KikNwHlG90aAh0QNULFgsNQbWj0RUog+oW1lZGR1T3sYYwhjCEMqCIDVT4URFicR8A47vOaqn7YhsAAAAASUVORK5CYII=',
			'C039' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYAhhDGaY6IImJtDKGsDY6BAQgiQU0srYyNAQ6iCCLNYg0OjQ6wsTATopaNW1l1tRVUWFI7oOoc5iKoRdEYtgRgGIHNrdgc/NAhR8VIRb3AQAo2M0JsLaa3QAAAABJRU5ErkJggg==',
			'B1FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0MdkMQCpjAGsDYwOgQgi7WygsVEUNQxIIuBnRQatSpqaejKrGlI7kNTBzWPSDGoXmS3hAJdDBRDcfNAhR8VIRb3AQAONMmjCBywagAAAABJRU5ErkJggg==',
			'C736' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WENEQx1DGaY6IImJtDI0ujY6BAQgiQU0MjQ6NAQ6CCCLNTAAVTo6ILsvatWqaaumrkzNQnIfUF0AUB2qeQ2MQH2BDiIodrA2oIuJtIo0sKK5hTVEpIERzc0DFX5UhFjcBwB2Rs0cpgLUOAAAAABJRU5ErkJggg==',
			'8185' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjAGMDo6OiCrC2hlDWBtCEQRE5nCAFLn6oDkvqVRq6JWha6MikJyH0SdQ4MIinkMQPMCsIgFOohg2OEQgOw+oEtCGUIZpjoMgvCjIsTiPgAaH8kkPBKm5AAAAABJRU5ErkJggg==',
			'4D8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpI37poiGMIQyhjogi4WItDI6OjoEIIkxhog0ujYEOoggibFOEWl0BKoTQXLftGnTVmaFrsyahuS+AFR1YBgaimkewxSsYhhuwermgQo/6kEs7gMAPb7Lk1IKM5cAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>