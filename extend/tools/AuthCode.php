<?php
//核心代码开始

namespace tools;
/**
 * 安全的验证码要：验证码文字扭曲、旋转，使用不同字体，添加干扰码。
 * @原 作 者： 流水孟春 <cmpan@qq.com>
 * @修    改： flymorn <www.piaoyi.org>
 * @二次开发： 蓝鲸 <mcbbs论坛id：lj2000lj>
 * @三次开发: Fleey <fleey2013@live.cn>
 * @新增了点击验证码的接口
 **/
class  AuthCode
{
    //验证码的session的下标  
    public static $seKey = 'verify_py';   //验证码关键字
    public static $expire = 3000;     // 验证码过期时间（s）    
    //验证码中使用的字符，01IO容易混淆，不用  
    public static $codeSet = '23456789ABCDEFGHJKLMNPQRTUVWXY';
    public static $fontSize = 26;     // 验证码字体大小(px)   
    public static $useCurve = true;   // 是否画混淆曲线   
    public static $useNoise = true;   // 是否添加杂点    
    public static $imageH = 0;        // 验证码图片宽
    public static $imageL = 0;        // 验证码图片长
    public static $length = 6;        // 验证码位数
    public static $bg = array(243, 251, 254);  // 背景   

    protected static $_image = null;     // 验证码图片实例   
    protected static $_color = null;     // 验证码字体颜色   

    /**
     * 判断验证码是否正确
     * @param $name
     * @param $AuthCode
     * @return bool
     */
    public static function CheckAuthCode($name ,$AuthCode){
        if(empty($name) || empty($AuthCode)){
            return false;
        }
        if(!session('AuthCode_'.$name)){
            return false;
        }
        //标准判断错误
        $RightAuthCode = session('AuthCode_'.$name);
        session('AuthCode_'.$name,NULL);
        if(!is_array($AuthCode)){
            if(strtoupper($AuthCode) != strtoupper($RightAuthCode)){
                return false;
            }
            return true;
        }
        //非点击验证码判断姿势
        if(!session('AuthCode_'.$name.'_info') || count($AuthCode)!=strlen($RightAuthCode)){
            return false;
        }
        //判断参数无
        $CalcRet = session('AuthCode_'.$name.'_info');
        session('AuthCode_'.$name.'_info',NULL);
        //时刻清空一些不必要的，避免安全问题
        $StrBox = '';
        foreach ($AuthCode as $Key => $Item){
            foreach ($CalcRet as $PointKey => $Point){
                if($Point['top'] >= $Item['y'] && $Point['bottom'] <= $Item['y']){
                    if($Point['left'] <= $Item['x'] && $Point['right'] >= $Item['x']){
                        $StrBox .= $Point['code'];
                        unset($CalcRet[$PointKey]);
                        unset($AuthCode[$Key]);
                        //删掉不必要的，优化搜索速度
                    }
                }
            }
        }

        if($StrBox != $RightAuthCode){
            return false;
        }
        return true;
    }

