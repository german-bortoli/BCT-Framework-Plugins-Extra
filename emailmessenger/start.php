<?php
    /**
     * @file
     * Email messenger.
     *
     * Provides an email support.
     *
     * @package emailmessenger
     * @license The MIT License (see LICENCE.txt), other licenses available.
     * @author Marcus Povey <marcus@marcus-povey.co.uk>
     * @copyright Marcus Povey 2011-2012
     * @link http://www.marcus-povey.co.uk
     */



     /**
      * Send an Email.
      * This class sends a message via email.
      */
     class EmailMessenger extends Messenger
     {

	 public function message($address, array $message)
	 {
	     global $CONFIG;

	     if (!is_array($address)) $address = array($address);

	     $system_address = $CONFIG->site_email;
	     if (!$system_address)
	     {
		 $breakdown = parse_url($CONFIG->wwwroot);
		 $email = "noreply@{$breakdown['host']}";
		 $system_address = "{$CONFIG->name} <$email>";
	     }

	     $from = $message['from'];
	     if (!$from) $from = $system_address;

	     $attachments = null;
	     if ($message['attachments']) $attachments = $message['attachments'];

	     return self::sendEmail($address, $message['subject'], $message['body'], array(
		     "From: $system_address",
		     "Reply-To: $from",
		     "Return-Path: $system_address"
	     ), $attachments);
	 }

	 public function messageObject(BCTObject $to, array $message)
	 {
	     // Get address
	     $address = $this->getProperty($to);
	     if (!$address) $address = $to->email; // Try a sensible default.

	     if ($address)
		 $this->message($address, $message);
	     else
		throw new MessengerException(sprintf(_echo('emailmessenger:exception:missingproperty'), 'address'));
	 }

	 /**
	  * Physically send an email to a given recipient.
	  * @param string|array $to Email address or array of addresses in RFC 2822 format.
	  * @param string $subject Subject
	  * @param string $body Message body
	  * @param array $headers Headers, e.g. from address, reply to etc.
	  * @param array $attachments An array of attachments, described as arrays('name' => 'name', 'mime' => 'mime/type', 'data' => raw data)
	  * @link http://www.faqs.org/rfcs/rfc2822
	  */
	 public static function sendEmail($to, $subject = "", $body = "", array $headers = null, array $attachments = null)
	 {
	    global $CONFIG, $version;

	    if (!is_array($to)) $to = array($to);

	    if (!$headers)
		$headers = array();

	    // Now do some sanitisation
	    foreach ($to as $email)
		if (preg_match( "/[\r\n]/", $email)) return false; // Some funny business, abort mail send

	    foreach ($headers as $header)
		if (preg_match( "/[\r\n]/", $header)) return false; // We should have no line feeds in headers at this stage (we add them later) so this probably means an injection attempt

	    $subject = explode("\n", $subject);
	    $subject = trim($subject[0], "\r\n ");

	    // Add some handy headers
	    $headers[] = "Organization: {$CONFIG->name}";
	    $headers[] = "MIME-Version: 1.0";
	    $headers[] = "X-Priority: 3";
	    $headers[] = "X-Mailer: BCT$version";


	    if ($attachments)
	    {
		$boundary = md5(time());
		$headers[] = "Content-Type: multipart/mixed; boundary=\"bct-mixed-$boundary\"";

		foreach ($attachments as $attachment)
		{
		    
		    $b64_attachment = chunk_split(base64_encode($attachment['data']));
		    
		    $attachmentblub .= <<< END
--bct-mixed-$boundary
Content-Type: {$attachment['mime']}; name="{$attachment['name']}"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

$b64_attachment
END;

		}

		$body = <<< END
--bct-mixed-$boundary
Content-Type: multipart/alternative; boundary="bct-alt-$boundary"

--bct-alt-$boundary
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

$body

--bct-alt-$boundary--

$attachmentblub
--bct-mixed-$boundary--
END;
	    }
	    else
	    {
		// No attachments so send this as plain text
		$headers[] = "Content-type: text/plain; charset=iso-8859-1";
	    }

	    return @mail(
		implode(', ',$to),
		$subject, $body,
		implode("\r\n", $headers). "\r\n"
	    );
	 }
     }

     function emailmessenger_factory($class, $hook, $parameters, $return_value)
     {
	    global $CONFIG;

	    // If we already have a messenger don't create an ew one
	    if ($return_value)
		    return $return_value;

	    // Otherwise we see if we can create a cache
	    switch ($hook)
	    {
		    case 'messenger' :
		    case 'messenger:email' :
			return new EmailMessenger();
	    }
     }

     /**
      * Initialise the email messanging subsystem, registering factories.
      */
     function emailmessenger_init()
     {
	plugin_depends_core(2011050301); // Requires messenger support

	register_factory('messenger', 'emailmessenger_factory');
	register_factory('messenger:email', 'emailmessenger_factory');
     }

     register_event('system', 'init', 'emailmessenger_init');