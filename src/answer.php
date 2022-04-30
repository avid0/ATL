<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL;

class Answer {
    /**
     * @var int $pid Update id
     * @var int $cid Chat id
     * @var int $uid User id
     * @var int $mid Message id
     * @var int $rid Reply message id
     * @var int $nid Pinned message id
     * @var int $iid Inline message id
     * @var int $lid Poll id
     */
    public $pid, $cid, $uid, $mid, $rid, $nid, $iid, $lid;

    /**
     * @var string $did File id
     * @var int $date Message date
     */
    public $did, $date;

    /**
     * Answers dest
     * @var int CHAT = 1
     * @var int USER = 2
     * @var int OWNER = 3
     * @var int ADMIN = 4
     */
    const CHAT = 1;
    const USER = 2;
    const OWNER = 3;
    const ADMIN = 4;
}
?>