<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATLException;

class SentMessage extends \stdClass {
    /**
     * Constructor 
     * 
     * @method __construct
     * @param \ATL $atl
     * @param object $message
     */
    public function __construct(\ATL $atl, object $message){
        foreach((array)$message as $key => $value)
            $this->$key = $value;
        $this->atl = $atl;
    }

    /**
     * @method editText
     * @param string $text
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function editText($text, array $datas = array(), int $request = 0){
        $this->text = $text;
        return $this->atl->editMessageText($this->chat->id, $this->message_id, $text, $datas, $request);
    }
    
    /**
     * @method editCaption
     * @param string $caption
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function editCaption($caption, array $datas = array(), int $request = 0){
        $this->caption = $caption;
        return $this->atl->editMessageCaption($this->chat->id, $this->message_id, $caption, $datas, $request);
    }

    /**
     * @method appendText
     * @param string $text
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function appendText($text, array $datas = array(), int $request = 0){
        if(!isset($this->text))
            $this->text = $text;
        else
            $this->text .= $text;
        return $this->atl->editMessageText($this->chat->id, $this->message_id, $this->text, $datas, $request);
    }
    
    /**
     * @method appendCaption
     * @param string $caption
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function appendCaption($caption, array $datas = array(), int $request = 0){
        if(!isset($this->caption))
            $this->caption = $caption;
        else
            $this->caption .= $caption;
        return $this->atl->editMessageCaption($this->chat->id, $this->message_id, $this->caption, $datas, $request);
    }

    /**
     * @method editReplyMarkup
     * @param array $keyboard
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function editReplyMarkup(array $keyboard, array $datas = array(), int $request = 0){
        return $this->atl->editMessageReplyMarkup($this->chat->id, $this->message_id, $keyboard, $datas, $request);
    }
    
    /**
     * @method deleteReplyMarkup
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function deleteReplyMarkup(array $datas = array(), int $request = 0){
        return $this->editReplyMarkup(array(), $datas, $request);
    }

    /**
     * @method forwardTo
     * @param string $chat
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function forwardTo($chat, array $datas = array(), int $request = 0){
        return $this->atl->forwardMessage($chat, $this->chat->id, $this->message_id, $datas, $request);
    }
    
    /**
     * @method answerForward
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function answerForward(array $datas = array(), int $request = 0){
        return $this->atl->answerForwardMessage($this->chat->id, $this->message_id, $datas, $request);
    }
    
    /**
     * @method copyTo
     * @param string $chat
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function copyTo($chat, array $datas = array(), int $request = 0){
        return $this->atl->copyMessage($chat, $this->chat->id, $this->message_id, $datas, $request);
    }
    
    /**
     * @method resend
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function resend(array $datas = array(), int $request = 0){
        return $this->atl->answerCopyMessage($this->chat->id, $this->message_id, $datas, $request);
    }

    /**
     * @method delete
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function delete(array $datas = array(), int $request = 0){
        return $this->atl->deleteMessage($this->chat->id, $this->message_id, $datas, $request);
    }

    /**
     * @method reply
     * @param string $text
     * @param array $datas = array()
     * @param int $request = default
     * @return object
     */
    public function reply($text, array $datas = array(), int $request = 0){
        $datas['reply'] = $this->message_id;
        return $this->atl->sendMessage($this->chat->id, $text, $datas, $request);
    }
}
?>