<?php

namespace SAPF\Util;

class SendMail
{
    /*
     * Hepler for sending mails.
     * REQUIRES: PHPMailer!
     */

    protected $_mail;
    protected $_debug        = false;
    protected $_debugDir     = false;
    protected $_replacements = [];

    /**
     * Sends debug email to programmer
     * @param type $msg Message
     * @param type $data Debug data
     * @param type $email Programmer email address
     * @return \SAPF\Util\SendMail
     */
    public static function debugReport($msg, $data = [], $email = "p.kawecki@blazingcode.net")
    {
        $mail = new SendMail();
        $mail->setFrom("noreply@" . $_SERVER['SERVER_NAME']);
        $mail->addAddress($email);
        $mail->setSubject($msg);
        $mail->setBodyHTML("Path: " . $_SERVER['REQUEST_URI'] . "<br>Event time: " . date('d.m.Y H:i') . "<br>Debug data:<br><pre>" . var_export($data, true) . "</pre>");
        return $mail;
    }

    public static function createFromConfig(array $config)
    {
        $mail = new SendMail();
        $mail->loadFromConfig($config);
        return $mail;
    }

    public function __construct($smtpParams = null)
    {
        $this->_mail           = new \PHPMailer();
        $this->_mail->Subject  = "";
        $this->_mail->AltBody  = "";
        $this->_mail->CharSet  = "UTF-8";
        $this->_mail->Encoding = "base64";
        $this->_mail->XMailer  = "SAPF";

        if ($smtpParams) {
            $this->_mail->isSMTP();
            $this->_mail->Host     = $smtpParams['host'];
            $this->_mail->Port     = $smtpParams['port'];
            $this->_mail->Username = $smtpParams['username'];
            $this->_mail->Password = $smtpParams['password'];
        }
    }

    public function loadFromConfig(array $config)
    {
        $this->setFrom("noreply@" . $_SERVER['SERVER_NAME']);

        if (isset($config['smtp'])) {
            $this->_mail->isSMTP();
            $this->_mail->Host     = isset($config['smtp']['host']) ? $config['smtp']['host'] : "localhost";
            $this->_mail->Port     = isset($config['smtp']['port']) ? intval($config['smtp']['port']) : 25;
            $this->_mail->Username = $config['smtp']['username'];
            $this->_mail->Password = $config['smtp']['password'];
        }

        if (isset($config['subject'])) {
            $this->setSubject($config['subject']);
        }

        $body = isset($config['bodyHtml']) ? $config['bodyHtml'] : $config['body'];
        if (isset($config['footer'])) {
            $body .= $config['footer'];
        }
        $this->setBodyHTML($body);

        if (isset($config['bodyHtml']) && isset($config['body'])) {
            $this->setAltBody($config['body']);
        }

        // from
        if (isset($config['from'])) {
            $this->setFrom($config['from'], $config['from_name'] ? : $config['from']);
        }

        // to
        if (isset($config['to'])) {
            if (is_array($config['to'])) {
                foreach ($config['to'] as $to) {
                    $this->AddAddress($to);
                }
            }
            else {
                $this->AddAddress($config['to']);
            }
        }
    }

    /**
     * Returns PHPMailer instance
     * @return \PHPMailer
     */
    public function getPhpMailer()
    {
        return $this->_mail;
    }

    /**
     * Set dir, where debug mode will write emails
     * @param string $debugDir
     * @return \SAPF\Util\SendMail
     */
    public function setDebugDir($debugDir)
    {
        $this->_debugDir = $debugDir;
        return $this;
    }

    /**
     * Enable/Disable debugging mode
     * @param boolean $debug
     * @return \SAPF\Util\SendMail
     */
    public function setDebug($debug = false)
    {
        $this->_debug = $debug;
        return $this;
    }

    /**
     * Check if debug is enabled or not
     * @return boolean
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * Get debug directory
     * @return type
     */
    public function getDebugDir()
    {
        return $this->_debugDir;
    }

    /**
     * Clear all attachments
     * @return \SAPF\Util\SendMail
     */
    public function clearAttachments()
    {
        $this->_mail->clearAttachments();
        return $this;
    }

