<?php

/**
 * @author Patryk Cieślak <patryk@bendar.eu>
 * @version 1.0.0
 */


namespace Helper;

class Dump
{
    private $styles = '';
    private $scripts = '';
    private $assetsAreDrown = false;
    private $icons = false;
    private $jQueryCDN = 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js';
    // private $jQueryCDN = '';
    private $iconUp = '<i class="fas fa-caret-square-up"></i>';
    private $iconDown = '<i class="fas fa-caret-square-down"></i>';
    private $stream = '';
    private $file = __DIR__ . "dump.html";
    private $errors;
    private static $staticStyles =
    "<style>
        pre {
            background: #eee;
            color: #121212;
            font-size: 12px;
            border: 1px solid #888;
            padding: 12px;
            border-radius: 4px;
            text-align: left;
        }
        .dumpKey, .dumpTime{
            color: #080;
            font-weight: bold;
        }
        .dumpBracket {
            color: #ea5517;
        }
        .dumpArray {
            color: #800;
        }
        .dumpString {
            color: #00a;
        }
        .dumpBool {
            color: #31947a;
        }
        .dumpInt {
            color: #7c3ab5;
        }
        .var_desc{
            font-weight: bold;
            color: #0274a7;
            clear:both;
            background-color: #ddd;
            padding: 9px;
            border: 1px solid #aaa;
            border-radius: 8px;
            border-radius: 8px;
            width:33%;
            min-width: 130px;
            cursor: pointer;
        }
        .textString{
            color: #146d49;
            font-weight: bold;
        }
        .dumpedVar {
            margin: 5px;
            margin-left: 150px;
            text-align: left;
        }
        .dump-dropdown {
            cursor: pointer;
            float:right;
            color: #ea5517;
        }
        .code {
            margin: -8px 0 4px 0;
            display:none;
        }
        .dumpErrors {
            background:#f39b9b;
            border: 1px solid #b32b2b;
            color: #8e1f1f;font-size: 1.3em;
            font-weight: normal;
            padding: 2px 3px;
        }
        .showCode {
            display:block;
        }
        </style>"
        ;

    public function __construct($var = null)
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        ob_start();

        if ($var !== null) {
            $this->d($var);
        }
        $this->styles = self::$staticStyles;

