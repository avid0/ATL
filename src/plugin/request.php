<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * API Request methods
 */
namespace ATL\Plugin;

trait Request {
    /**
     * API Request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @param int $request = default
     * @return object or array or false
     */
    public function request(string $method, array $datas = [], int $request = 0){
        if(!$this->api){
            Logger::log("\ATL::request(): The API object was not created to send the request");
            return false;
        }
        $datas = $this->parseDatas($datas, $method);
        $datas['method'] = $method;
        $datas = \ATL\Parser::parseMultidatas(array($datas));
        if($this->default->auto_action)
            $datas = \ATL\Parser::appendAutoAction($datas);
        if(!isset($datas[0]))
            return false;
        if(!isset($datas[1])){
            $datas = $datas[0];
            $method = $datas['method'];
            unset($datas['method']);
            return $this->api->request($method, $datas, $request);
        }
        return $this->api->multirequest($datas, $request);
    }

    /**
     * API Multirequest
     * 
     * @method multirequest
     * @param string $datas
     * @param int $request = defeult
     * @param int $limit = 30
     * @return array or false
     */
    public function multirequest(array $datas, int $request = 0, int $limit = 30){
        if(!$this->api){
            Logger::log("\ATL::multirequest(): The API object was not created to send the request");
            return false;
        }
        foreach($datas as &$data){
            if(isset($data['method'])){
                $data = $this->parseDatas($data, $data['method']);
            }
        }
        $datas = \ATL\Parser::parseMultidatas($datas);
        if($this->default->auto_action)
            $datas = \ATL\Parser::appendAutoAction($datas);
        return $this->api->multirequest($datas, $request, $limit);
    }

    /**
     * API GetUpdates
     * 
     * @method getUpdates
     * @param int $offset = 0
     * @param int $limit = 100
     * @param int $timeout = 0
     * @param array $allowed = null
     * @return object
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0, array $allowed = null, int $request = 0){
        $datas = array(
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout
        );
        if($allowed)
            $datas['allowed_updates'] = $allowed;
        return $this->request('getUpdates', $datas, $request);
    }

    /**
     * API Read file
     * 
     * @method file
     * @param string $path
     * @param int $request = default
     * @return string or false
     */
    public function file(string $path, int $request = 0){
        if(!$this->api){
            Logger::log("\ATL::file(): The API object was not created to send the request");
            return false;
        }
        return $this->api->file($path, $request);
    }

    /**
     * @method getMe
     * @param int $request = default
     * @return object
     */
    public function getMe(int $request = 0){
        return $this->request('getMe', array(), $request);
    }

    /**
     * @method getFile
     * @param string $file
     * @param int $request = default
     * @return object
     */
    public function getFile(string $file, int $request = 0){
        return $this->request('getFile', array(
            'file_id' => $file
        ), $request);
    }

    /**
     * @method getChat
     * @param string $chat
     * @param int $request = default
     * @return object
     */
    public function getChat($chat, int $request = 0){
        return $this->request('getChat', array(
            'chat_id' => $chat
        ), $request);
    }

    /**
     * @method readFile
     * @param string $file
     * @param int $request = default
     * @return object
     */
    public function readFile(string $file, int $request = 0){
        $path = $this->getFile($file, $request);
        if(!$path)
            return false;
        return $this->file($path->file_path, $request);
    }
    
    /**
     * @method download
     * @param string $file
     * @param string $into
     * @param int $request = 0
     * @return object
     */
    public function download(string $file, string $into, int $request = 0){
        $path = $this->getFile($file, $request);
        if(!$path)
            return false;
        return $this->api->download($path->file_path, $into);
    }

    /**
     * @method setWebhook
     * @param string $url
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function setWebhook(string $url, array $datas = array(), int $request = 0){
        $datas['url'] = $url;
        return $this->request('setWebhook', $datas, $request);
    }

    /**
     * @method deleteWebhook
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function deleteWebhook(array $datas = array(), int $request = 0){
        return $this->setWebhook('', $datas, $request);
    }

    /**
     * @method getWebhookInfo
     * @param int $request = default
     * @return object
     */
    public function getWebhookInfo(int $request = 0){
        return $this->request('getWebhookInfo', array(), $request);
    }

    /**
     * sendChatAction
     * @method sendAction
     * @param string $chat
     * @param string $action
     * @param int $request = default
     * @return object
     */
    public function sendAction($chat, string $action, int $request = 0){
        return $this->request('sendChatAction', array(
            'chat_id' => $chat,
            'action' => $action
        ), $request);
    }

