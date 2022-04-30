<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * API Answer request methods
 */
namespace ATL\Plugin;
use \ATL\Logger;

trait Make {
    /**
     * making photo file by sendPhoto
     * @method makePhoto
     * @param string $chat
     * @param mixed $photo
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function makePhoto($chat, $photo, array $datas = array(), int $request = 0){
        $msg = $this->sendPhoto($chat, $photo, $datas, $request);
        $msg->delete($datas, $request);
        return $msg->photo;
    }
    
    /**
     * answering by making photo file by sendPhoto
     * @method answerMakePhoto
     * @param mixed $photo
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerMakePhoto($photo, array $datas = array(), int $request = 0){
        return $this->makePhoto($this->whereAnswers(), $photo, $datas, $request);
    }
    
    /**
     * making document file by sendDocument
     * @method makeDocument
     * @param string $chat
     * @param mixed $document
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function makeDocument($chat, $document, array $datas = array(), int $request = 0){
        $msg = $this->sendDocument($chat, $document, $datas, $request);
        $msg->delete($datas, $request);
        return $msg->document;
    }
        
    /**
     * answering by making document file by sendDocument
     * @method answerMakeDocument
     * @param mixed $document
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function answerMakeDocument($document, array $datas = array(), int $request = 0){
        return $this->makeDocument($this->whereAnswers(), $document, $datas, $request);
    }
}
?>