    /**
     * 输出验证码并把验证码的值保存的session中
     * @param $name
     * @param $isClickCode
     */
    public static function entry($name , $isClickCode = false)
    {
        // 图片宽(px)   
        self::$imageL || self::$imageL = self::$length * self::$fontSize * 1.5 + self::$fontSize * 1.5;
        // 图片高(px)   
        self::$imageH || self::$imageH = self::$fontSize * 2;
        // 建立一幅 self::$imageL x self::$imageH 的图像   
        self::$_image = imagecreate(self::$imageL, self::$imageH);
        // 设置背景         
        imagecolorallocate(self::$_image, self::$bg[0], self::$bg[1], self::$bg[2]);
        // 验证码字体随机颜色   
        self::$_color = imagecolorallocate(self::$_image, mt_rand(1, 120), mt_rand(1, 120), mt_rand(1, 120));
        // 验证码使用随机字体，保证目录下有这些字体集   
        $ttf = env('ROOT_PATH') . '/public/static/fonts/t' . mt_rand(1, 4) . '.ttf';

        if (self::$useNoise) {
            // 绘杂点   
            self::_writeNoise();
        }
        if (self::$useCurve) {
            // 绘干扰线   
            self::_writeCurve();
        }
        // 绘验证码   
        $code   = array(); // 验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        $Info = [];
        $org = imagecolorallocate(self::$_image,  255,143,0);
        for ($i = 0; $i < self::$length; $i++) {
            if($isClickCode){
                do{
                    $code[$i] = self::$codeSet[mt_rand(0, 28)];
                }while(array_count_values($code)[$code[$i]] == 2);
                //去除重复的字符
                $codeNX   += mt_rand(self::$fontSize * 2, self::$fontSize * 2.2);
            }else{
                $codeNX   += mt_rand(self::$fontSize * 1.2, self::$fontSize * 1.6);
                $code[$i] = self::$codeSet[mt_rand(0, 28)];
            }
            $y = mt_rand(self::$fontSize * 1.5,self::$imageH - 10);
            // 写一个验证码字符
            $Angle = mt_rand(-35, 35);
            imagettftext(self::$_image, self::$fontSize,$Angle, $codeNX, $y, self::$_color, $ttf, $code[$i]);

            if(false){
                $x = $codeNX;
                $AngleRet =  self::AngleCoordsPoint($Angle,$x + (self::$fontSize),$y,$x,$y);
                imageline(self::$_image,$x,$y,$AngleRet[0],$AngleRet[1],$org);
                $AngleRet3 =  self::AngleCoordsPoint($Angle,$x +(self::$fontSize),$y - (self::$fontSize),$x,$y);
                imageline(self::$_image, $AngleRet[0],$AngleRet[1],$AngleRet3[0],$AngleRet3[1],$org);
                $AngleRet2 =  self::AngleCoordsPoint($Angle,$x,$y-(self::$fontSize),$x,$y);
                imageline(self::$_image, $AngleRet3[0],$AngleRet3[1],$AngleRet2[0],$AngleRet2[1],$org);
            }
            //DEBUG专用
            if($isClickCode){
                $Info[$i]['x'] = $codeNX;
                $Info[$i]['y'] = self::$fontSize * 1.5;
                $Info[$i]['angle'] = $Angle;
                $Info[$i]['code'] = $code[$i];
            }

        }
        // 保存验证码
        session('AuthCode_'.$name.'_info',self::CalcPoint($Info,self::$fontSize));
        if($isClickCode){
            shuffle($code);
        }
        session('AuthCode_'.$name,join("", $code));
        cookie('AuthCode_'.$name,join("", $code));
        header('Pragma: no-cache');
        header("content-type: image/JPEG");
        // 输出图像
        imageJPEG(self::$_image);
        imagedestroy(self::$_image);
    }

    /**
     * 快速排序
     * @param $arr
     * @return array
     */
    public static function QuickSort($arr) {
        //先判断是否需要继续进行
        $length = count($arr);
        if($length <= 1) {
            return $arr;
        }
        //如果没有返回，说明数组内的元素个数 多余1个，需要排序
        //选择一个标尺
        //选择第一个元素
        $base_num = $arr[0];
        //遍历 除了标尺外的所有元素，按照大小关系放入两个数组内
        //初始化两个数组
        $left_array = array();//小于标尺的
        $right_array = array();//大于标尺的
        for($i=1; $i<$length; $i++) {
            if($base_num > $arr[$i]) {
                //放入左边数组
                $left_array[] = $arr[$i];
            } else {
                //放入右边
                $right_array[] = $arr[$i];
            }
        }
        //再分别对 左边 和 右边的数组进行相同的排序处理方式
        //递归调用这个函数,并记录结果
        $left_array = self::QuickSort($left_array);
        $right_array = self::QuickSort($right_array);
        //合并左边 标尺 右边
        return array_merge($left_array, array($base_num), $right_array);
    }

