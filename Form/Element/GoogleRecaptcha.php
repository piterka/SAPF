<?php

namespace SAPF\Form\Element;

class GoogleRecaptcha extends \SAPF\Form\Element\ElementAbstract implements \SAPF\Translate\TranslateAwareInterface
{

    use \SAPF\Translate\TranslateAwareTrait;

    const ERR_GOOGLE_RECAPTCHA = "ERR_GOOGLE_RECAPTCHA";

    protected $_errorMessage = "Weryfikacja reCaptcha niepoprawna";
    //
    protected $_privateKey;
    protected $_publicKey;
    protected $_request;
    protected $_googleRecaptchaPassed;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request = null, $publicKey = null, $privateKey = null)
    {
        $this->_request               = $request;
        $this->_publicKey             = $publicKey;
        $this->_privateKey            = $privateKey;
        $this->_googleRecaptchaPassed = false;
    }

    /**
     * Gets privateKey
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    /**
     * Gets publicKey
     * @return string
     */
    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    /**
     * Gets request
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Sets privateKey
     * @param string $privateKey
     * @return \SAPF\Form\Element\GoogleRecaptcha
     */
    public function setPrivateKey($privateKey)
    {
        $this->_privateKey = $privateKey;
        return $this;
    }

    /**
     * Sets publicKey
     * @param string $publicKey
     * @return \SAPF\Form\Element\GoogleRecaptcha
     */
    public function setPublicKey($publicKey)
    {
        $this->_publicKey = $publicKey;
        return $this;
    }

    /**
     * Sets Request
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \SAPF\Form\Element\GoogleRecaptcha
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    public function isValid()
    {
        if (!parent::isValid()) {
            return false;
        }

        return $this->_validator()->validate($this->getValue($this->_validatePostFilters))->isValid();
    }

    public function getValidatorErrors()
    {
        $errors = parent::getValidatorErrors();
        return $this->_validator()->getErrors() + $errors;
    }

    protected function _validator()
    {
        if (!$this->__validator) {
            $valid = (new \SAPF\Validator\ValidatorCallback())
                    ->setCallback([$this, '_checkAnswer'])
                    ->setMsg(self::ERR_GOOGLE_RECAPTCHA, $this->_errorMessage);
            if ($this->getTranslate()) {
                $valid->setTranslate($this->getTranslate());
            }
            $this->__validator = $valid;
        }
        return $this->__validator;
    }

    protected function _getDecorElement()
    {
        $decor = new \SAPF\Decor\DecorHtmlTag();
        $decor->setTag('div');
        $decor->setShort(false);

        $decor->addClass('g-recaptcha');
        $decor->setAttr('data-sitekey', $this->_publicKey);

        return $decor;
    }

    protected function _renderDecorElement()
    {
        $decor = $this->getElementDecor();
        return "<script src='https://www.google.com/recaptcha/api.js'></script>" . $decor->decorate("");
    }

    public function _checkAnswer()
    {
        if ($this->_googleRecaptchaPassed) {
            return false;
        }

        $data = array(
            'secret'   => $this->_privateKey,
            'remoteip' => $this->_request->getClientIp(),
            'response' => $this->_request->get("g-recaptcha-response"),
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result  = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

        $response = json_decode($result, TRUE);

        if ($response['success']) {
            $this->_googleRecaptchaPassed = true;
            return false;
        }

        return [self::ERR_GOOGLE_RECAPTCHA];
    }

}