    /**
     * @method forwardMessage
     * @param string $chat
     * @param string $from
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function forwardMessage($chat, $from, $message, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['from_chat_id'] = $from;
        $datas['message_id'] = $message;
        return $this->request('forwardMessage', $datas, $request);
    }

    /**
     * @method deleteMessage
     * @param string $chat
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function deleteMessage($chat, $message, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['message_id'] = $message;
        return $this->request('deleteMessage', $datas, $request);
    }

    /**
     * @method sendMessage
     * @param string $chat
     * @param string $text
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendMessage($chat, $text, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['text'] = $text;
        $res = $this->request('sendMessage', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method editMessageText
     * @param string $chat
     * @param int $message
     * @param string $text
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function editMessageText($chat, $message, $text, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['message_id'] = $message;
        $datas['text'] = $text;
        return $this->request('editMessageText', $datas, $request);
    }
    
    /**
     * @method editMessageCaption
     * @param string $chat
     * @param int $message
     * @param string $caption
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function editMessageCaption($chat, $message, $caption, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['message_id'] = $message;
        $datas['caption'] = $caption;
        return $this->request('editMessageCaption', $datas, $request);
    }

    /**
     * @method editMessageReplyMarkup
     * @param string $chat
     * @param int $message
     * @param array $keyboard
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function editMessageReplyMarkup($chat, $message, $keyboard, array $datas = array(), int $request = 0){
        if(is_array($keyboard) && !isset($keyboard['inline_keyboard']))
            $keyboard = array('inline_keyboard' => $keyboard);
        $datas['chat_id'] = $chat;
        $datas['message_id'] = $message;
        $datas['reply_markup'] = $keyboard;
        return $this->request('editMessageReplyMarkup', $datas, $request);
    }

    /**
     * @method deleteMessageReplyMarkup
     * @param string $chat
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function deleteMessageReplyMarkup($chat, $message, array $datas = array(), int $request = 0){
        return $this->editMessageReplyMarkup($chat, $message, array(), $datas, $request);
    }

    /**
     * @method sendPhoto
     * @param string $chat
     * @param mixed $photo
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendPhoto($chat, $photo, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['photo'] = $photo;
        $res = $this->request('sendPhoto', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendAudio 
     * @param string $chat
     * @param mixed $audio
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendAudio($chat, $audio, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['audio'] = $audio;
        $res = $this->request('sendAudio', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendDocument
     * @param string $chat
     * @param mixed $document
     * @param array $datas = []
     * @param int $request = default
     * @param \ATL\Plugin\SentMessage
     */
    public function sendDocument($chat, $document, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['document'] = $document;
        $res = $this->request('sendDocument', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendVideo
     * @param string $chat
     * @param mixed $video
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVideo($chat, $video, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['video'] = $video;
        $res = $this->request('sendVideo', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendAnimation
     * @param string $chat
     * @param mixed $animation
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendAnimation($chat, $animation, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['animation'] = $animation;
        $res = $this->request('sendAnimation', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendVoice
     * @param string $chat
     * @param mixed $voice
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVoice($chat, $voice, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['voice'] = $voice;
        $res = $this->request('sendVoice', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendVideoNote
     * @param string $chat
     * @param mixed $video_note
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendVideoNote($chat, $video_note, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['video_note'] = $video_note;
        $res = $this->request('sendVideoNote', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendSticker
     * @param string $chat
     * @param mixed $sticker
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendSticker($chat, $sticker, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['sticker'] = $sticker;
        $res = $this->request('sendSticker', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendDice
     * @param string $chat
     * @param string $emoji
     * @param array $datas = []
     * @param int $request = default
     * @return \ATL\Plugin\SentMessage
     */
    public function sendDice($chat, $emoji, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['emoji'] = $emoji;
        $res = $this->request('sendDice', $datas, $request); 
        if(is_bool($res))
            return $res;
        return new \ATL\Plugin\SentMessage($this, $res);
    }

    /**
     * @method sendContact
     * @param string $chat
     * @param string $phone
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendContact($chat, string $phone, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['phone_number'] = $phone;
        return $this->request('sendContact', $datas, $request);
    }

    /**
     * @method sendLocation
     * @param string $chat
     * @param float $latitude
     * @param float $logitude
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendLocation($chat, float $latitude, float $logitude, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['latitude'] = $latitude;
        $datas['longitude'] = $longitude;
        return $this->request('sendLocation', $datas, $request);
    }
    
    /**
     * @method sendVenue
     * @param string $chat
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendVenue($chat, float $latitude, float $logitude, string $title, string $address, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['latitude'] = $latitude;
        $datas['longitude'] = $longitude;
        $datas['title'] = $title;
        $datas['address'] = $address;
        return $this->request('sendVenue', $datas, $request);
    }

    /**
     * @method sendPoll
     * @param string $chat
     * @param string $question
     * @param array $options
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendPoll($chat, string $question, array $options, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['question'] = $question;
        $datas['options'] = $options;
        return $this->request('sendPoll', $datas, $request);
    }
    
    /**
     * @method copyMessage
     * @param string $chat
     * @param string $from
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function copyMessage($chat, $from, $message, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['from_chat_id'] = $from;
        $datas['message_id'] = $message;
        return $this->request('copyMessage', $datas, $request);
    }

    /**
     * @method getChatMemberCount
     * @param string $chat
     * @param array $datas = []
     * @param int $request = default
     * @return int
     */
    public function getChatMemberCount($chat, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        return $this->request('getChatMemberCount', $datas, $request);
    }

    /**
     * @method getChatMember
     * @param string $chat
     * @param string $user
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function getChatMember($chat, $user, array $datas = array(), int $request = 0){
        $datas['chat_id'] = $chat;
        $datas['user_id'] = $user;
        return $this->request('getChatMember', $datas, $request);
    }

    /**
     * @method sendAnswerCallbackQuery
     * @param string $id
     * @param mixed $text
     * @param bool $alert = false
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function sendAnswerCallbackQuery(string $id, $text, bool $alert = false, array $datas = array(), int $request = 0){
        $datas['callback_query_id'] = $id;
        $datas['text'] = $text;
        $datas['show_alert'] = $alert;
        return $this->request('answerCallbackQuery', $datas, $request);
    }
}
?>