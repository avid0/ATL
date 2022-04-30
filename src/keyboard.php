<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL;
use \ATL\Logger;

class Keyboard {
    use \ATL\Plugin\KeyboardRequest;

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
     * @var array $buttons = []
     * @var array $rows = []
     * @var array $keyboards = []
     */
    public $buttons = array();
    public $rows = array();
    public $keyboards = array();

    /**
     * Parse reply markup
     * 
     * @method parse
     * @param array $keyboard
     * @return array or false
     */
    public function parse($keyboard){
        if(is_string($keyboard)){
            if(isset($this->keyboards[$keyboard]))
                return $this->parse($this->keyboards[$keyboard]);
            elseif(isset($this->rows[$keyboard]))
                return [
                    "keyboard" => $this->parse([$this->rows[$keyboard]])
                ];
            elseif(isset($this->buttons[$keyboard]))
                return [
                    "keyboard" => $this->parse([[$this->buttons[$keyboard]]])
                ];
            else
                return $keyboard;
        }
        if(isset($keyboard['keyboard'])){
            $keyboard['keyboard'] = $this->replyMarkupRows($this->parse($keyboard['keyboard']));
            return $keyboard;
        }elseif(isset($keyboard['inline_keyboard'])){
            $keyboard['inline_keyboard'] = $this->replyMarkupRows($this->parse($keyboard['inline_keyboard']));
            return $keyboard;
        }elseif(isset($keyboard['inline_list'])){
            $keyboard['inline_list'] = $this->replyMarkupRows($this->parse($keyboard['inline_list']));
            if(isset($keyboard['list_top']))
                $keyboard['list_top'] = $this->replyMarkupRows($this->parse($keyboard['list_top']));
            if(isset($keyboard['list_between']))
                $keyboard['list_between'] = $this->replyMarkupRows($this->parse($keyboard['list_between']));
            if(isset($keyboard['list_bottom']))
                $keyboard['list_bottom'] = $this->replyMarkupRows($this->parse($keyboard['list_bottom']));
            return $keyboard;
        }
        for($i = 0; isset($keyboard[$i]); ++$i){
            if(is_string($keyboard[$i])){
                if(isset($this->rows[$keyboard[$i]]))
                    $keyboard[$i] = $this->rows[$keyboard[$i]];
                elseif(isset($this->buttons[$keyboard[$i]]))
                    $keyboard[$i] = array($this->buttons[$keyboard[$i]]);
            }
            if(is_array($keyboard[$i])){
                for($j = 0; isset($keyboard[$i][$j]); ++$j){
                    if(is_string($keyboard[$i][$j])){
                        if(isset($this->buttons[$keyboard[$i][$j]]))
                            $keyboard[$i][$j] = $this->buttons[$keyboard[$i][$j]];
                    }
                }
            }
        }
        return $keyboard;
    }
    
