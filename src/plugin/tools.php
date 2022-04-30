<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class Tools {
    /**
     * @method progress
     * @param int $step
     * @param int $max = 100
     * @param int $size = 9
     * @param string $fillstr = '◾'
     * @param string $emptystr = '◽'
     * @param bool $pre = false
     */
    public function progress(int $step, int $max = 100, int $size = 9, string $fillstr = null, string $emptystr = null, bool $pre = false){
        if($fillstr === null)
            $fillstr = '◾';
        if($emptystr === null)
            $emptystr = '◽';
        $fill = floor($step / $max * $size);
        $empty = $size - $fill;
        $fill = $fill == 0 ? '' : str_repeat($fillstr, $fill);
        $empty = $empty == 0 ? '' : str_repeat($emptystr, $empty);
        $pre = round($step / $max * 100);
        $res = $fill.$empty;
        if($pre){
            $pre = round($step / $max * 100);
            $res.= " %$pre";
        }
        return $res;
    }
}
?>