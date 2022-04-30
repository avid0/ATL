<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * API Request methods with reply_markup parameter
 */
namespace ATL\Plugin;

trait KeyboardRequest {
    /**
     * API Request
     * 
     * @method request
     * @param string $method
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object or array or false
     */
    public function request(string $method, $reply_markup, array $datas = [], int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->request($method, $datas, $request);
    }

    /**
     * @method sendMessage
     * @param string $chat
     * @param string $text
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendMessage($chat, $text, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendMessage($chat, $text, $datas, $request);
    }
    
    /**
     * @method answerMessage
     * @param string $text
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerMessage($text, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerMessage($text, $datas, $request);
    }
        
    /**
     * @method replyMessage
     * @param string $text
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyMessage($text, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyMessage($text, $datas, $request);
    }
    
    /**
     * @method sendPhoto
     * @param string $chat
     * @param mixed $photo
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendPhoto($chat, $photo, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendPhoto($chat, $photo, $datas, $request);
    }
        
    /**
     * @method answerPhoto
     * @param mixed $photo
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerPhoto($photo, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerPhoto($photo, $datas, $request);
    }
            
    /**
     * @method replyPhoto
     * @param mixed $photo
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyPhoto($photo, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyPhoto($photo, $datas, $request);
    }

    /**
     * @method sendAudio 
     * @param string $chat
     * @param mixed $audio
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendAudio($chat, $audio, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendAudio($chat, $audio, $datas, $request);
    }

    /**
     * @method answerAudio
     * @param mixed $audio
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerAudio($audio, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerAudio($audio, $datas, $request);
    }
    
    /**
     * @method replyAudio
     * @param mixed $audio
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyAudio($audio, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyAudio($audio, $datas, $request);
    }

    /**
     * @method sendDocument
     * @param string $chat
     * @param mixed $document
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @param \ATL\Plugin\SentMessage
     */
    public function sendDocument($chat, $document, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendDocument($chat, $document, $datas, $request);
    }
    
    /**
     * @method answerDocument
     * @param mixed $document
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @param \ATL\Plugin\SentMessage
     */
    public function answerDocument($document, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerDocument($document, $datas, $request);
    }
        
    /**
     * @method replyDocument
     * @param mixed $document
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @param \ATL\Plugin\SentMessage
     */
    public function replyDocument($document, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyDocument($document, $datas, $request);
    }

    /**
     * @method sendVideo
     * @param string $chat
     * @param mixed $video
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVideo($chat, $video, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendVideo($chat, $video, $datas, $request);
    }
    
    /**
     * @method answerVideo
     * @param mixed $video
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerVideo($video, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerVideo($video, $datas, $request);
    }
        
    /**
     * @method replyVideo
     * @param mixed $video
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyVideo($video, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyVideo($video, $datas, $request);
    }

    /**
     * @method sendAnimation
     * @param string $chat
     * @param mixed $animation
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendAnimation($chat, $animation, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendAnimation($chat, $animation, $datas, $request);
    }
    
    /**
     * @method answerAnimation
     * @param mixed $animation
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerAnimation($animation, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerAnimation($animation, $datas, $request);
    }

    /**
     * @method replyAnimation
     * @param mixed $animation
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyAnimation($animation, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyAnimation($animation, $datas, $request);
    }

    /**
     * @method sendVoice
     * @param string $chat
     * @param mixed $voice
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVoice($chat, $voice, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendVoice($chat, $voice, $datas, $request);
    }
    
    /**
     * @method answerVoice
     * @param mixed $voice
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerVoice($voice, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerVoice($voice, $datas, $request);
    }
        
    /**
     * @method replyVoice
     * @param mixed $voice
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyVoice($voice, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyVoice($voice, $datas, $request);
    }

    /**
     * @method sendVideoNote
     * @param string $chat
     * @param mixed $video_note
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVideoNote($chat, $video_note, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendVideoNote($chat, $video_note, $datas, $request);
    }
    
    /**
     * @method answerVideoNote
     * @param mixed $video_note
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerVideoNote($video_note, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerVideoNote($video_note, $datas, $request);
    }
        
    /**
     * @method replyVideoNote
     * @param mixed $video_note
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyVideoNote($video_note, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyVideoNote($video_note, $datas, $request);
    }

    /**
     * @method sendSticker
     * @param string $chat
     * @param mixed $sticker
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendSticker($chat, $sticker, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendSticker($chat, $sticker, $datas, $request);
    }
    
    /**
     * @method answerSticker
     * @param mixed $sticker
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerSticker($sticker, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerSticker($sticker, $datas, $request);
    }

    /**
     * @method replySticker
     * @param mixed $sticker
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replySticker($sticker, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replySticker($sticker, $datas, $request);
    }

    /**
     * @method sendDice
     * @param string $chat
     * @param string $emoji
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendDice($chat, $emoji, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendDice($chat, $emoji, $datas, $request);
    }
    
    /**
     * @method answerDice
     * @param string $emoji
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function answerDice($emoji, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerDice($emoji, $datas, $request);
    }

    /**
     * @method replyDice
     * @param string $emoji
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function replyDice($emoji, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyDice($emoji, $datas, $request);
    }

    /**
     * @method sendContact
     * @param string $chat
     * @param string $phone
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendContact($chat, string $phone, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendContact($chat, $phone, $datas, $request);
    }
    
    /**
     * @method answerContact
     * @param string $phone
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerContact(string $phone, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerContact($phone, $datas, $request);
    }
        
    /**
     * @method replyContact
     * @param string $phone
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyContact(string $phone, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyContact($phone, $datas, $request);
    }

    /**
     * @method sendLocation
     * @param string $chat
     * @param float $latitude
     * @param float $logitude
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendLocation($chat, float $latitude, float $logitude, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendLocation($chat, $latitude, $longitude, $request);
    }
    
    /**
     * @method answerLocation
     * @param float $latitude
     * @param float $logitude
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerLocation(float $latitude, float $logitude, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerLocation($latitude, $longitude, $request);
    }
        
    /**
     * @method replyLocation
     * @param float $latitude
     * @param float $logitude
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyLocation(float $latitude, float $logitude, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyLocation($latitude, $longitude, $request);
    }
    
    /**
     * @method sendVenue
     * @param string $chat
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendVenue($chat, float $latitude, float $logitude, string $title, string $address, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->sendVenue($chat, $latitude, $longitude, $title, $address, $datas, $request);
    }

    /**
     * @method answerVenue
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerVenue(float $latitude, float $logitude, string $title, string $address, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->answerVenue($latitude, $longitude, $title, $address, $datas, $request);
    }

    /**
     * @method replyVenue
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param string|array $reply_markup
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function replyVenue(float $latitude, float $logitude, string $title, string $address, $reply_markup, array $datas = array(), int $request = 0){
        $datas['reply_markup'] = $reply_markup;
        return $this->atl->replyVenue($latitude, $longitude, $title, $address, $datas, $request);
    }
}