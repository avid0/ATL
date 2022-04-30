<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * API Answer request methods
 */
namespace ATL\Plugin;
use \ATL\Logger;

trait Reply {
    /**
     * replying by sendMessage
     * @method replyMessage
     * @param string $text
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyMessage($text, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerMessage($text, $datas, $request);
    }

    /**
     * replying by sendPhoto
     * @method replyPhoto
     * @param mixed $photo
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyPhoto($photo, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerPhoto($photo, $datas, $request);
    }

    /**
     * replying by sendAudio
     * @method replyAudio
     * @param mixed $audio
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyAudio($audio, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerAudio($audio, $datas, $request);
    }

    /**
     * replying by sendDocument
     * @method replyDocument
     * @param mixed $document
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyDocument($document, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerDocument($document, $datas, $request);
    }

    /**
     * replying by sendVideo
     * @method replyVideo
     * @param mixed $video
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyVideo($video, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerVideo($video, $datas, $request);
    }

    /**
     * replying by sendAnimation
     * @method replyAnimation
     * @param mixed $animation
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyAnimation($animation, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerAnimation($animation, $datas, $request);
    }

    /**
     * replying by sendVoice
     * @method replyVoice
     * @param mixed $voice
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyVoice($voice, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerVoice($voice, $datas, $request);
    }

    /**
     * replying by sendVideoNote
     * @method replyVideoNote
     * @param mixed $video_note
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyVideoNote($video_note, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerVideoNote($video_note, $datas, $request);
    }

    /**
     * replying by sendSticker
     * @method replySticker
     * @param mixed $sticker
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replySticker($sticker, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerSticker($sticker, $datas, $request);
    }

    /**
     * replying by sendDice
     * @method replyDice
     * @param mixed $emoji
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyDice($emoji, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerDice($emoji, $datas, $request);
    }

    /**
     * replying by sendConact
     * @method replyContact
     * @param string $phone
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyContact(string $phone, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerContact($phone, $datas, $request);
    }

    /**
     * replying by sendLocation
     * @method replyLocation
     * @param float $latitude
     * @param float $logitude
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyLocation(float $latitude, float $longitude, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerLocation($latitude, $longitude, $datas, $request);
    }
    
    /**
     * replying by sendVenue
     * @method replyVenue
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyVenue(float $latitude, float $longitude, string $title, string $address, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerVenue($latitude, $longitude, $title, $address, $datas, $request);
    }

    /**
     * replying by sendPoll
     * @method replyPoll
     * @param string $question
     * @param array $options
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyPoll(string $question, array $options, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerPoll($question, $options, $datas, $request);
    }
    
    /**
     * replying by copyMessage
     * @method replyCopyMessage
     * @param string $from
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyCopyMessage($from, $message, array $datas = array(), int $request = 0){
        if($this->answer->mid)
            $datas['reply_to_message_id'] = $this->answer->mid;
        return $this->answerCopyMessage($from, $message, $datas, $request);
    }
}
?>