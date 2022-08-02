<?php

namespace Jbrathod\MathCaptcha;

use Illuminate\Session\SessionManager;

class MathCaptcha
{
    public $first_operator;
    public $second_operator;
    public $operands = array('+', '-', '*');
    protected $answer;

    /**
     * @var SessionManager
     */
    private $session;

    /**
     *
     * @param SessionManager|null $session
     */
    public function __construct(SessionManager $session = null)
    {
        $this->session = $session;
    }

    /**
     * Create captcha image
     * 
     * @param  string $page
     * @return array|mixed
     */
    public function image($page = null)
    {
        $page = ($page == '') ? 'common' : $page;

        //Set the Content Type
        header('Content-type: image/png');

        // Create Image From Existing File
        $image = imagecreatefrompng($this->background());

        // Allocate A Color For The Text
        $font_color = imagecolorallocate($image, 0, 0, 0);

        // Set Path to Font File
        $font = __DIR__ . '/../assets/fonts/gothambook.ttf';

        // Set Text to Be Printed On Image
        $text = $this->label($page);

        // Print Text On Image
        imagettftext($image, 20, 5, 30, 35, $font_color, $font, $text);

        ob_start();
        imagepng($image);
        $image_str = base64_encode(ob_get_clean());

        //clear memory
        imagedestroy($image);

        return 'data:image/png;base64,'.$image_str;

    }

    /**
     * Captcha label
     * 
     * @param  string $page
     * @return string
     */
    protected function label($page)
    {
        $this->first_operator = rand(1, 9);
        $this->second_operator = rand(1, 9);
        $this->operand = $this->operands[rand(0, count($this->operands) -1)];

        if($this->operand == '-' && $this->first_operator < $this->second_operator){
            $first = $this->second_operator;
            $second = $this->first_operator;
            $this->second_operator = $second;
            $this->first_operator = $first;
        }

        $this->storeResult($page);

        return sprintf("%d %s %d", $this->first_operator, $this->operand, $this->second_operator);
    }

    /**
     * Verify captch
     * 
     * @param  string $page
     * @param  string $value
     * @return boolean
     */
    public function verify($value, $page = null)
    {
        $page = ($page == '') ? 'common' : $page;

        $result = $this->session->get('mathcaptcha.'.$page);

        if(!is_null($result) && $value == $result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Calculate and store captcha result to session.
     * 
     * @param  string $page
     * @return void
     */
    protected function storeResult($page)
    {
        switch($this->operand){
            case '+':
            $result = $this->first_operator + $this->second_operator;
            break;

            case '-':
            $result = $this->first_operator - $this->second_operator;
            break;

            case '*':
            $result = $this->first_operator * $this->second_operator;
            break;
        }

        $this->session->put('mathcaptcha.'.$page, $result);
    }

    /**
     * Image backgrounds
     *
     * @return string
     */
    protected function background()
    {
        $backgrounds = \File::allFiles(__DIR__ . '/../assets/backgrounds');
        return $backgrounds[rand(0, count($backgrounds) - 1)];
    }

    /**
     * Reset the captcha.
     *
     * @return void
     */
    public function reset($page = null)
    {
        $page = ($page == '') ? 'common' : $page;

        $this->session->forget('mathcaptcha.'.$page);
    }
}
