<?php 


namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;

class ApplicationMailer
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendNewNotification(Application $application)
	{
		$message = new \Swift_Message(
			'Nouvelle candidature',
			'Vous avez reçu une nouvelle candidature.'
		);

		$message->addTo('contact@web-shiny.fr')
		->addFrom('admin@monsite.com');

		$this->mailer->send($message);
	}
}