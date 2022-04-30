<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class ParseMode {
    /**
     * @var \ATL $atl
     */
    public $atl;

    /**
     * Constructor
     * 
     * @method __construct
     * @param \ATL $atl
     */
    public function __construct(\ATL $atl){
        $this->atl = $atl;
    }
    
    /**
     * @method MarkDownV2_quote
     * @param string $text
     * @return string
     */
    public function MarkDownV2_quote(string $text){
        return str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'], $text);
    }
    
    /**
     * @method MarkDown_quote
     * @param string $text
     * @return string
     */
    public function MarkDown_quote(string $text){
        return str_replace(['_', '*', '`', '['], ['\_', '\*', '\`', '\['], $text);
    }
    
    /**
     * @method HTML_quote
     * @param string $text
     * @return string
     */
    public function HTML_quote(string $text){
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);
    }

    /**
     * @method default_quote
     * @param string $text
     * @return string
     */
    public function default_quote(string $text){
        switch(strtolower($this->atl->default->parse_mode)){
            case 'markdown':
                $text = $this->MarkDown_quote($text);
            break;
            case 'markdownv2':
                $text = $this->MarkDownV2_quote($text);
            break;
            case 'html':
                $text = $this->HTML_quote($text);
            break;
        }
        return $text;
    }
}
?>