    /**
     * 计算旋转后的坐标点位置
     * @param $Data
     * @param $FontSize
     *
     * @return array
     */
    private static function CalcPoint($Data,$FontSize){
        $i = 0;
        foreach ($Data as $Key => $Item){
            $ListX = $ListY = $AngleRet = [];
            $ListX[] = $Item['x'];
            $ListY[] = $Item['y'];
            $AngleRet[] =  self::AngleCoordsPoint($Item['angle'],$Item['x']+$FontSize,$Item['y'],$Item['x'],$Item['y']);
            $AngleRet[] =  self::AngleCoordsPoint($Item['angle'],$Item['x']+$FontSize,$Item['y'] - $FontSize,$Item['x'],$Item['y']);
            $AngleRet[] =  self::AngleCoordsPoint($Item['angle'],$Item['x'],$Item['y'] - $FontSize,$Item['x'],$Item['y']);
            foreach($AngleRet as $item){
                $ListX[] = $item[0];
                $ListY[] = $item[1];
            }
            $ListX =  self::QuickSort($ListX);
            $ListY =  self::QuickSort($ListY);
            //排序获取最大与最小
            $Point[$i]['top'] = $ListY[3] + 5;
            $Point[$i]['bottom'] = $ListY[0] - 2;
            $Point[$i]['left'] = $ListX[0] - 2;
            $Point[$i]['right'] = $ListX[3] + 2;
            $Point[$i]['code'] = $Item['code'];
            $i++;
            //后面的+2 -2 为了增加容错机制。。。我发现每次都好像差那么点点就触发了
        }
        return $Point;
    }


    /**
     * 旋转坐标点
     * @param $Angle
     * @param $X
     * @param $Y
     * @param int $BaseX
     * @param int $BaseY
     * @return array
     */
    private static function AngleCoordsPoint($Angle,$X,$Y,$BaseX = 0,$BaseY = 0){
        $Deg = deg2rad(~$Angle);
        $AngleX = (($X - $BaseX)*cos($Deg)) - (($Y - $BaseY)*sin($Deg)) + $BaseX ;
        $AngleY = (($X - $BaseX)*sin($Deg)) + (($Y - $BaseY)*cos($Deg)) + $BaseY ;
        if($AngleY>intval($AngleY)){
            $AngleY = intval($AngleY)+1;
        }
        if($AngleX>intval($AngleX)){
            $AngleX = intval($AngleX)+1;
        }
        return [$AngleX,$AngleY];
    }


    /**
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
     *      正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     */
    protected static function _writeCurve()
    {
        $A = mt_rand(1, self::$imageH / 2);                  // 振幅
        $b = mt_rand(-self::$imageH / 4, self::$imageH / 4);   // Y轴方向偏移量
        $f = mt_rand(-self::$imageH / 4, self::$imageH / 4);   // X轴方向偏移量
        $T = mt_rand(self::$imageH * 1.5, self::$imageL * 2);  // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0;  // 曲线横坐标起始位置   
        $px2 = mt_rand(self::$imageL / 2, self::$imageL * 0.667);  // 曲线横坐标结束位置
        for ($px = $px1; $px <= $px2; $px = $px + 0.9) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + self::$imageH / 2;  // y = Asin(ωx+φ) + b
                $i  = (int)((self::$fontSize - 6) / 4);
                while ($i > 0) {
                    imagesetpixel(self::$_image, $px + $i, $py + $i, self::$_color);
                    //这里画像素点比imagettftext和imagestring性能要好很多
                    $i--;
                }
            }
        }
        $A   = mt_rand(1, self::$imageH / 2);                  // 振幅
        $f   = mt_rand(-self::$imageH / 4, self::$imageH / 4);   // X轴方向偏移量
        $T   = mt_rand(self::$imageH * 1.5, self::$imageL * 2);  // 周期
        $w   = (2 * M_PI) / $T;
        $b   = $py - $A * sin($w * $px + $f) - self::$imageH / 2;
        $px1 = $px2;
        $px2 = self::$imageL;
        for ($px = $px1; $px <= $px2; $px = $px + 0.9) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + self::$imageH / 2;  // y = Asin(ωx+φ) + b
                $i  = (int)((self::$fontSize - 8) / 4);
                while ($i > 0) {
                    imagesetpixel(self::$_image, $px + $i, $py + $i, self::$_color);
                    //这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出
                    //的（不用while循环）性能要好很多
                    $i--;
                }
            }
        }
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    protected static function _writeNoise()
    {
        for ($i = 0; $i < 10; $i++) {
            //杂点颜色   
            $noiseColor = imagecolorallocate(
                self::$_image,
                mt_rand(150, 225),
                mt_rand(150, 225),
                mt_rand(150, 225)
            );
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点   
                imagestring(
                    self::$_image,
                    5,
                    mt_rand(-10, self::$imageL),
                    mt_rand(-10, self::$imageH),
                    self::$codeSet[mt_rand(0, 28)], // 杂点文本为随机的字母或数字   
                    $noiseColor
                );
            }
        }
    }

}

//核心代码结束
