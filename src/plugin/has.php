<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

trait Has {
    /**
     * @method hasChat
     * @param int $id
     * @return bool
     */
    public function hasChat(int $id){
        return $this->answer->cid == $id;
    }
    
    /**
     * @method hasUser
     * @param int $id
     * @return bool
     */
    public function hasUser(int $id){
        return $this->answer->uid == $id;
    }

    /**
     * @method hasOwner
     * @param int $id = cid
     * @return bool
     */
    public function hasOwner(int $id = null){
        if(!$id)
            $id = $this->answer->cid;
        return $this->owner->has($id);
    }

    /**
     * @method hasAdmin
     * @param int $id = cid
     * @return bool
     */
    public function hasAdmin(int $id = null){
        if(!$id)
            $id = $this->answer->cid;
        if(!$id)
            return false;
        return $this->admin->has($id);
    }

    /**
     * @method hasOwnerUser
     * @param int $id = uid
     * @return bool
     */
    public function hasOwnerUser(int $id = null){
        if(!$id)
            $id = $this->answer->uid;
        return $this->owner->has($id);
    }

    /**
     * @method hasAdminUser
     * @param int $id = uid
     * @return bool
     */
    public function hasAdminUser(int $id = null){
        if(!$id)
            $id = $this->answer->uid;
        return $this->admin->has($id);
    }

    /**
     * @method hasMessage
     * @return bool
     */
    public function hasMessage(){
        return (bool)$this->message;
    }
    
    /**
     * @method hasReply
     * @return bool
     */
    public function hasReply(){
        return $this->message && $this->message->reply;
    }

    /**
     * @method hasReplyTo
     * @param int $id = self bot id
     * @return bool
     */
    public function hasReplyTo(int $id = null){
        if(!$id)
            $id = $this->botid;
        return $this->message && $this->message->reply && $this->message->reply->from->id == $id;
    }

    /**
     * @method hasText
     * @param string $text = anything
     * @return bool
     */
    public function hasText($text = null){
        return $this->message && $this->message->type == 'text' && (!$text || $this->message->text == $this->lang->select($text));
    }

    /**
     * @method hasKeyboardButton
     * @param string $index
     * @return bool
     */
    public function hasKeyboardButton(string $index){
        return $this->message && $this->message->type == 'text' && isset($this->keyboard->buttons[$index]['text'])
            && $this->message->text == $this->lang->select($this->keyboard->buttons[$index]['text']);
    }

    /**
     * @method hasNumeric
     * @return bool
     */
    public function hasNumeric(){
        return $this->message && $this->message->type == 'text' && is_numeric($this->message->text);
    }

    /**
     * @method hasUsername
     * @param string $text = message
     * @return string $username without @
     */
    public function hasUsername(string $text = null){
        if($text === null){
            if(!$this->message || $this->message->type != 'text')
                return false;
            $text = $this->message->text;
        }
        if(!preg_match("/^@{0,1}([a-zA-Z](?:[a-zA-Z0-9]|(?<!_)_){4,31}(?<!_))$/is", $text, $match))
            return false;
        return $match[1];
    }

    /**
     * @method hasCommand
     * @param string $prefix = '/'
     * @param string $command = anything
     * @return array of [command, after command] or string of after command or bool
     */
    public function hasCommand(string $prefix = '/', string $command = null){
        if(!$this->message || $this->message->type != 'text')
            return false;
        $text = $this->message->text;
        $prefixreg = preg_quote($prefix, '/');
        if(!preg_match("/^[{$prefixreg}]([a-zA-Z0-9_]{1,32})((?:\s.+){0,1})$/s", $text, $matches))
            return false;
        $matches[2] = ltrim($matches[2]);
        if(!$command)
            return array($matches[1], $matches[2]);
        if(strtolower($matches[1]) != strtolower($command))
            return false;
        if($matches[2] === '')
            return true;
        return $matches[2];
    }

    /**
     * @method hasStart
     * @return string after start or bool
     */
    public function hasStart(){
        return $this->hasCommand('/', 'start');
    }

    /**
     * @method hasCaption
     * @param string $caption = anything
     * @return bool
     */
    public function hasCaption($caption = null){
        return $this->message && isset($this->message->caption) && (!$caption || $this->message->caption == $this->lang->select($caption));
    }

    /**
     * @method pregText
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregText(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->message && $this->message->type == 'text')
            $text = $this->message->text;
        elseif(!$this->message || isset($this->message->caption))
            $text = $this->message->caption;
        else return false;
        return preg_match($pattern, $text, $matches, $flags, $offset);
    }

    /**
     * @method pregTextAll
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregTextAll(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->message && $this->message->type == 'text')
            $text = $this->message->text;
        elseif(!$this->message || isset($this->message->caption))
            $text = $this->message->caption;
        else return false;
        return preg_match_all($pattern, $text, $matches, $flags, $offset);
    }

    /**
     * @method pregReplyText
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregReplyText(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->message && isset($this->message->reply) && $this->message->reply->type == 'text')
            $text = $this->message->reply->text;
        elseif($this->message && !sset($this->message->reply) && isset($this->message->reply->caption))
            $text = $this->message->reply->text;
        else return false;
        return preg_match($pattern, $text, $matches, $flags, $offset);
    }

    /**
     * @method pregReplyTextAll
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregReplyTextAll(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->message && isset($this->message->reply) && $this->message->reply->type == 'text')
            $text = $this->message->reply->text;
        elseif($this->message && !sset($this->message->reply) && isset($this->message->reply->caption))
            $text = $this->message->reply->text;
        else return false;
        return preg_match_all($pattern, $text, $matches, $flags, $offset);
    }

    /**
     * @method hasPhoto
     * @return bool
     */
    public function hasPhoto(){
        return $this->message && $this->message->type == 'photo';
    }

