<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * API Answer request methods
 */
namespace ATL\Plugin;
use \ATL\Logger;

trait Answer {
    /**
     * @var array $push_answers = []
     * @var int $switch_answers = 1
     */
    public $push_answers = array();
    public $switch_answers = 1;

    /**
     * Answers switching
     * 
     * @method switchAnswers
     * @param int $dest = \ATL\Answer::CHAT
     */
    public function switchAnswers(int $dest = 1){
        $this->switch_answers = $dest;
    }

    /**
     * Push answers
     * 
     * @method pushAnswers
     * @param int $dest = \ATL\Answers::CHAT
     */
    public function pushAnswers(int $dest = 1){
        $this->push_answers[] = $this->switch_answers;
        $this->switch_answers = $dest;
    }

    /**
     * Pop answers
     * 
     * @method popAnswers
     * @return int or false
     */
    public function popAnswers(){
        if(!isset($this->push_answers[0])){
            Logger::log("\ATL::popAnswers(): There is not exists any pushed answer switcher");
            return false;
        }
        return $this->switch_answers = array_pop($this->push_answers);
    }

    /**
     * @method whereAnswers
     * @return int or false
     */
    public function whereAnswers(){
        if(!$this->update){
            Logger::log("\ATL::update(): There is no update object for reading");
            return false;
        }
        switch($this->switch_answers){
            case \ATL\Answer::CHAT:
                $answer = $this->answer->cid;
                $dest = 'chat';
            break;
            case \ATL\Answer::USER:
                $answer = $this->answer->uid;
                $dest = 'user';
            break;
            case \ATL\Answer::OWNER:
                $answer = $this->owner->id;
                $dest = 'owner';
            break;
            case \ATL\Answer::ADMIN:
                $answer = $this->admin->ids;
                if($answer === array())
                    $answer = null;
                $dest = 'admin';
            break;
            default:
                $answer = $this->switch_answers;
                $dest = 'specified chat';
        }
        if(!$answer){
            if($this->answer->cid){
                Logger::log("\ATL::whereAnswers(): There is not exists any chat id for $dest answering. By default, the current chat was selected to reply");
                $answer = $this->answer->cid;
            }else{
                Logger::log("\ATL::whereAnswers(): There is not exists any chat id for $dest answering");
                return false;
            }
        }
        return $answer;
    }

    /**
     * @method hasAnswers
     * @param mixed $id
     * @return bool
     */
    public function hasAnswers($id){
        $where = $this->whereAnswers();
        if(!is_array($where))
            $where = array($where);
        if(!is_array($id))
            $id = array($id);
        return array_diff($where, $id) !== $where;
    }

    /**
     * @method setAnswers
     * @param array $datas
     * @param int $id
     * @return array
     */
    public function setAnswers(array $datas, int $id){
        if($this->switch_answers == \ATL\Answer::CHAT && isset($datas['chat_id'])){
            $datas['chat_id'] = $id;
        }elseif(isset($datas['user_id'])){
            $datas['user_id'] = $id;
        }else{
            $datas['chat_id'] = $id;
        }
        return $datas;
    }

    /**
     * answering by sendChatAction
     * @method answerAction
     * @param string $action
     * @param int $request = 0
     * @return object
     */
    public function answerAction(string $action, int $request = 0){
        return $this->sendAction($this->whereAnswers(), $action, $request);
    }

