<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL;

class Parser {
    /**
     * @static
     * @method datasJsonify
     * @param array $datas
     * @param \ATL\Defaults $default = null
     * @return $datas
     */
    public static function datasJsonify(array $datas, \ATL\Defaults $default = null){
        if(isset($datas['reply_markup'])){
            if(is_array($datas['reply_markup'])){
                if(!isset($datas['reply_markup']['resize_keyboard']) && isset($datas['reply_markup']['keyboard']) && $default && $default->resize_keyboard)
                    $datas['reply_markup']['resize_keyboard'] = true;
                $datas['reply_markup'] = json_encode($datas['reply_markup']);
            }elseif(is_string($datas['reply_markup']) && $default && $default->resize_keyboard){
                $keyboard = json_decode($datas['reply_markup']);
                if(is_array($keyboard) && !isset($keyboard['resize_keyboard'])){
                    $keyboard['resize_keyboard'] = true;
                    $datas['reply_markup'] = json_encode($keyboard);
                }
            }
        }
        if(!isset($datas['parse_mode']) && $default && $default->parse_mode)
            $datas['parse_mode'] = $default->parse_mode;
        if(isset($datas['parse_mode'])){
            switch(strtolower($datas['parse_mode'])){
                case 'markdownv2':
                    $datas['parse_mode'] = 'MarkDownV2';
                break;
                case 'markdown':
                    $datas['parse_mode'] = 'MarkDown';
                break;
                case 'html':
                    $datas['parse_mode'] = 'HTML';
                break;
            }
        }
        if(isset($datas['text']) && !is_string($datas['text']) && !is_numeric($datas['text'])){
            $datas['text'] = json_encode($datas['text'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $datas['text'] = '`'.str_replace(['\\', '`'], ['\\\\', '\\`'], $datas['text']).'`';
            $datas['parse_mode'] = 'MarkDownV2';
        }elseif(isset($datas['caption']) && !is_string($datas['caption']) && !is_numeric($datas['caption'])){
            $datas['caption'] = json_encode($datas['caption'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $datas['caption'] = '`'.str_replace(['\\', '`'], ['\\\\', '\\`'], $datas['caption']).'`';
            $datas['parse_mode'] = 'MarkDownV2';
        }
        if(isset($datas['entities']) && is_array($datas['entities']))
            $datas['entities'] = json_encode($datas['entities']);
        if(isset($datas['caption_entities']) && is_array($datas['caption_entities']))
            $datas['caption_entities'] = json_encode($datas['caption_entities']);
        return $datas;
    }
    
    /**
     * @static
     * @method datasInsertKeyboard
     * @param array $datas
     * @param \ATL\Keyboard $keyboard
     * @return array
     */
    public static function datasInsertKeyboard(array $datas, \ATL\Keyboard $keyboard){
        if(isset($datas['reply_markup']))
            $datas['reply_markup'] = $keyboard->parse($datas['reply_markup']);
        return $datas;
    }

    /**
     * @static
     * @method datasInsertKeyboardLang
     * @param array $datas
     * @param \ATL\Keyboard $keyboard
     * @param \ATL\Plugin\Lang $lang
     * @return array
     */
    public static function datasInsertKeyboardLang(array $datas, \ATL\Keyboard $keyboard, \ATL\Plugin\Lang $lang){
        if(isset($datas['reply_markup']))
            $datas['reply_markup'] = $keyboard->parseLang($datas['reply_markup'], $lang);
        return $datas;
    }

    /**
     * @static
     * @method selectLang
     * @param array $datas
     * @param \ATL\Plugin\Lang $lang
     * @return array
     */
    public static function selectLang(array $datas, \ATL\Plugin\Lang $lang){
        if(isset($datas['text']))
            $datas['text'] = $lang->getText($datas['text']);
        if(isset($datas['caption']))
            $datas['caption'] = $lang->getText($datas['caption']);
        return $datas;
    }

    /**
     * @static
     * @method datasFileProcessing
     * @param array $datas
     * @param string $method
     * @return array
     */
    public static function datasFileProcessing(array $datas, string $method){
        switch(strtolower($method)){
            case 'sendphoto':
                $index = 'photo';
            break;
            case 'sendaudio':
                $index = 'audio';
            break;
            case 'senddocument':
                $index = 'document';
            break;
            case 'sendvideo':
                $index = 'video';
            break;
            case 'sendanimation':
                $index = 'animation';
            break;
            case 'sendvoice':
                $index = 'voice';
            break;
            case 'sendvideonote':
                $index = 'video_note';
            break;
            case 'sticker':
                $index = 'sticker';
            break;
        }
        if(isset($index) && isset($datas[$index]))
            $file = $datas[$index];
        if(isset($datas['file'])){
            $file = $datas['file'];
            unset($datas['file']);
        }
        if(isset($file) && is_string($file)){
            if(isset($datas['mime_type'])){
                $mimetype = $datas['mime_type'];
                unset($datas['mime_type']); 
            }else
                $mimetype = '';
            if(isset($datas['file_name'])){
                $filename = $datas['file_name'];
                unset($datas['file_name']);
            }else
                $filename = '';
            if(strpos($file, '@') === 0)
                $datas[$index] = new \CURLFile(substr($file, 1), $mimetype, $filename);
            //elseif(strpos($datas['file_id'], '/') !== false || strpos($datas['file_id'], '.') !== false)
            //    $datas[$index] = new CURLFile($datas['file_id'], $mimetype, $filename);
            else
                $datas[$index] = $file;
        }elseif(isset($file) && is_object($file)){
            if(isset($file->$index))
                $file = $file->$index;
            if($index == 'photo' && is_array($file)){
                $lfk = count($file) - 1;
                if(isset($file[$lfk]->file_id))
                    $file = $file[$lfk]->file_id;
            }
            if(isset($file->file_id))
                $file = $file->file_id;
        }elseif(isset($file) && is_array($file) && $index == 'photo'){
            $lfk = count($file) - 1;
            if(isset($file[$lfk]->file_id))
                $file = $file[$lfk]->file_id;
        }
        if(isset($datas['thumb']) && is_string($datas['thumb'])){
            $thumb = $datas['thumb'];
            if(strpos($thumb, '@') === 0)
                $thumb = new \CURLFile(substr($thumb, 1));
            $datas['thumb'] = $thumb;
        }
        return $datas;
    }

    /**
     * Parse message object
     * @static
     * @method parseMessage
     * @param object $message
     * @return object
     */
    public static function parseMessage(object $message){
        if(isset($message->text))$message->type = 'text';
        elseif(isset($message->animation))$message->type = 'animation';
        elseif(isset($message->audio))$message->type = 'audio';
        elseif(isset($message->document))$message->type = 'document';
        elseif(isset($message->photo))$message->type = 'photo';
        elseif(isset($message->sticker))$message->type = 'sticker';
        elseif(isset($message->video))$message->type = 'video';
        elseif(isset($message->video_note))$message->type = 'video_note';
        elseif(isset($message->voice))$message->type = 'voice';
        elseif(isset($message->contact))$message->type = 'contact';
        elseif(isset($message->dice))$message->type = 'dice';
        elseif(isset($message->game))$message->type = 'game';
        elseif(isset($message->poll))$message->type = 'poll';
        elseif(isset($message->venue))$message->type = 'venue';
        elseif(isset($message->location))$message->type = 'location';
        elseif(isset($message->new_chat_members))$message->type = 'new_chat_members';
        elseif(isset($message->left_chat_member))$message->type = 'left_chat_member';
        elseif(isset($message->new_chat_title))$message->type = 'new_chat_title';
        elseif(isset($message->new_chat_photo))$message->type = 'new_chat_photo';
        elseif(isset($message->delete_chat_photo))$message->type = 'delete_chat_photo';
        elseif(isset($message->group_chat_created))$message->type = 'group_chat_created';
        elseif(isset($message->supergroup_chat_created))$message->type = 'supergroup_chat_created';
        elseif(isset($message->channel_chat_created))$message->type = 'channel_chat_created';
        elseif(isset($message->pinned_message))$message->type = 'pinned_message';
        elseif(isset($message->invoice))$message->type = 'invoice';
        elseif(isset($message->successful_payment))$message->type = 'successful_payment';
        else $message->type = 'unknown';
        if(isset($message->reply_to_message)){
            $message->reply = self::parseMessage($message->reply_to_message);
            unset($message->reply_to_message);
        }
        if(isset($message->forward_from_chat)){
            $message->forward_type = $message->forward_from_chat->type;
        }elseif(isset($message->forward_from)){
            $message->forward_type = 'private';
        }elseif(isset($message->forward_sender_name))
            $message->forward_type = 'private';
        if(isset($message->pinned_message)){
            $message->pinned_message = self::parseMessage($message->pinned_message);
        }
        return $message;
    }

    /**
     * @static
     * @method parseMessageFile
     * @param object $message
     * @return object or null
     */
    public static function parseMessageFile(object $message = null){
        if(!$message)
            return null;
        switch($message->type){
            case 'animation':
                return $message->animation;
            case 'audio':
                return $message->audio;
            case 'document':
                return $message->document;
            case 'photo':
                $photo = $message->photo;
                return $photo[count($photo)-1];
            case 'sticker':
                return $message->sticker;
            case 'video':
                return $message->video;
            case 'vide_note':
                return $message->video_note;
            case 'voice':
                return $message->voice;
            default:
                return null;
        }
    }

    /**
     * @static
     * @method parseUpdate
     * @param object $update
     * @return object
     */
    public static function parseUpdate(object $update){
        if(isset($update->message))$update->type = 'message';
        elseif(isset($update->edited_message))$update->type = 'edited_message';
        elseif(isset($update->channel_post))$update->type = 'channel_post';
        elseif(isset($update->edited_channel_post))$update->type = 'edited_channel_post';
        elseif(isset($update->inline_query))$update->type = 'inline_query';
        elseif(isset($update->chosen_inline_result))$update->type = 'chosen_inline_result';
        elseif(isset($update->callback_query))$update->type = 'callback_query';
        elseif(isset($update->shipping_query))$update->type = 'shipping_query';
        elseif(isset($update->pre_checkout_query))$update->type = 'pre_checkout_query';
        elseif(isset($update->poll))$update->type = 'poll';
        elseif(isset($update->poll_answer))$update->type = 'poll_answer';
        elseif(isset($update->my_chat_member))$update->type = 'my_chat_member';
        elseif(isset($update->chat_member))$update->type = 'chat_member';
        elseif(isset($update->chat_join_request))$update->type = 'chat_join_request';
        else $update->type = 'unknown';
        return $update;
    }

    /**
     * @static
     * @method datasUpdateProcessing
     * @param array $datas
     * @param \ATL\Answer $answer = null
     * @return array
     */
    public static function datasUpdateProcessing(array $datas, \ATL\Answer $answer = null){
        if(isset($datas['reply'])){
            $datas['reply_to_message_id'] = $datas['reply'];
            unset($datas['reply']);
        }
        if(isset($datas['reply_to_message_id']) && $datas['reply_to_message_id'] === 0 && $answer && $answer->mid)
            $datas['reply_to_message_id'] = $answer->mid;
        else{
            if(isset($datas['reply_to_message_id']->message))
                $datas['reply_to_message_id'] = $datas['reply_to_message_id']->message;
            if(isset($datas['reply_to_message_id']->message_id))
                $datas['reply_to_message_id'] = $datas['reply_to_message_id']->message_id;
        }
        if(isset($datas['message_id']) && $datas['message_id'] === 0 && $answer && $answer->mid)
            $datas['message_id'] = $answer->mid;
        else{
            if(isset($datas['message_id']->message))
                $datas['message_id'] = $datas['message_id']->message;
            if(isset($datas['message_id']->message_id))
                $datas['message_id'] = $datas['message_id']->message_id;
        }
        if(isset($datas['user_id']) && $datas['user_id'] === 0 && $answer && $answer->cid)
            $datas['user_id'] = $answer->fid ? $answer->fid : $answer->cid;
        else{
            if(isset($datas['user_id']->message))
                $datas['user_id'] = $datas['user_id']->message;
            if(isset($datas['user_id']->user))
                $datas['user_id'] = $datas['user_id']->user->id;
            if(isset($datas['user_id']->id))
                $datas['user_id'] = $datas['user_id']->id;
            if(isset($datas['user_id']->ids))
                $datas['user_id'] = $datas['user_id']->ids;
            if(isset($datas['user_id']['id']))
                $datas['user_id'] = $datas['user_id']['id'];
        }
        if(isset($datas['chat_id']) && $datas['chat_id'] === 0 && $answer && $answer->cid)
            $datas['chat_id'] = $answer->cid;
        else{
            if(isset($datas['chat_id']->message))
                $datas['chat_id'] = $datas['chat_id']->message;
            if(isset($datas['chat_id']->chat))
                $datas['chat_id'] = $datas['chat_id']->chat->id;
            if(isset($datas['chat_id']->id))
                $datas['chat_id'] = $datas['chat_id']->id;
            if(isset($datas['chat_id']->ids))
                $datas['chat_id'] = $datas['chat_id']->ids;
            if(isset($datas['chat_id']['id']))
                $datas['chat_id'] = $datas['chat_id']['id'];
        }
        return $datas;
    }

    /**
     * @static
     * @method parseMultidata
     * @param array $datas
     * @return array
     */
    public static function parseMultidata(array $datas){
        $multi = array();
        if(isset($datas['chat_id']) && is_array($datas['chat_id'])){
            foreach($datas['chat_id'] as $id){
                $data = $datas;
                $data['chat_id'] = $id;
                $multi[] = self::parseMultidata($data);
            }
            $multi = array_merge(...$multi);
        }elseif(isset($datas['user_id']) && is_array($datas['user_id'])){
            foreach($datas['user_id'] as $id){
                $data = $datas;
                $data['user_id'] = $id;
                $multi[] = self::parseMultidata($data);
            }
            $multi = array_merge(...$multi);
        }elseif(isset($datas['message_id']) && is_array($datas['message_id'])){
            foreach($datas['message_id'] as $id){
                $data = $datas;
                $data['message_id'] = $id;
                $multi[] = self::parseMultidata($data);
            }
            $multi = array_merge(...$multi);
        }else{
            $multi[] = $datas;
        }
        return $multi;
    }

    /**
     * @static
     * @method parseMultidatas
     * @param array $datas
     * @return $datas
     */
    public static function parseMultidatas(array $datas){
        foreach($datas as &$data)
            $data = self::parseMultidata($data);
        $datas = array_merge(...$datas);
        return $datas;
    }

    /**
     * @static
     * @method appendAutoAction
     * @param array $datas
     * @return $datas
     */
    public static function appendAutoAction(array $datas){
        $res = array();
        foreach($datas as $data){
            if(!isset($data['chat_id'])){
                $res[] = $data;
                continue;
            }
            switch(strtolower($data['method'])){
                case 'sendmessage':
                    $action = 'typing';
                break;
                case 'sendphoto':
                    $action = 'upload_photo';
                break;
                case 'sendvideo':
                    $action = 'upload_video';
                break;
                case 'sendaudio':
                    $action = 'upload_audio';
                break;
                case 'senddocument':
                    $action = 'upload_document';
                break;
                case 'sendlocation':
                    $action = 'find_location';
                break;
                case 'sendvideonote':
                    $action = 'upload_video_note';
                break;
                default:
                    $action = false;
            }
            if($action){
                $res[] = array(
                    'method' => 'sendChatAction',
                    'chat_id' => $data['chat_id'],
                    'action' => $action
                );
            }
            $res[] = $data;
        }
        return $res;
    }
}
?>