    /**
     * @method hasReplyPhoto
     * @return bool
     */
    public function hasReplyPhoto(){
        return $this->message && isset($this->message->reply) && $this->message->reply->type == 'photo';
    }
    
    /**
     * @method hasDocument
     * @return bool
     */
    public function hasDocument(){
        return $this->message && $this->message->type == 'document';
    }

    /**
     * @method hasReplyDocument
     * @return bool
     */
    public function hasReplyDocument(){
        return $this->message && isset($this->message->reply) && $this->message->reply->type == 'document';
    }
    
    /**
     * @method hasAudio
     * @return bool
     */
    public function hasAudio(){
        return $this->message && $this->message->type == 'audio';
    }

    /**
     * @method hasReplyAudio
     * @return bool
     */
    public function hasReplyAudio(){
        return $this->message && isset($this->message->reply) && $this->message->reply->type == 'audio';
    }

    /**
     * @method hasCallback
     * @param string $data = anything
     * @return bool
     */
    public function hasCallback(string $data = null){
        if($this->update->type != 'callback_query')
            return false;
        if(!$data)
            return true;
        return $this->update->callback_query->data == $data;
    }

    /**
     * @method hasInline
     * @param string $query = anything
     * @return bool
     */
    public function hasInline(string $query = null){
        if($this->update->type != 'inline_query')
            return false;
        if(!$query)
            return true;
        return $this->update->inline_query->query == $query;
    }

    /**
     * @method hasChosenInline
     * @param string $query = anything
     * @return bool
     */
    public function hasChosenInline(string $query = null){
        if($this->update->type != 'chosen_inline_result')
            return false;
        if(!$query)
            return true;
        return $this->update->chosen_inline_query->query == $query;
    }

    /**
     * @method pregCallback
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregCallback(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'callback_query')
            return false;
        $data = $this->update->callback_query->data;
        return preg_match($pattern, $data, $matches, $flags, $offset);
    }

    /**
     * @method pregCallbackAll
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregCallbackAll(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'callback_query')
            return false;
        $data = $this->update->callback_query->data;
        return preg_match_all($pattern, $data, $matches, $flags, $offset);
    }
    
    /**
     * @method pregInline
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregInline(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'inline_query')
            return false;
        $query = $this->update->inline_query->query;
        return preg_match($pattern, $query, $matches, $flags, $offset);
    }

    /**
     * @method pregInlineAll
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregInlineAll(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'inline_query')
            return false;
        $query = $this->update->inline_query->query;
        return preg_match_all($pattern, $query, $matches, $flags, $offset);
    }
        
    /**
     * @method pregChosenInline
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregChosenInline(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'chosen_inline_result')
            return false;
        $query = $this->update->chosen_inline_result->query;
        return preg_match($pattern, $query, $matches, $flags, $offset);
    }

    /**
     * @method pregChosenInlineAll
     * @param string $pattern
     * @param array &$matches = null
     * @param int $flags = 0
     * @param int $offset = 0
     * @return bool
     */
    public function pregChosenInlineAll(string $pattern, array &$matches = null, int $flags = 0, int $offset = 0){
        if($this->update->type != 'chosen_inline_result')
            return false;
        $query = $this->update->chosen_inline_result->query;
        return preg_match_all($pattern, $query, $matches, $flags, $offset);
    }

    /**
     * @method hasChatType
     * @param string $type
     * @return bool
     */
    public function hasChatType(string $type){
        return $this->chat && $this->chat->type == $type;
    }

    /**
     * @method hasGroup
     * @return bool
     */
    public function hasGroup(){
        return $this->chat && in_array($this->chat->type, array('group', 'supergroup'));
    }

    /**
     * @method hasLinkText
     * @return bool
     */
    public function hasLinkText(){
        if(!$this->message || $this->message->type != 'text')
            return false;
        $text = $this->message->text;
        return filter_var(str_replace(' ', '+', $text), FILTER_VALIDATE_URL);
    }
    
    /**
     * @method hasLinkReply
     * @return bool
     */
    public function hasLinkReply(){
        if(!$this->message || !$this->message->reply || $this->message->reply->type != 'text')
            return false;
        $text = $this->message->reply->text;
        return filter_var(str_replace(' ', '+', $text), FILTER_VALIDATE_URL);
    }

    /**
     * @method getText
     * @return string text or caption or false
     */
    public function getText(){
        if(!$this->message)
            return false;
        if($this->message->type == 'text')
            return $this->message->text;
        if(isset($this->message->caption))
            return $this->message->caption;
        return false;
    }

    /**
     * @method getReplyText
     * @return string text or caption or false
     */
    public function getReplyText(){
        if(!$this->message || !isset($this->message->reply))
            return false;
        if($this->message->reply->type == 'text')
            return $this->message->reply->text;
        if(isset($this->message->reply->caption))
            return $this->message->reply->caption;
        return false;
    }
}
?>