    /**
     * Clear all custom headers
     * @return \SAPF\Util\SendMail
     */
    public function clearCustomHeaders()
    {
        $this->_mail->clearCustomHeaders();
        return $this;
    }

    /**
     * Clear addresses
     * @return \SAPF\Util\SendMail
     */
    public function clearAddresses()
    {
        $this->_mail->clearAddresses();
        return $this;
    }

    /**
     * Clear all recipients
     * @return \SAPF\Util\SendMail
     */
    public function clearAllRecipients()
    {
        $this->_mail->clearAllRecipients();
        return $this;
    }

    /**
     * Clear CCs
     * @return \SAPF\Util\SendMail
     */
    public function clearCCs()
    {
        $this->_mail->clearCCs();
        return $this;
    }

    /**
     * Clear reply tos
     * @return \SAPF\Util\SendMail
     */
    public function clearReplyTos()
    {
        $this->_mail->clearReplyTos();
        return $this;
    }

    /**
     * Clear BBCs
     * @return \SAPF\Util\SendMail
     */
    public function clearBCCs()
    {
        $this->_mail->clearBCCs();
        return $this;
    }

    /**
     * Add an attachment from a path on the filesystem.
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     * @throws phpmailerException
     * @return \SAPF\Util\SendMail
     */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        $this->_mail->addAttachment($path, $name, $encoding, $type, $disposition);
        return $this;
    }

    /**
     * Add a string or binary attachment (non-filesystem).
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     * @return \SAPF\Util\SendMail
     */
    public function addStringAttachment($string, $filename, $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        $this->_mail->addStringAttachment($string, $filename, $encoding, $type, $disposition);
        return $this;
    }

    /**
     * Add an embedded (inline) attachment from a file.
     * This can include images, sounds, and just about any other document type.
     * These differ from 'regular' attachments in that they are intended to be
     * displayed inline with the message, not just attached for download.
     * This is used in HTML messages that embed the images
     * the HTML refers to using the $cid value.
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment; Use this to reference
     *        the content when using an embedded image in HTML.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File MIME type.
     * @param string $disposition Disposition to use
     * @return \SAPF\Util\SendMail
     */
    public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = '', $disposition = 'inline')
    {
        $this->_mail->addEmbeddedImage($path, $cid, $name, $encoding, $type, $disposition);
        return $this;
    }

    /**
     * Add a "Reply-To" address.
     * @param string $address The email address to reply to
     * @param string $name
     * @return \SAPF\Util\SendMail
     */
    public function addReplyTo($email, $name = null)
    {
        $this->_mail->addReplyTo($email, $name ? $name : $email);
        return $this;
    }

    /**
     * Add a "CC" address.
     * @note: This function works with the SMTP mailer on win32, not with the "mail" mailer.
     * @param string $address The email address to send to
     * @param string $name
     * @return \SAPF\Util\SendMail
     */
    public function addCC($email, $name = null)
    {
        $this->_mail->addCC($email, $name ? $name : $email);
        return $this;
    }

    /**
     * Add a "BCC" address.
     * @note: This function works with the SMTP mailer on win32, not with the "mail" mailer.
     * @param string $address The email address to send to
     * @param string $name
     * @return \SAPF\Util\SendMail
     */
    public function addBCC($email, $name = null)
    {
        $this->_mail->addBCC($email, $name ? $name : $email);
        return $this;
    }

    /**
     * Add a custom header.
     * $name value can be overloaded to contain
     * both header name and value (name:value)
     * @access public
     * @param string $name Custom header name
     * @param string $value Header value
     * @return \SAPF\Util\SendMail
     */
    public function addCustomHeader($name, $value = null)
    {
        $this->_mail->addCustomHeader($name, $value);
        return $this;
    }

    /**
     * Sets XMailer name
     * @param type $mailerName
     * @return \SAPF\Util\SendMail
     */
    public function setXMailer($mailerName = "SAPF")
    {
        $this->_mail->XMailer = $mailerName;
        return $this;
    }

    /**
     * Set the From and FromName properties.
     * @param string $address
     * @param string $name
     * @param boolean $auto Whether to also set the Sender address, defaults to true
     * @throws phpmailerException
     * @return \SAPF\Util\SendMail
     */
    public function setFrom($fromEmail, $fromName = null)
    {
        $this->_mail->setFrom($fromEmail, $fromName ? $fromName : $fromEmail);
        return $this;
    }

    /**
     * Add a "To" address.
     * @param string $address The email address to send to
     * @param string $name
     * @return \SAPF\Util\SendMail
     */
    public function addAddress($email, $name = null)
    {
        $this->_mail->AddAddress($email, $name ? $name : $email);
        return $this;
    }

    /**
     * Set email subject
     * @param string $subject
     * @return \SAPF\Util\SendMail
     */
    public function setSubject($subject = "")
    {
        $this->_mail->Subject = $subject;
        return $this;
    }

    /**
     * Return email subject
     * @return string
     */
    public function getSubject()
    {
        return $this->_mail->Subject;
    }

    /**
     * Set email altBody
     * @param string $AltBody
     * @return \SAPF\Util\SendMail
     */
    public function setAltBody($AltBody = "")
    {
        $this->_mail->AltBody = $AltBody;
        return $this;
    }

    /**
     * Get email altBody
     * @return string
     */
    public function getAltBody()
    {
        return $this->_mail->AltBody;
    }

    /**
     * Set email body (html message)
     * @param string $body
     * @return \SAPF\Util\SendMail
     */
    public function setBodyHTML($body = "")
    {
        $this->_mail->msgHTML($body);
        return $this;
    }

    /**
     * Set email body
     * @param string $body
     * @return \SAPF\Util\SendMail
     */
    public function setBody($body = "")
    {
        $this->_mail->Body = $body;
        return $this;
    }

    /**
     * Get email body
     * @return string
     */
    public function getBody()
    {
        return $this->_mail->Body;
    }

    /**
     * Get all message replacements
     * @return array
     */
    public function getReplacements()
    {
        return $this->_replacements;
    }

    /**
     * Set all message replacements
     * @param array $replacements
     * @return \SAPF\Util\SendMail
     */
    public function setReplacements(array $replacements)
    {
        $this->_replacements = $replacements;
        return $this;
    }

    /**
     * Set one replacement
     * @param string $replacement
     * @param string $value
     * @return \SAPF\Util\SendMail
     */
    public function setReplacement($replacement, $value)
    {
        $this->_replacements[$replacement] = $value;
        return $this;
    }

    /**
     * Get email body with replacements
     * @return string
     */
    public function getBodyWithReplacements()
    {
        $body = $this->getBody();
        foreach ($this->_replacements as $key => $val) {
            $body = str_replace($key, $val, $body);
        }
        return $body;
    }

    /**
     * Get email body with replacements
     * @return string
     */
    public function getSubjectWithReplacements()
    {
        $subject = $this->getSubject();
        foreach ($this->_replacements as $key => $val) {
            $subject = str_replace($key, $val, $subject);
        }
        return $subject;
    }

    /**
     * Get email altBody with replacements
     * @return string
     */
    public function getAltBodyWithReplacements()
    {
        $altBody = $this->getAltBody();
        foreach ($this->_replacements as $key => $val) {
            $altBody = str_replace($key, $val, $altBody);
        }
        return $altBody;
    }

    /**
     * Send message to addresses or to file if debug enabled
     * @return boolean TRUE if sending is successful
     */
    public function send()
    {
        $mailCopy          = clone $this->_mail;
        $mailCopy->Body    = $this->getBodyWithReplacements();
        $mailCopy->Subject = $this->getSubjectWithReplacements();
        $mailCopy->AltBody = $this->getAltBodyWithReplacements();

        if ($this->_debug) {
            if (!$mailCopy->preSend()) {
                return false;
            }
            $body = $mailCopy->getSentMIMEMessage();
            $name = [];

            $name[] = str_replace(['*', '?', '|', '\\', '/', '"', ':', '>', '<', ' '], "_", $this->getSubject());
            $name[] = date("dmY");
            $name[] = date("Hi");
            $name[] = substr(sha1($body), 0, 10);

            if (file_put_contents($this->_debugDir . implode('_', $name) . '.eml', $body) > 0) {
                return true;
            }
            return false;
        }

        return $mailCopy->Send();
    }

}