        $rotationSript = ($this->icons) ? "
            rotation += 180;
            var icon = $(this).prev().children('.dump-dropdown');
            icon.rotate(rotation);
            icon.toggleClass('text-success');" : "";
        $this->scripts = "
        <script>
        var rotation = 0;
        let $ = jQuery.noConflict();
        if(typeof(jQuery) != 'undefined') {
        jQuery.fn.rotate = function(degrees) {
            $(this).css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
                         '-moz-transform' : 'rotate('+ degrees +'deg)',
                         '-ms-transform' : 'rotate('+ degrees +'deg)',
                         'transform' : 'rotate('+ degrees +'deg)'});
        };
        }
        $(document).ready(function(){
            // $('pre.code').hide();
            $('.showCode').show();

            $('.dumpedVar .var_desc').unbind('click').click(function(event){
                event.stopPropagation();
                $(this).next().stop(true, true).slideToggle(200, function(){
                " . $rotationSript . "
                });
            });
        });
        </script>
        ";
        if (!$this->icons) {
            $this->iconUp = '';
            $this->iconDown = '';
        }
    }

    public function d($var, $desc = null, $show = false)
    {
        $this->drawAssets();
        $result = "<div class='dumpedVar'>";
        if ($desc !== null) {
            $result .= '<div class="var_desc">'
            . $desc
            . ': <div class="dump-dropdown">'
            . $this->iconUp . '</div></div>';
        } else {
            $result .= '<div class="var_desc">Zmienna: <div class="dump-dropdown">'
            . $this->iconDown
            . '</div></div>';
        }
        ob_start();
        var_dump($var);
        // $show = ($show) ? ' style="display: block;"' : '';
        $show = ($show) ? 'showCode' : '';
        $result .= "<pre class='code $show'>";
        $result .= ob_get_clean();
        $result .= "</pre>";
        $result .= "</div>";
        $result = str_replace('array', '<span class="dumpArray">array</span>', $result);
        $result = str_replace('string', '<span class="dumpString">string</span>', $result);
        $result = str_replace('bool', '<span class="dumpBool">bool</span>', $result);
        $result = str_replace('int', '<span class="dumpInt">int</span>', $result);
        $result = str_replace('[', '[<span class="dumpKey">', $result);
        $result = str_replace(']', '</span>]', $result);
        $result = str_replace('(', '(<span class="dumpBracket">', $result);
        $result = str_replace(')', '</span>)', $result);
        $result = str_replace(') "', ') <span class="textString">"', $result);
        $result = str_replace("\"\n", "\"</span>\n", $result);
        echo $result;
    }

    public static function sd($var, $desc = null)
    {
        echo self::$staticStyles;
        $result = "<div class='dumpedVar'>";
        if ($desc !== null) {
            $result .= '<div class="var_desc">'
            . $desc
            . '</div>';
        } else {
            $result .= '<div class="var_desc">Variable:';
        }
        ob_start();
        var_dump($var);

        $result .= "<pre class='code showCode'>";
        $result .= ob_get_clean();
        $result .= "</pre>";
        $result .= "</div>";
        $result = str_replace('array', '<span class="dumpArray">array</span>', $result);
        $result = str_replace('string', '<span class="dumpString">string</span>', $result);
        $result = str_replace('bool', '<span class="dumpBool">bool</span>', $result);
        $result = str_replace('int', '<span class="dumpInt">int</span>', $result);
        $result = str_replace('[', '[<span class="dumpKey">', $result);
        $result = str_replace(']', '</span>]', $result);
        $result = str_replace('(', '(<span class="dumpBracket">', $result);
        $result = str_replace(')', '</span>)', $result);
        $result = str_replace(') "', ') <span class="textString">"', $result);
        $result = str_replace("\"\n", "\"</span>\n", $result);
        echo $result;
    }

    public function ex($var, $desc = null, $show = false)
    {
        $varStr = var_export($var, true);
        $varStr = str_replace('array', '<span class="dumpArray">array</span>', $varStr);
        $varStr = str_replace('string', '<span class="dumpString">string</span>', $varStr);
        $varStr = str_replace('bool', '<span class="dumpBool">bool</span>', $varStr);
        $varStr = str_replace('int', '<span class="dumpInt">int</span>', $varStr);
        $varStr = str_replace('[', '[<span class="dumpKey">', $varStr);
        $varStr = str_replace(']', '</span>]', $varStr);
        $varStr = str_replace('(', '(<span class="dumpBracket">', $varStr);
        $varStr = str_replace(')', '</span>)', $varStr);
        $varStr = str_replace(') "', ') <span class="textString">"', $varStr);
        $varStr = str_replace("\"\n", "\"</span>\n", $varStr);
        $result = $this->addAssets();
        $result .= "<div class='dumpedVar'>";
        if ($desc !== null) {
            $result .= '<div class="var_desc">'
            . $desc
            . ': <div class="dump-dropdown">'
            . $this->iconUp
            . '</div></div>';
        } else {
            $result .= '<div class="var_desc">Zmienna: <div class="dump-dropdown">' . $this->iconDown . '</div></div>';
        }
        // ob_start();
        // $show = ($show) ? ' style="display: block;"' : '';
        $show = ($show) ? 'showCode' : '';
        $result .= "<pre class='code $show'>";
        $result .= $varStr;
        // $result .= ob_get_clean();
        $result .= "</pre>";
        $result .= "</div>";
        $this->stream .= $result;
        return $result;
    }

    public static function sex($var, $desc = null, $show = false)
    {
        $varStr = var_export($var, true);
        $varStr = str_replace('array', '<span class="dumpArray">array</span>', $varStr);
        $varStr = str_replace('string', '<span class="dumpString">string</span>', $varStr);
        $varStr = str_replace('bool', '<span class="dumpBool">bool</span>', $varStr);
        $varStr = str_replace('int', '<span class="dumpInt">int</span>', $varStr);
        $varStr = str_replace('[', '[<span class="dumpKey">', $varStr);
        $varStr = str_replace(']', '</span>]', $varStr);
        $varStr = str_replace('(', '(<span class="dumpBracket">', $varStr);
        $varStr = str_replace(')', '</span>)', $varStr);
        $varStr = str_replace(') "', ') <span class="textString">"', $varStr);
        $varStr = str_replace("\"\n", "\"</span>\n", $varStr);
        $result = self::$staticStyles;
        $result .= "<div class='dumpedVar'>";
        if ($desc !== null) {
            $result .= '<div class="var_desc">'
            . $desc
            . '</div>';
        } else {
            $result .= '<div class="var_desc">Variable:';
        }
        // ob_start();
        // $show = ($show) ? ' style="display: block;"' : '';
        $result .= "<pre class='code showCode'>";
        $result .= $varStr;
        // $result .= ob_get_clean();
        $result .= "</pre>";
        $result .= "</div>";
        if ($show) {
            echo $result;
        }
        return $result;
    }

    private function drawAssets()
    {
        if ($this->jQueryCDN) {
            echo "<script src='" . $this->jQueryCDN . "'></script>";
        }
        if (!$this->assetsAreDrown) {
            echo $this->styles;
            echo $this->scripts;
            $this->assetsAreDrown = true;
        }
    }
    private function addAssets()
    {
        $assets = '';
        if ($this->jQueryCDN) {
            $assets .= "<script src='" . $this->jQueryCDN . "'></script>";
        }
        if (!$this->assetsAreDrown) {
            $assets .= $this->styles;
            $assets .= $this->scripts;
            $this->assetsAreDrown = true;
        }
        return $assets;
    }



    public function save()
    {
        $this->errors = ob_get_flush();
        if (strlen($this->errors) > 0) {
            $this->errors = '<br><div class="dumpErrors">' . $this->errors . '</div>';
        }
        $this->stream = '<span class="dumpTime">== ' . date('Y-m-d H:i') . ': ==</span>'
        . $this->errors . '<br>' . $this->stream;
        // $this->d(scandir($this->file), 'files', true);
        $fp = fopen($this->file, 'a+');
        fwrite($fp, $this->stream);
        fclose($fp);
    }

    public static function showPHPerros()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
}
