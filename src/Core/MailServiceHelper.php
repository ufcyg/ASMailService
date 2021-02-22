<?php declare(strict_types=1);
namespace ASMailService\Core;

use Shopware\Core\Content\MailTemplate\Service\MailService;
use Shopware\Core\Framework\Context;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/*

Contains the mail service and dispatches requested eMails

*/
class MailServiceHelper
{
    /** @var MailService $mailserviceInterface */
    private $mailservice;
    public function __construct(MailService $mailservice)
    {
        $this->mailservice = $mailservice;
    }

    public function sendMyMail($recipients ,$salesChannelID, $senderName, string $subject, string $notificationPlain, string $notificationHTML, array $filenames): void
    {
        $data = new ParameterBag();
        $data->set(
            'recipients', $recipients
        );

        foreach($filenames as $filename)
        {
            $binAttachements[] = $this->createBinAttachement($filename);
        }

        $data->set('senderName', $senderName);

        $data->set('contentHtml', $notificationHTML);
        $data->set('contentPlain', $notificationPlain);
        $data->set('subject', $subject);
        $data->set('salesChannelId', $salesChannelID);
        
        if (count($binAttachements)>=3)
            $data->set('binAttachments', $binAttachements);
        $this->mailservice->send(
            $data->all(),
            Context::createDefaultContext()
        );
    }
    private function createBinAttachement(string $filePath): ?array
    {
        $binAttachment = null;
        if($filePath === '')
            return $binAttachment;
        $binAttachment = null;
        $content = file_get_contents($filePath);
        $fileNameArray = explode('/', $filePath);
        $fileName = $fileNameArray[count($fileNameArray)-1];
        $fileNameArray = explode('.', $fileName);
        $mimeType = $fileNameArray[count($fileNameArray)-1];;
        $binAttachment['content'] = $content;
        $binAttachment['fileName'] = $fileName;
        $binAttachment['mimeType'] = $mimeType;
        return $binAttachment;
    }
}