    /**
     * answering by forwardMessage
     * @method answerForwardMessage
     * @param string $from
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerForwardMessage($from, $message, array $datas = array(), int $request = 0){
        return $this->forwardMessage($this->whereAnswers(), $from, $message, $datas, $request);
    }

    /**
     * answering by deleteMessage
     * @method answerDeleteMessage
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerDeleteMessage($message, array $datas = array(), int $request = 0){
        return $this->deleteMessage($this->whereAnswers(), $message, $datas, $request);
    }

    /**
     * answering by sendMessage
     * @method answerMessage
     * @param string $text
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerMessage($text, array $datas = array(), int $request = 0){
        return $this->sendMessage($this->whereAnswers(), $text, $datas, $request);
    }

    /**
     * answering by editMessageText
     * @method answerEditMessageText
     * @param int $message
     * @param string $text
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerEditMessageText($message, $text, array $datas = array(), int $request = 0){
        return $this->editMessageText($this->whereAnswers(), $message, $text, $datas, $request);
    }
    
    /**
     * answering by editMessageCaption
     * @method answerEditMessageCaption
     * @param int $message
     * @param string $caption
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerEditMessageCaption($message, $caption, array $datas = array(), int $request = 0){
        return $this->editMessageCaption($this->whereAnswers(), $message, $caption, $datas, $request);
    }

    /**
     * answering by editMessageReplyMarkup
     * @method answerEditMessageReplyMarkup
     * @param int $message
     * @param array $keyboard
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerEditMessageReplyMarkup($message, $keyboard, array $datas = array(), int $request = 0){
        return $this->editMessageReplyMarkup($this->whereAnswers(), $message, $keyboard, $datas, $request);
    }

    /**
     * answering by deleteMessageReplyMarkup
     * @method answerDeleteMessageReplyMarkup
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerDeleteMessageReplyMarkup($message, array $datas = array(), int $request = 0){
        return $this->answerEditMessageReplyMarkup($message, array(), $datas, $request);
    }

    /**
     * answering by sendPhoto
     * @method answerPhoto
     * @param mixed $photo
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerPhoto($photo, array $datas = array(), int $request = 0){
        return $this->sendPhoto($this->whereAnswers(), $photo, $datas, $request);
    }

    /**
     * answering by sendAudio
     * @method answerAudio
     * @param mixed $audio
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerAudio($audio, array $datas = array(), int $request = 0){
        return $this->sendAudio($this->whereAnswers(), $audio, $datas, $request);
    }

    /**
     * answering by sendDocument
     * @method answerDocument
     * @param mixed $document
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerDocument($document, array $datas = array(), int $request = 0){
        return $this->sendDocument($this->whereAnswers(), $document, $datas, $request);
    }

    /**
     * answering by sendVideo
     * @method answerVideo
     * @param mixed $video
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerVideo($video, array $datas = array(), int $request = 0){
        return $this->sendVideo($this->whereAnswers(), $video, $datas, $request);
    }

    /**
     * answering by sendAnimation
     * @method answerAnimation
     * @param mixed $animation
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerAnimation($animation, array $datas = array(), int $request = 0){
        return $this->sendAnimation($this->whereAnswers(), $animation, $datas, $request);
    }

    /**
     * answering by sendVoice
     * @method answerVoice
     * @param mixed $voice
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerVoice($voice, array $datas = array(), int $request = 0){
        return $this->sendVoice($this->whereAnswers(), $voice, $datas, $request);
    }

    /**
     * answering by sendVideoNote
     * @method answerVideoNote
     * @param mixed $video_note
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerVideoNote($video_note, array $datas = array(), int $request = 0){
        return $this->sendVideoNote($this->whereAnswers(), $video_note, $datas, $request);
    }

    /**
     * answering by sendSticker
     * @method answerSticker
     * @param mixed $sticker
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerSticker($sticker, array $datas = array(), int $request = 0){
        return $this->sendSticker($this->whereAnswers(), $sticker, $datas, $request);
    }

    /**
     * answering by sendDice
     * @method answerDice
     * @param mixed $emoji
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerDice($emoji, array $datas = array(), int $request = 0){
        return $this->sendDice($this->whereAnswers(), $emoji, $datas, $request);
    }

    /**
     * answering by sendConact
     * @method answerContact
     * @param string $phone
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerContact(string $phone, array $datas = array(), int $request = 0){
        return $this->sendContact($this->whereAnswers(), $phone, $datas, $request);
    }

    /**
     * answering by sendLocation
     * @method answerLocation
     * @param float $latitude
     * @param float $logitude
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerLocation(float $latitude, float $longitude, array $datas = array(), int $request = 0){
        return $this->sendLocation($this->whereAnswers(), $latitude, $longitude, $datas, $request);
    }
    
    /**
     * answering by sendVenue
     * @method answerVenue
     * @param float $latitude
     * @param float $logitude
     * @param string $title
     * @param string $address
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerVenue(float $latitude, float $longitude, string $title, string $address, array $datas = array(), int $request = 0){
        return $this->sendVenue($this->whereAnswers(), $latitude, $longitude, $title, $address, $datas, $request);
    }

    /**
     * answering by sendPoll
     * @param string $question
     * @param array $options
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerPoll(string $question, array $options, array $datas = array(), int $request = 0){
        return $this->sendPoll($this->whereAnswers(), $question, $options, $datas, $request);
    }
    
    /**
     * answering by copyMessage
     * @method answerCopyMessage
     * @param string $from
     * @param int $message
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerCopyMessage($from, $message, array $datas = array(), int $request = 0){
        return $this->copyMessage($this->whereAnswers(), $from, $message, $datas, $request);
    }

    /**
     * getChatMemberCount on this chat
     * @method thisChatMemberCount
     * @param array $datas = []
     * @param int $request = default
     * @return int
     */
    public function thisChatMemberCount(array $datas = array(), int $request = 0){
        return $this->getChatMemberCount($this->whereAnswers(), $datas, $request);
    }

    /**
     * getChatMember on this chat
     * @method thisChatMember
     * @param string $user
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function thisChatMember($user, array $datas = array(), int $request = 0){
        return $this->getChatMember($this->whereAnswers(), $datas, $request);
    }

    /**
     * answering by answerCallbackQuery
     * @method answerCallbackQuery
     * @param mixed $text
     * @param bool $alert = false
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerCallbackQuery($text, bool $alert = false, array $datas = array(), int $request = 0){
        if($this->update && $this->update->type == 'callback_query')
            return $this->sendAnswerCallbackQuery($this->update->callback_query->id, $text, $alert, $datas, $request);
        Logger::log("\ATL\Answers::answerCallbackQuery(): There is not exists callback_query update for answering");
        return false;
    }
}
?>