    /**
     * Parse reply markup with lang
     * 
     * @method parseLang
     * @param array $keyboard
     * @param \ATL\Plugin\Lang $lang
     * @return array or false
     */
    public function parseLang($keyboard, \ATL\Plugin\Lang $lang){
        $keyboard = $lang->select($keyboard);
        if(is_string($keyboard)){
            if(isset($this->keyboards[$keyboard]))
                return $this->parseLang($this->keyboards[$keyboard], $lang);
            elseif(isset($this->rows[$keyboard]))
                return [
                    "keyboard" => $this->parseLang([$this->rows[$keyboard]], $lang)
                ];
            elseif(isset($this->buttons[$keyboard]))
                return [
                    "keyboard" => $this->parseLang([[$this->buttons[$keyboard]]], $lang)
                ];
            else
                return $keyboard;
        }
        if(isset($keyboard['keyboard'])){
            $keyboard['keyboard'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['keyboard']), $lang));
            return $keyboard;
        }elseif(isset($keyboard['inline_keyboard'])){
            $keyboard['inline_keyboard'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['inline_keyboard']), $lang));
            return $keyboard;
        }elseif(isset($keyboard['inline_list'])){
            $keyboard['inline_list'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['inline_list']), $lang));
            if(isset($keyboard['list_top']))
                $keyboard['list_top'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['list_top']), $lang));
            if(isset($keyboard['list_between']))
                $keyboard['list_between'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['list_between']), $lang));
            if(isset($keyboard['list_bottom']))
                $keyboard['list_bottom'] = $this->replyMarkupRows($this->parseLang($lang->select($keyboard['list_bottom']), $lang));
            return $keyboard;
        }
        for($i = 0; isset($keyboard[$i]); ++$i){
            if(is_string($keyboard[$i])){
                if(isset($this->rows[$keyboard[$i]]))
                    $keyboard[$i] = $lang->select($this->rows[$keyboard[$i]]);
                elseif(isset($this->buttons[$keyboard[$i]])){
                    $button = $lang->select($this->buttons[$keyboard[$i]]);
                    if(isset($button['text']))
                        $button['text'] = $lang->select($button['text']);
                    $keyboard[$i] = array($button);
                }
            }
            if(is_array($keyboard[$i])){
                for($j = 0; isset($keyboard[$i][$j]); ++$j){
                    if(is_string($keyboard[$i][$j])){
                        if(isset($this->buttons[$keyboard[$i][$j]])){
                            $button = $lang->select($this->buttons[$keyboard[$i][$j]]);
                            if(isset($button['text']))
                                $button['text'] = $lang->select($button['text']);
                            $keyboard[$i][$j] = $button;
                        }
                    }else{
                        $button = $lang->select($keyboard[$i][$j]);
                        if(isset($button['text']))
                            $button['text'] = $lang->select($button['text']);
                        $keyboard[$i][$j] = $button;
                    }
                }
            }
        }
        return $keyboard;
    }

    /**
     * @method replyMarkupRows
     * @param array $reply_markup
     * @return array
     */
    public function replyMarkupRows(array $reply_markup){
        if(isset($reply_markup['keyboard']))
            return $reply_markup['keyboard'];
        if(isset($reply_markup['inline_keyboard']))
            return $reply_markup['inline_keyboard'];
        if(isset($reply_markup['inline_list']))
            return $reply_markup['inline_list'];
        return $reply_markup;
    }

    /**
     * @internal
     * @method existsCheck
     * @param string $index
     * @param string $func = "existsCheck"
     * @return bool
     */
    private function existsCheck(string $index, string $func = "existsCheck"){
        if(isset($this->keyboards[$index])){
            Logger::log("\ATL\Keyboard::{$func}(): The selected index is currently selected for a keyboard");
            return true;
        }elseif(isset($this->rows[$index])){
            Logger::log("\ATL\Keyboard::{$func}(): The selected index is currently selected for a row");
            return true;
        }elseif(isset($this->buttons[$index])){
            Logger::log("\ATL\Keyboard::{$func}(): The selected index is currently selected for a button");
            return true;
        }
        return false;
    }

    /**
     * @method addRemoveKeyboard
     * @param string $index
     * @param bool $selective = null
     * @return self
     */
    public function addRemoveKeyboard(string $index, bool $selective = null){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->keyboards[$index] = array(
            "remove_keyboard" => true
        );
        if($selective !== null)
            $this->keyboards[$index]["selective"] = $selective;
        return $this;
    }
    
    /**
     * @method addKeyboard
     * @param string $index
     * @param array $rows
     * @return self
     */
    public function addKeyboard(string $index, array $rows){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->keyboards[$index] = array(
            "keyboard" => $rows
        );
        return $this;
    }
    
    /**
     * @method addInlineKeyboard
     * @param string $index
     * @param array $rows
     * @return self
     */
    public function addInlineKeyboard(string $index, array $rows){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->keyboards[$index] = array(
            "inline_keyboard" => $rows
        );
        return $this;
    }
    
    /**
     * @method addReplyMarkup
     * @param string $index
     * @param array $reply_markup
     * @return self
     */
    public function addReplyMarkup(string $index, array $reply_markup){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->keyboards[$index] = $reply_markup;
        return $this;
    }
        
    /**
     * @method addRow
     * @param string $index
     * @param array $buttons
     * @return self
     */
    public function addRow(string $index, array $buttons){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        if(count($buttons) > 8){
            $buttons = array_slice($buttons, 0, 8);
            Logger::log("\ATL\Keyboard::registerRow(): Cannot have more than 8 buttons in a row");
        }
        $this->rows[$index] = $buttons;
        return $this;
    }

    /**
     * @method appendRow
     * @param string $index
     * @param array $rows
     * @return self
     */
    public function appendRow(string $index, array $rows){
        if(!isset($this->keyboards[$index])){
            Logger::log("\ATL\Keyboard::appendRow(): The desired keyboard does not exist");
            return $this;
        }
        if(isset($this->keyboards[$index]['keyboard']))
            $this->keyboards[$index]['keyboard'] = array_merge($this->keyboards[$index]['keyboard'], $rows);
        elseif(isset($this->keyboards[$index]['inline_keyboard']))
            $this->keyboards[$index]['inline_keyboard'] = array_merge($this->keyboards[$index]['inline_keyboard'], $rows);
        else
            Logger::log("\ATL\Keyboard::appendRow(): Can not add rows to this keyboard");
        return $this;
    }

    /**
     * @method appendButton
     * @param string $index
     * @param array $buttons
     * @return self
     */
    public function appendButton(string $index, array $buttons){
        if(!isset($this->rows[$index])){
            Logger::log("\ATL\Keyboard::appendButton(): The desired row does not exist");
            return $this;
        }
        $this->rows[$index] = array_merge($this->rows[$index], $buttons);
        if(count($this->rows[$index]) > 8){
            $this->rows[$index] = array_slice($this->rows[$index], 0, 8);
            Logger::log("\ATL\Keyboard::appendButton(): Cannot have more than 8 buttons in a row");
        }
        return $this;
    }

    /**
     * @method addTextButton
     * @param string $index
     * @param mixed $name
     * @return self
     */
    public function addTextButton(string $index, $name){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name
        );
        return $this;
    }
    
    /**
     * @method addContactButton
     * @param string $index
     * @param mixed $name
     * @return self
     */
    public function addContactButton(string $index, $name){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "request_contact" => true
        );
        return $this;
    }
    
    /**
     * @method addLocationButton
     * @param string $index
     * @param mixed $name
     * @return self
     */
    public function addLocationButton(string $index, $name){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "request_location" => true
        );
        return $this;
    }
    
    /**
     * @method addPollButton
     * @param string $index
     * @param mixed $name,
     * @param string $poll_type = null
     * @return self
     */
    public function addPollButton(string $index, $name, string $poll_type = null){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "request_poll" => array()
        );
        if($poll_type)
            $this->buttons[$index]["request_poll"]["type"] = $poll_type;
        return $this;
    }
        
    /**
     * @method addURLButton
     * @param string $index
     * @param mixed $name,
     * @param string $url
     * @return self
     */
    public function addURLButton(string $index, $name, string $url){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "url" => $url
        );
        return $this;
    }
       
    /**
     * @method addCallbackButton
     * @param string $index
     * @param mixed $name,
     * @param string $data
     * @return self
     */
    public function addCallbackButton(string $index, $name, string $data){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "callback_data" => $data
        );
        return $this;
    }
       
    /**
     * @method addQueryButton
     * @param string $index
     * @param mixed $name,
     * @param string $query
     * @return self
     */
    public function addQueryButton(string $index, $name, string $query){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "switch_inline_query" => $query
        );
        return $this;
    }
           
    /**
     * @method addQueryCurButton
     * @param string $index
     * @param mixed $name,
     * @param string $query
     * @return self
     */
    public function addQueryCurButton(string $index, $name, string $query){
        if($this->existsCheck($index, __METHOD__))
            return $this;
        $this->buttons[$index] = array(
            "text" => $name,
            "switch_inline_query_current_chat" => $query
        );
        return $this;
    }

    /**
     * @method exists
     * @param string $index
     * @return bool
     */
    public function exists(string $index){
        return isset($this->keyboards[$index]) || isset($this->rows[$index]) || isset($this->buttons[$index]);
    }

    /**
     * @method get
     * @param string $index
     * @return array or false
     */
    public function get(string $index){
        if(isset($this->keyboards[$index]))
            return $this->keyboards[$index];
        if(isset($this->rows[$index]))
            return $this->rows[$index];
        if(isset($this->buttons[$index]))
            return $this->buttons[$index];
        return false;
    }
    
    /**
     * @method remove
     * @param string $index
     * @return bool
     */
    public function remove(string $index){
        if(isset($this->keyboards[$index]))
            unset($this->keyboards[$index]);
        elseif(isset($this->rows[$index]))
            unset($this->rows[$index]);
        elseif(isset($this->buttons[$index]))
            unset($this->buttons[$index]);
        else
            return false;
        return true;
    }

    /**
     * @method reset
     */
    public function reset(){
        $this->keyboards = $this->rows = $this->buttons = array();
    }
}
?>